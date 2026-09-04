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

        $clients = $db->query("SELECT * FROM clients_avicoles WHERE statut_client_avicole = 'actif' ORDER BY nom_client_avicole ASC")->fetchAll(PDO::FETCH_ASSOC);
        $etiquettesDispo = $db->query("
            SELECT p.*, prod.libelle_produit, cat.libelle_categorie_poids 
            FROM pesees_etiquettes_avicole p
            LEFT JOIN produits_aviculture_avicole prod ON p.produit_code = prod.code_produit_aviculture
            LEFT JOIN categories_poids_avicole cat ON p.categorie_poids_code = cat.code_categorie_poids
            WHERE p.statut_pesee = 'en_stock'
            ORDER BY p.id_pesee DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/aviculture/ventes_avicoles.php', [
            'clients' => $clients,
            'etiquettes' => $etiquettesDispo
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
            $row['client_nom'] = $row['nom_client_avicole'] ?? 'Client Comptoir';
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
        $etiquettes_selectionnees = $_POST['etiquettes'] ?? [];

        if (empty($etiquettes_selectionnees)) {
            $this->error("Veuillez sélectionner au moins une étiquette / volaille pour la vente.");
            return;
        }

        $code_vente = 'VNT-AV-' . date('Ymd') . '-' . rand(1000, 9999);
        $user_code = $_SESSION[USERS_AUTH]['code_user'] ?? 'USR-ADMIN-001';
        $etab_code = $_SESSION[USERS_AUTH]['etablissement_code'] ?? '5454544456';
        $zone_code = $_SESSION[USERS_AUTH]['zone_user'] ?? 'DEFAULT';

        // Déterminer le statut de livraison
        $statut_livraison = ($type_vente === 'comptoir_direct') ? 'non_requis' : 'a_planifier';

        // 1. Récupérer les détails des étiquettes choisies
        $inClause = implode(',', array_fill(0, count($etiquettes_selectionnees), '?'));
        $stmtP = $db->prepare("
            SELECT * FROM pesees_etiquettes_avicole 
            WHERE code_etiquette IN ($inClause) AND statut_pesee = 'en_stock'
        ");
        $stmtP->execute($etiquettes_selectionnees);
        $items = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        if (empty($items)) {
            $this->error("Aucune des étiquettes sélectionnées n'est disponible en stock.");
            return;
        }

        $montant_total = 0;
        foreach ($items as $item) {
            $montant_total += (float)$item['prix_unitaire_applique'];
        }

        // 2. Insérer l'en-tête de vente
        $stmtV = $db->prepare("
            INSERT INTO ventes_avicoles (
                code_vente_avicole, client_avicole_code, type_vente, type_reglement, 
                montant_total_ht, montant_remise, montant_total_net, montant_paye, 
                statut_vente, statut_livraison, date_vente, user_code, etablissement_code, zone_code
            ) VALUES (
                :code, :client, :type_v, :type_reg, 
                :ht, 0, :net, :paye, 
                'validee', :statut_liv, NOW(), :user, :etab, :zone
            )
        ");
        $stmtV->execute([
            ':code' => $code_vente,
            ':client' => $client_code ?: null,
            ':type_v' => $type_vente,
            ':type_reg' => $type_reglement,
            ':ht' => $montant_total,
            ':net' => $montant_total,
            ':paye' => $montant_total,
            ':statut_liv' => $statut_livraison,
            ':user' => $user_code,
            ':etab' => $etab_code,
            ':zone' => $zone_code
        ]);

        // 3. Insérer les détails et marquer les étiquettes comme vendues
        $stmtDet = $db->prepare("
            INSERT INTO details_ventes_avicoles (
                vente_code, produit_code, code_etiquette, categorie_poids_code, 
                quantite, poids_total_kg, prix_unitaire, montant_total
            ) VALUES (
                :vente, :prod, :etiq, :cat, 
                1, :poids, :prix, :total
            )
        ");

        $stmtUpd = $db->prepare("
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
                1, :poids, :ref, NOW(), :user, :etab, :zone
            )
        ");

        foreach ($items as $item) {
            $stmtDet->execute([
                ':vente' => $code_vente,
                ':prod' => $item['produit_code'],
                ':etiq' => $item['code_etiquette'],
                ':cat' => $item['categorie_poids_code'],
                ':poids' => $item['poids_net_reel'],
                ':prix' => $item['prix_unitaire_applique'],
                ':total' => $item['prix_unitaire_applique']
            ]);

            $stmtUpd->execute([':etiq' => $item['code_etiquette']]);

            // Seule la vente au comptoir directe décrémente immédiatement le stock physique global.
            // Pour les commandes à livrer, le déchargement de stock s'effectue à la confirmation du Bon de Livraison (BL).
            if ($type_vente === 'comptoir_direct') {
                $stmtMvm->execute([
                    ':mvm' => 'MVM-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                    ':prod' => $item['produit_code'],
                    ':cat' => $item['categorie_poids_code'],
                    ':poids' => -$item['poids_net_reel'],
                    ':ref' => $code_vente,
                    ':user' => $user_code,
                    ':etab' => $etab_code,
                    ':zone' => $zone_code
                ]);
            }
        }

        $this->success("Vente enregistrée avec succès ! Facture/Ticket N° {$code_vente}", [
            'code_vente' => $code_vente,
            'montant_total' => number_format($montant_total, 0, ',', ' ') . ' FCFA',
            'type_vente' => $type_vente,
            'statut_livraison' => $statut_livraison
        ]);
    }
}
