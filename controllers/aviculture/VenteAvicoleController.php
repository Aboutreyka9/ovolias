<?php

class VenteAvicoleController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelVenteAvicole();
    }

    public function list()
    {
        $this->requireAuth();
        $db = $this->model->getCon();
        $etab_code = $_SESSION[USERS_AUTH]['etablissement_code'] ?? '5454544456';

        // 1. Clients
        $clients = $db->query("SELECT * FROM clients_avicoles WHERE statut_client_avicole = 'actif' ORDER BY nom_client_avicole ASC")->fetchAll(PDO::FETCH_ASSOC);

        // 2. Produits & Catégories de poids pour le Panier POS (Uniquement ceux avec tarif configuré dans la grille)
        $produits = $db->query("
            SELECT DISTINCT p.* 
            FROM produits_aviculture_avicole p
            INNER JOIN grilles_tarifs_poids_avicole g ON p.code_produit_aviculture = g.produit_code
            WHERE p.statut_produit = 'actif' 
              AND g.statut_grille = 'actif'
              AND g.prix_vente > 0
            ORDER BY p.libelle_produit ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
        $categoriesPoids = $db->query("SELECT * FROM categories_poids_avicole ORDER BY poids_min ASC")->fetchAll(PDO::FETCH_ASSOC);
        $grillesTarifs = $db->query("SELECT produit_code, categorie_poids_code, prix_vente FROM grilles_tarifs_poids_avicole WHERE statut_grille = 'actif' AND prix_vente > 0")->fetchAll(PDO::FETCH_ASSOC);

        // 3. Volailles pesées & étiquetées en stock
        $etiquettesDispo = $db->query("
            SELECT p.*, prod.libelle_produit, cat.libelle_categorie_poids 
            FROM pesees_etiquettes_avicole p
            LEFT JOIN produits_aviculture_avicole prod ON p.produit_code = prod.code_produit_aviculture
            LEFT JOIN categories_poids_avicole cat ON p.categorie_poids_code = cat.code_categorie_poids
            WHERE p.statut_pesee = 'en_stock'
            ORDER BY p.id_pesee DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // 4. Statistiques du Jour (KPIs)
        $today = date('Y-m-d');
        $stmtStats = $db->prepare("
            SELECT 
                COUNT(*) AS total_ventes,
                COALESCE(SUM(montant_total_net), 0) AS ca_jour,
                COALESCE(SUM(CASE WHEN type_vente = 'comptoir_direct' THEN montant_total_net ELSE 0 END), 0) AS ca_comptoir,
                COALESCE(SUM(CASE WHEN type_vente = 'commande_livraison' AND statut_livraison = 'a_planifier' THEN 1 ELSE 0 END), 0) AS cmd_a_livrer
            FROM ventes_avicoles
            WHERE etablissement_code = :etab
              AND DATE(date_vente) = :today
              AND statut_vente != 'annulee'
        ");
        $stmtStats->execute([':etab' => $etab_code, ':today' => $today]);
        $kpis = $stmtStats->fetch(PDO::FETCH_ASSOC);

        $this->loadView('../views/aviculture/ventes_avicoles.php', [
            'clients'         => $clients,
            'produits'        => $produits,
            'categoriesPoids' => $categoriesPoids,
            'grillesTarifs'   => $grillesTarifs,
            'etiquettes'      => $etiquettesDispo,
            'kpis'            => $kpis
        ]);
    }

    public function apiList()
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        $stmt = $db->query("
            SELECT v.*, c.nom_client_avicole, c.type_client_avicole, u.nom_user, u.prenom_user
            FROM ventes_avicoles v
            LEFT JOIN clients_avicoles c ON v.client_avicole_code = c.code_client_avicole
            LEFT JOIN users u ON v.user_code = u.code_user
            ORDER BY v.id_vente_avicole DESC
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        foreach ($rows as $row) {
            $row['editId'] = $this->validator->crypter($row['id_vente_avicole']);
            $row['client_nom'] = $row['nom_client_avicole'] ?? 'Client Comptoir Direct';
            $row['agent_nom'] = trim(($row['nom_user'] ?? '') . ' ' . ($row['prenom_user'] ?? ''));
            $data[] = $row;
        }

        $this->json(['data' => $data]);
    }

    public function addVente()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $db = $this->model->getCon();

        $client_code = $this->post('client_avicole_code');
        $type_vente = $this->post('type_vente') ?? (empty($client_code) ? 'comptoir_direct' : 'commande_livraison');
        $type_reglement = $this->post('type_reglement') ?? 'comptant_especes';
        $remise_totale = (float)($this->post('montant_remise') ?? 0);
        $montant_recu = (float)($this->post('montant_recu') ?? 0);

        // Option A: Panier multi-articles de formulaire (items JSON ou tableau)
        $cartItems = $_POST['cart_items'] ?? [];
        if (is_string($cartItems)) {
            $cartItems = json_decode($cartItems, true) ?: [];
        }

        // Option B: Étiquettes pesées cochées
        $etiquettes_selectionnees = $_POST['etiquettes'] ?? [];

        if (empty($cartItems) && empty($etiquettes_selectionnees)) {
            $this->error("Veuillez ajouter au moins un article ou sélectionner une volaille pesée.");
            return;
        }

        $code_vente = 'VNT-AV-' . date('Ymd') . '-' . rand(1000, 9999);
        $user_code = $_SESSION[USERS_AUTH]['code_user'] ?? 'USR-ADMIN-001';
        $etab_code = $_SESSION[USERS_AUTH]['etablissement_code'] ?? '5454544456';
        $zone_code = $_SESSION[USERS_AUTH]['zone_user'] ?? 'DEFAULT';

        // Statut de livraison
        $statut_livraison = ($type_vente === 'comptoir_direct') ? 'non_requis' : 'a_planifier';

        $total_ht = 0;
        $items_to_insert = [];

        // Traitement Option A: Articles Panier POS
        if (!empty($cartItems)) {
            foreach ($cartItems as $cItem) {
                $pCode = $cItem['produit_code'] ?? '';
                $catCode = $cItem['categorie_poids_code'] ?? null;
                $qte = (int)($cItem['quantite'] ?? 1);
                $poidsKg = (float)($cItem['poids_total_kg'] ?? 0);
                $pu = (float)($cItem['prix_unitaire'] ?? 0);
                $mTotal = (float)($cItem['montant_total'] ?? ($qte * $pu));

                if ($qte <= 0 || $pu < 0) continue;

                $total_ht += $mTotal;
                $items_to_insert[] = [
                    'produit_code'         => $pCode,
                    'code_etiquette'       => null,
                    'categorie_poids_code' => $catCode,
                    'quantite'             => $qte,
                    'poids_total_kg'       => $poidsKg,
                    'prix_unitaire'        => $pu,
                    'montant_total'        => $mTotal
                ];
            }
        }

        // Traitement Option B: Étiquettes Pesées
        if (!empty($etiquettes_selectionnees)) {
            $inClause = implode(',', array_fill(0, count($etiquettes_selectionnees), '?'));
            $stmtP = $db->prepare("
                SELECT * FROM pesees_etiquettes_avicole 
                WHERE code_etiquette IN ($inClause) AND statut_pesee = 'en_stock'
            ");
            $stmtP->execute($etiquettes_selectionnees);
            $etiquettesDB = $stmtP->fetchAll(PDO::FETCH_ASSOC);

            foreach ($etiquettesDB as $etiq) {
                $pu = (float)$etiq['prix_unitaire_applique'];
                $total_ht += $pu;

                $items_to_insert[] = [
                    'produit_code'         => $etiq['produit_code'],
                    'code_etiquette'       => $etiq['code_etiquette'],
                    'categorie_poids_code' => $etiq['categorie_poids_code'],
                    'quantite'             => 1,
                    'poids_total_kg'       => (float)$etiq['poids_net_reel'],
                    'prix_unitaire'        => $pu,
                    'montant_total'        => $pu
                ];
            }
        }

        if (empty($items_to_insert)) {
            $this->error("Les articles sélectionnés sont invalides ou indisponibles.");
            return;
        }

        $total_net = max(0, $total_ht - $remise_totale);
        
        // Validation du montant reçu pour les règlements au comptant en espèces
        if ($type_reglement === 'comptant_especes') {
            if ($montant_recu <= 0) {
                $this->error("Veuillez saisir le montant reçu en espèces de la part du client.");
                return;
            }
            if ($montant_recu < $total_net) {
                $this->error("Le montant reçu (" . number_format($montant_recu, 0, ',', ' ') . " FCFA) est inférieur au montant net à payer (" . number_format($total_net, 0, ',', ' ') . " FCFA).");
                return;
            }
            $montant_paye = $total_net;
            $monnaie_rendue = max(0, $montant_recu - $total_net);
            $statut_reglement = 'paye';
        } elseif ($type_reglement === 'credit') {
            $montant_paye = 0;
            $monnaie_rendue = 0;
            $statut_reglement = 'impaye';
        } else {
            $montant_paye = $total_net;
            $monnaie_rendue = 0;
            $statut_reglement = 'paye';
        }

        try {
            $db->beginTransaction();

            // 1. En-tête Vente
            $stmtV = $db->prepare("
                INSERT INTO ventes_avicoles (
                    code_vente_avicole, client_avicole_code, type_vente, type_reglement, 
                    montant_total_ht, montant_remise, montant_total_net, montant_paye, 
                    montant_recu, monnaie_rendue, statut_reglement, statut_vente, 
                    statut_livraison, date_vente, user_code, etablissement_code, zone_code
                ) VALUES (
                    :code, :client, :type_v, :type_reg, 
                    :ht, :remise, :net, :paye, 
                    :recu, :monnaie, :statut_reg, 'validee', 
                    :statut_liv, NOW(), :user, :etab, :zone
                )
            ");
            $stmtV->execute([
                ':code'       => $code_vente,
                ':client'     => $client_code ?: null,
                ':type_v'     => $type_vente,
                ':type_reg'   => $type_reglement,
                ':ht'         => $total_ht,
                ':remise'     => $remise_totale,
                ':net'        => $total_net,
                ':paye'       => $montant_paye,
                ':recu'       => $montant_recu,
                ':monnaie'    => $monnaie_rendue,
                ':statut_reg' => $statut_reglement,
                ':statut_liv' => $statut_livraison,
                ':user'       => $user_code,
                ':etab'       => $etab_code,
                ':zone'       => $zone_code
            ]);

            // 2. Détails Vente & Mouvements de Stock
            $stmtDet = $db->prepare("
                INSERT INTO details_ventes_avicoles (
                    vente_code, produit_code, code_etiquette, categorie_poids_code, 
                    quantite, poids_total_kg, prix_unitaire, montant_total
                ) VALUES (
                    :vente, :prod, :etiq, :cat, 
                    :qte, :poids, :prix, :total
                )
            ");

            $stmtUpdEtiq = $db->prepare("
                UPDATE pesees_etiquettes_avicole 
                SET statut_pesee = 'vendu' 
                WHERE code_etiquette = :etiq
            ");

            $stmtMvm = $db->prepare("
                INSERT INTO mouvements_stock_aviculture_avicole (
                    code_mouvement, produit_code, categorie_poids_code, type_mouvement, 
                    quantite_pieces, poids_total_kg, reference_document, date_mouvement, 
                    user_code, etablissement_code, zone_code
                ) VALUES (
                    :mvm, :prod, :cat, 'SORTIE_VENTE_DIRECTE', 
                    :qte, :poids, :ref, NOW(), :user, :etab, :zone
                )
            ");

            foreach ($items_to_insert as $item) {
                $stmtDet->execute([
                    ':vente' => $code_vente,
                    ':prod'  => $item['produit_code'],
                    ':etiq'  => $item['code_etiquette'],
                    ':cat'   => $item['categorie_poids_code'],
                    ':qte'   => $item['quantite'],
                    ':poids' => $item['poids_total_kg'],
                    ':prix'  => $item['prix_unitaire'],
                    ':total' => $item['montant_total']
                ]);

                if (!empty($item['code_etiquette'])) {
                    $stmtUpdEtiq->execute([':etiq' => $item['code_etiquette']]);
                }

                // Déstockage immédiat uniquement pour Vente Comptoir Directe
                if ($type_vente === 'comptoir_direct') {
                    $stmtMvm->execute([
                        ':mvm'   => 'MVM-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                        ':prod'  => $item['produit_code'],
                        ':cat'   => $item['categorie_poids_code'],
                        ':qte'   => -$item['quantite'],
                        ':poids' => -$item['poids_total_kg'],
                        ':ref'   => $code_vente,
                        ':user'  => $user_code,
                        ':etab'  => $etab_code,
                        ':zone'  => $zone_code
                    ]);
                }
            }

            $db->commit();

            $racine = defined('RACINE') ? RACINE : '/ovolias/';
            $ticketUrl = $racine . 'aviculture/imprimerTicket/' . $code_vente;
            $factureUrl = $racine . 'aviculture/imprimerFacture/' . $code_vente;

            $this->success("Vente " . $code_vente . " enregistrée avec succès !", [
                'code_vente'   => $code_vente,
                'monnaie'      => $monnaie_rendue,
                'ticket_url'   => $ticketUrl,
                'facture_url'  => $factureUrl,
                'type_vente'   => $type_vente
            ]);

        } catch (Exception $e) {
            $db->rollBack();
            $this->error("Erreur d'enregistrement : " . $e->getMessage());
        }
    }

    public function imprimerTicket($param = null)
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        $codeVente = $param ?: ($_GET['code'] ?? '');
        if (empty($codeVente)) {
            die("Référence de vente manquante.");
        }

        $stmt = $db->prepare("
            SELECT v.*, c.nom_client_avicole AS nom_client, c.type_client_avicole, c.telephone_client_avicole AS telephone_client,
                   u.nom_user, u.prenom_user, e.nom_etablissement, e.adresse_etablissement, e.telephone_etablissement
            FROM ventes_avicoles v
            LEFT JOIN clients_avicoles c ON v.client_avicole_code = c.code_client_avicole
            LEFT JOIN users u ON v.user_code = u.code_user
            LEFT JOIN etablissements e ON v.etablissement_code = e.code_etablissement
            WHERE v.code_vente_avicole = :code
        ");
        $stmt->execute([':code' => $codeVente]);
        $vente = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vente) {
            die("Vente introuvable.");
        }

        $stmtItems = $db->prepare("
            SELECT d.*, p.libelle_produit, c.libelle_categorie_poids
            FROM details_ventes_avicoles d
            LEFT JOIN produits_aviculture_avicole p ON d.produit_code = p.code_produit_aviculture
            LEFT JOIN categories_poids_avicole c ON d.categorie_poids_code = c.code_categorie_poids
            WHERE d.vente_code = :code
        ");
        $stmtItems->execute([':code' => $codeVente]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../views/aviculture/ticket_caisse.php';
    }

    public function imprimerFacture($param = null)
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        $codeVente = $param ?: ($_GET['code'] ?? '');
        if (empty($codeVente)) {
            die("Référence de vente manquante.");
        }

        $stmt = $db->prepare("
            SELECT v.*, c.nom_client_avicole AS nom_client, c.type_client_avicole, c.telephone_client_avicole AS telephone_client, c.adresse_client_avicole AS adresse_client,
                   u.nom_user, u.prenom_user, e.nom_etablissement, e.adresse_etablissement, e.telephone_etablissement
            FROM ventes_avicoles v
            LEFT JOIN clients_avicoles c ON v.client_avicole_code = c.code_client_avicole
            LEFT JOIN users u ON v.user_code = u.code_user
            LEFT JOIN etablissements e ON v.etablissement_code = e.code_etablissement
            WHERE v.code_vente_avicole = :code
        ");
        $stmt->execute([':code' => $codeVente]);
        $vente = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vente) {
            die("Facture introuvable.");
        }

        $stmtItems = $db->prepare("
            SELECT d.*, p.libelle_produit, c.libelle_categorie_poids
            FROM details_ventes_avicoles d
            LEFT JOIN produits_aviculture_avicole p ON d.produit_code = p.code_produit_aviculture
            LEFT JOIN categories_poids_avicole c ON d.categorie_poids_code = c.code_categorie_poids
            WHERE d.vente_code = :code
        ");
        $stmtItems->execute([':code' => $codeVente]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../views/aviculture/facture_vente.php';
    }

    public function apiDetails()
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        $codeVente = $_GET['code'] ?? '';
        if (empty($codeVente)) {
            $this->error("Code de vente requis.");
            return;
        }

        $stmtV = $db->prepare("
            SELECT v.*, c.nom_client_avicole, c.type_client_avicole, u.nom_user, u.prenom_user
            FROM ventes_avicoles v
            LEFT JOIN clients_avicoles c ON v.client_avicole_code = c.code_client_avicole
            LEFT JOIN users u ON v.user_code = u.code_user
            WHERE v.code_vente_avicole = :code
        ");
        $stmtV->execute([':code' => $codeVente]);
        $vente = $stmtV->fetch(PDO::FETCH_ASSOC);

        $stmtItems = $db->prepare("
            SELECT d.*, p.libelle_produit, c.libelle_categorie_poids
            FROM details_ventes_avicoles d
            LEFT JOIN produits_aviculture_avicole p ON d.produit_code = p.code_produit_aviculture
            LEFT JOIN categories_poids_avicole c ON d.categorie_poids_code = c.code_categorie_poids
            WHERE d.vente_code = :code
        ");
        $stmtItems->execute([':code' => $codeVente]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        $this->json(['status' => 'success', 'vente' => $vente, 'items' => $items]);
    }
}
