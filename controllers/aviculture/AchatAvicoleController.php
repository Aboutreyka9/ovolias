<?php

class AchatAvicoleController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelAchatAvicole();
    }

    public function list()
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        $fournisseurs = $db->query("SELECT * FROM fournisseurs_avicoles WHERE statut_fournisseur_avicole = 'actif' ORDER BY nom_fournisseur_avicole ASC")->fetchAll(PDO::FETCH_ASSOC);
        $produits = $db->query("SELECT * FROM produits_aviculture_avicole WHERE statut_produit = 'actif' ORDER BY libelle_produit ASC")->fetchAll(PDO::FETCH_ASSOC);
        $categoriesPoids = $db->query("SELECT * FROM categories_poids_avicole ORDER BY poids_min ASC")->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/aviculture/achats_avicoles.php', [
            'fournisseurs'    => $fournisseurs,
            'produits'       => $produits,
            'categoriesPoids' => $categoriesPoids
        ]);
    }

    public function apiList()
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        $stmt = $db->query("
            SELECT a.*, f.nom_fournisseur_avicole, f.categorie_intrants, u.nom_user, u.prenom_user, p.libelle_produit,
                   (SELECT COALESCE(SUM(d.quantite), 0) FROM details_achats_avicoles d WHERE d.achat_code = a.code_achat_avicole) AS quantite_totale
            FROM achats_avicoles a
            LEFT JOIN fournisseurs_avicoles f ON a.fournisseur_avicole_code = f.code_fournisseur_avicole
            LEFT JOIN users u ON a.user_code = u.code_user
            LEFT JOIN produits_aviculture_avicole p ON a.categorie_intrant = p.code_produit_aviculture
            ORDER BY a.id_achat_avicole DESC
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        foreach ($rows as $row) {
            $row['editId'] = $this->validator->crypter($row['id_achat_avicole']);
            $row['fournisseur_nom'] = $row['nom_fournisseur_avicole'] ?? 'Fournisseur Général';
            $row['agent_nom'] = trim(($row['nom_user'] ?? '') . ' ' . ($row['prenom_user'] ?? ''));
            $row['produit_libelle'] = $row['libelle_produit'] ?? $row['categorie_intrant'];
            $row['quantite_totale'] = (float)($row['quantite_totale'] ?? 0);
            $data[] = $row;
        }

        $this->json(['data' => $data]);
    }

    public function apiDetails()
    {
        $this->requireAuth();
        $code = $this->get('code') ?? $this->post('code');
        if (empty($code)) {
            $this->error("Code d'achat manquant");
            return;
        }

        $db = $this->model->getCon();
        $stmtA = $db->prepare("
            SELECT a.*, f.nom_fournisseur_avicole, f.telephone_fournisseur_avicole, f.adresse_fournisseur_avicole,
                   u.nom_user, u.prenom_user
            FROM achats_avicoles a
            LEFT JOIN fournisseurs_avicoles f ON a.fournisseur_avicole_code = f.code_fournisseur_avicole
            LEFT JOIN users u ON a.user_code = u.code_user
            WHERE a.code_achat_avicole = :code OR a.id_achat_avicole = :code
        ");
        $stmtA->execute([':code' => $code]);
        $achat = $stmtA->fetch(PDO::FETCH_ASSOC);

        if (!$achat) {
            $this->error("Achat introuvable");
            return;
        }

        $stmtD = $db->prepare("
            SELECT * FROM details_achats_avicoles WHERE achat_code = :code ORDER BY id_detail_achat ASC
        ");
        $stmtD->execute([':code' => $achat['code_achat_avicole']]);
        $details = $stmtD->fetchAll(PDO::FETCH_ASSOC);

        $achat['fournisseur_nom'] = $achat['nom_fournisseur_avicole'] ?? 'Fournisseur Général';
        $achat['agent_nom'] = trim(($achat['nom_user'] ?? '') . ' ' . ($achat['prenom_user'] ?? ''));

        $this->json([
            'status'  => 'success',
            'achat'   => $achat,
            'details' => $details
        ]);
    }

    public function detailAchat($param = null)
    {
        $this->requireAuth();
        $code = $param ?? $this->get('code') ?? $this->get('id');

        if (empty($code)) {
            header('Location: ' . RACINE . 'aviculture/achats');
            exit();
        }

        try {
            $decryptedId = $this->validator->decrypter($code);
            if (!empty($decryptedId)) {
                $code = $decryptedId;
            }
        } catch (Exception $e) {
            // Leave code as original if not encrypted
        }

        $db = $this->model->getCon();
        $stmtA = $db->prepare("
            SELECT a.*, f.nom_fournisseur_avicole, f.telephone_fournisseur_avicole, f.adresse_fournisseur_avicole,
                   u.nom_user, u.prenom_user
            FROM achats_avicoles a
            LEFT JOIN fournisseurs_avicoles f ON a.fournisseur_avicole_code = f.code_fournisseur_avicole
            LEFT JOIN users u ON a.user_code = u.code_user
            WHERE a.code_achat_avicole = :code OR a.id_achat_avicole = :code
        ");
        $stmtA->execute([':code' => $code]);
        $achat = $stmtA->fetch(PDO::FETCH_ASSOC);

        if (!$achat) {
            header('Location: ' . RACINE . 'aviculture/achats');
            exit();
        }

        $stmtD = $db->prepare("
            SELECT * FROM details_achats_avicoles WHERE achat_code = :code ORDER BY id_detail_achat ASC
        ");
        $stmtD->execute([':code' => $achat['code_achat_avicole']]);
        $details = $stmtD->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $achat['fournisseur_nom'] = $achat['nom_fournisseur_avicole'] ?? 'Fournisseur Général';
        $achat['agent_nom'] = trim(($achat['nom_user'] ?? '') . ' ' . ($achat['prenom_user'] ?? ''));

        $this->loadView('../views/aviculture/detail_achat.php', [
            'achat'   => $achat,
            'details' => $details
        ]);
    }

    /**
     * Génère un numéro de facture fournisseur unique au format FACT-YYYY-XXXX (3 à 5 chiffres)
     */
    public function generateUniqueFactureNumber()
    {
        $db = $this->model->getCon();
        $year = date('Y');
        $maxAttempts = 100;
        $attempt = 0;

        do {
            $attempt++;
            $rand = rand(100, 99999);
            $num = "FACT-{$year}-{$rand}";

            $stmt = $db->prepare("SELECT COUNT(*) FROM achats_avicoles WHERE numero_facture_fournisseur = :num");
            $stmt->execute([':num' => $num]);
            $exists = ($stmt->fetchColumn() > 0);
        } while ($exists && $attempt < $maxAttempts);

        return $num;
    }

    public function genererNumFacture()
    {
        $this->requireAuth();
        $num = $this->generateUniqueFactureNumber();
        $this->json(['status' => 'success', 'numero_facture' => $num]);
    }

    public function addAchat()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $db = $this->model->getCon();

        $fournisseur_code = $this->post('fournisseur_avicole_code');
        $num_facture      = trim($this->post('numero_facture_fournisseur') ?? '');
        $statut_reg       = $this->post('statut_reglement') ?? 'paye';
        $articles_raw     = $_POST['articles'] ?? [];

        if (empty($fournisseur_code)) {
            $this->error("Veuillez sélectionner un fournisseur.");
            return;
        }

        if (!empty($num_facture)) {
            $stmtCheck = $db->prepare("SELECT COUNT(*) FROM achats_avicoles WHERE numero_facture_fournisseur = :num");
            $stmtCheck->execute([':num' => $num_facture]);
            if ($stmtCheck->fetchColumn() > 0) {
                $this->error("Le numéro de facture '{$num_facture}' existe déjà en base de données. Veuillez en générer un nouveau.");
                return;
            }
        } else {
            $num_facture = $this->generateUniqueFactureNumber();
        }

        if (empty($articles_raw) || !is_array($articles_raw)) {
            $this->error("Veuillez ajouter au moins un produit à la commande.");
            return;
        }

        $valid_items = [];
        $grand_total = 0;

        foreach ($articles_raw as $item) {
            $prod_code = trim($item['produit_code'] ?? '');
            $qte       = (float)($item['quantite'] ?? 0);
            $pu        = (float)($item['prix_unitaire'] ?? 0);
            $unite     = trim($item['unite_mesure'] ?? 'Pièces');

            if (!empty($prod_code) && $qte > 0 && $pu > 0) {
                $stmtP = $db->prepare("SELECT libelle_produit FROM produits_aviculture_avicole WHERE code_produit_aviculture = :code");
                $stmtP->execute([':code' => $prod_code]);
                $pRow = $stmtP->fetch(PDO::FETCH_ASSOC);
                $libelle = $pRow ? $pRow['libelle_produit'] : $prod_code;

                $stot = $qte * $pu;
                $grand_total += $stot;

                $valid_items[] = [
                    'produit_code' => $prod_code,
                    'libelle'      => $libelle,
                    'qte'          => $qte,
                    'unite'        => $unite,
                    'pu'           => $pu,
                    'total'        => $stot
                ];
            }
        }

        if (empty($valid_items)) {
            $this->error("Veuillez remplir correctement au moins une ligne produit (Quantité > 0 et Prix > 0).");
            return;
        }

        $code_achat = 'ACH-AV-' . date('Ymd') . '-' . rand(1000, 9999);
        $first_cat  = $valid_items[0]['produit_code'];

        $user_code = $_SESSION[USERS_AUTH]['code_user'] ?? 'USR-ADMIN-001';
        $etab_code = $_SESSION[USERS_AUTH]['etablissement_code'] ?? '5454544456';
        $zone_code = $_SESSION[USERS_AUTH]['zone_user'] ?? 'DEFAULT';

        $montant_paye = ($statut_reg === 'paye') ? $grand_total : 0;

        $db->beginTransaction();

        try {
            // 1. Insérer l'en-tête d'achat
            $stmtA = $db->prepare("
                INSERT INTO achats_avicoles (
                    code_achat_avicole, fournisseur_avicole_code, numero_facture_fournisseur, 
                    categorie_intrant, montant_total, montant_paye, statut_reglement, 
                    date_achat, user_code, etablissement_code, zone_code
                ) VALUES (
                    :code, :frs, :num_fac, 
                    :cat, :tot, :paye, :statut_reg, 
                    NOW(), :user, :etab, :zone
                )
            ");
            $stmtA->execute([
                ':code'       => $code_achat,
                ':frs'        => $fournisseur_code,
                ':num_fac'    => $num_facture,
                ':cat'        => $first_cat,
                ':tot'        => $grand_total,
                ':paye'       => $montant_paye,
                ':statut_reg' => $statut_reg,
                ':user'       => $user_code,
                ':etab'       => $etab_code,
                ':zone'       => $zone_code
            ]);

            // 2. Insérer les lignes de détail et mouvements de stock
            $stmtDet = $db->prepare("
                INSERT INTO details_achats_avicoles (
                    achat_code, libelle_article_intrant, quantite, 
                    unite_mesure, prix_unitaire, montant_total
                ) VALUES (
                    :code, :article, :qte, 
                    :unite, :pu, :tot
                )
            ");

            $stmtStock = $db->prepare("
                INSERT INTO mouvements_stock_aviculture_avicole (
                    code_mouvement, produit_code, type_mouvement, quantite_pieces, 
                    poids_total_kg, reference_document, date_mouvement, user_code, etablissement_code, zone_code
                ) VALUES (
                    :code_mvt, :pcode, 'ENTREE_ACHAT', :qte_p, 
                    :poids, :ref_doc, NOW(), :user, :etab, :zone
                )
            ");

            foreach ($valid_items as $item) {
                $stmtDet->execute([
                    ':code'    => $code_achat,
                    ':article' => $item['libelle'],
                    ':qte'     => $item['qte'],
                    ':unite'   => $item['unite'],
                    ':pu'      => $item['pu'],
                    ':tot'     => $item['total']
                ]);

                // Création du mouvement de stock ENTREE_ACHAT
                $code_mvt = 'MVT-ACH-' . strtoupper(substr(uniqid(), -6));
                $stmtStock->execute([
                    ':code_mvt' => $code_mvt,
                    ':pcode'    => $item['produit_code'],
                    ':qte_p'    => (int)$item['qte'],
                    ':poids'    => (float)$item['qte'],
                    ':ref_doc'  => $code_achat,
                    ':user'     => $user_code,
                    ':etab'     => $etab_code,
                    ':zone'     => $zone_code
                ]);
            }

            $db->commit();

            $nb_items = count($valid_items);
            $this->success("Commande d'achat d'intrants ({$nb_items} produit(s)) enregistrée avec succès ! Référence : {$code_achat}", [
                'code_achat'    => $code_achat,
                'montant_total' => number_format($grand_total, 0, ',', ' ') . ' FCFA'
            ]);

        } catch (\Exception $e) {
            $db->rollBack();
            $this->error("Erreur lors de l'enregistrement du bon d'achat : " . $e->getMessage());
        }
    }
}
