<?php

class LivraisonAvicoleController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelAchatAvicole();
    }

    private function getEtablissementCode()
    {
        return $_SESSION[USERS_AUTH]['etablissement_code'] ?? '5454544456';
    }

    private function getUserCode()
    {
        return $_SESSION[USERS_AUTH]['code_user'] ?? 'USR-ADMIN-001';
    }

    /**
     * Dashboard / Liste des Expéditions & Planning des Livraisons
     */
    public function list()
    {
        $this->requireAuth();
        $db = $this->model->getCon();
        $etabCode = $this->getEtablissementCode();

        // 1. Commandes à planifier (statut_livraison = 'a_planifier')
        $stmtPlanif = $db->prepare("
            SELECT v.*, c.nom_client_avicole AS nom_client, c.type_client_avicole, c.telephone_client_avicole AS telephone_client, c.adresse_client_avicole AS adresse_client, z.libelle_zone
            FROM ventes_avicoles v
            LEFT JOIN clients_avicoles c ON v.client_avicole_code = c.code_client_avicole
            LEFT JOIN zones z ON v.zone_code = z.code_zone
            WHERE v.etablissement_code = :etab
              AND v.statut_livraison = 'a_planifier'
              AND v.statut_vente != 'annulee'
            ORDER BY v.date_vente DESC
        ");
        $stmtPlanif->execute([':etab' => $etabCode]);
        $commandesAPlanifier = $stmtPlanif->fetchAll(PDO::FETCH_ASSOC);

        // 2. Livraisons en cours / planifiées
        $stmtEnCours = $db->prepare("
            SELECT l.*, v.code_vente_avicole, v.montant_total_net, v.statut_reglement,
                   c.nom_client_avicole AS nom_client, c.telephone_client_avicole AS telephone_client, c.adresse_client_avicole AS adresse_client,
                   u.nom_user, u.prenom_user, u.telephone_user,
                   veh.libelle_vehicule, veh.immatriculation
            FROM livraisons_avicoles l
            INNER JOIN ventes_avicoles v ON l.vente_code = v.code_vente_avicole
            LEFT JOIN clients_avicoles c ON v.client_avicole_code = c.code_client_avicole
            LEFT JOIN users u ON l.livreur_user_code = u.code_user
            LEFT JOIN vehicules_livraison_avicole veh ON l.vehicule_code = veh.code_vehicule
            WHERE v.etablissement_code = :etab
              AND l.statut_livraison IN ('planifiee', 'en_cours')
            ORDER BY l.date_planification ASC
        ");
        $stmtEnCours->execute([':etab' => $etabCode]);
        $livraisonsEnCours = $stmtEnCours->fetchAll(PDO::FETCH_ASSOC);

        // 3. Historique des livraisons livrées
        $stmtLivrees = $db->prepare("
            SELECT l.*, v.code_vente_avicole, v.montant_total_net,
                   c.nom_client_avicole AS nom_client,
                   u.nom_user, u.prenom_user,
                   veh.libelle_vehicule, veh.immatriculation
            FROM livraisons_avicoles l
            INNER JOIN ventes_avicoles v ON l.vente_code = v.code_vente_avicole
            LEFT JOIN clients_avicoles c ON v.client_avicole_code = c.code_client_avicole
            LEFT JOIN users u ON l.livreur_user_code = u.code_user
            LEFT JOIN vehicules_livraison_avicole veh ON l.vehicule_code = veh.code_vehicule
            WHERE v.etablissement_code = :etab
              AND l.statut_livraison = 'livree'
            ORDER BY l.date_livraison_effective DESC
            LIMIT 50
        ");
        $stmtLivrees->execute([':etab' => $etabCode]);
        $livraisonsLivrees = $stmtLivrees->fetchAll(PDO::FETCH_ASSOC);

        // 4. Liste des livreurs / chauffeurs éligibles (Users actifs)
        $stmtLivreurs = $db->query("
            SELECT code_user, nom_user, prenom_user, telephone_user
            FROM users
            WHERE statut_user = 'actif'
            ORDER BY nom_user ASC, prenom_user ASC
        ");
        $livreurs = $stmtLivreurs->fetchAll(PDO::FETCH_ASSOC);

        // 5. Liste des véhicules disponibles
        $stmtVehicules = $db->query("
            SELECT * FROM vehicules_livraison_avicole
            WHERE statut IN ('disponible', 'en_livraison')
            ORDER BY libelle_vehicule ASC
        ");
        $vehicules = $stmtVehicules->fetchAll(PDO::FETCH_ASSOC);

        // KPIs
        $nbAPlanifier = count($commandesAPlanifier);
        $nbEnCours = count($livraisonsEnCours);
        $nbLivrees = count($livraisonsLivrees);

        $this->loadView('../views/aviculture/livraisons.php', [
            'commandesAPlanifier' => $commandesAPlanifier,
            'livraisonsEnCours' => $livraisonsEnCours,
            'livraisonsLivrees' => $livraisonsLivrees,
            'livreurs' => $livreurs,
            'vehicules' => $vehicules,
            'nbAPlanifier' => $nbAPlanifier,
            'nbEnCours' => $nbEnCours,
            'nbLivrees' => $nbLivrees
        ]);
    }

    /**
     * Planifier une livraison (Affectation livreur + véhicule + Bon de Livraison)
     */
    public function planifier()
    {
        $this->requireAuth();
        $this->requirePost(true);
        $db = $this->model->getCon();

        $venteCode = trim($this->post('vente_code'));
        $livreurCode = trim($this->post('livreur_user_code'));
        $vehiculeCode = trim($this->post('vehicule_code'));
        $datePlanif = trim($this->post('date_planification'));
        $notes = trim($this->post('notes_livraison'));

        if (empty($venteCode) || empty($livreurCode) || empty($datePlanif)) {
            $this->error("Veuillez remplir tous les champs obligatoires (Commande, Livreur, Date).");
            return;
        }

        try {
            $db->beginTransaction();

            // Vérifier la vente
            $stmtVente = $db->prepare("SELECT * FROM ventes_avicoles WHERE code_vente_avicole = :code FOR UPDATE");
            $stmtVente->execute([':code' => $venteCode]);
            $vente = $stmtVente->fetch(PDO::FETCH_ASSOC);

            if (!$vente) {
                $db->rollBack();
                $this->error("Commande introuvable.");
                return;
            }

            // Générer le code du Bon de Livraison (BL-YYYYMMDD-XXXX)
            $datePrefix = date('Ymd');
            $stmtCount = $db->query("SELECT COUNT(*) FROM livraisons_avicoles WHERE DATE(created_at) = CURDATE()");
            $countToday = (int)$stmtCount->fetchColumn() + 1;
            $codeBL = 'BL-' . $datePrefix . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            // Insérer la livraison
            $stmtIns = $db->prepare("
                INSERT INTO livraisons_avicoles (
                    code_livraison, vente_code, livreur_user_code, vehicule_code,
                    date_planification, statut_livraison, notes_livraison, created_by
                ) VALUES (
                    :bl, :vente, :livreur, :vehicule,
                    :date_p, 'planifiee', :notes, :user
                )
            ");
            $stmtIns->execute([
                ':bl' => $codeBL,
                ':vente' => $venteCode,
                ':livreur' => $livreurCode,
                ':vehicule' => !empty($vehiculeCode) ? $vehiculeCode : null,
                ':date_p' => date('Y-m-d H:i:s', strtotime($datePlanif)),
                ':notes' => $notes,
                ':user' => $this->getUserCode()
            ]);

            // Mettre à jour le statut de livraison de la vente
            $stmtUpVente = $db->prepare("UPDATE ventes_avicoles SET statut_livraison = 'planifiee' WHERE code_vente_avicole = :code");
            $stmtUpVente->execute([':code' => $venteCode]);

            // Si un véhicule est sélectionné, mettre son statut à 'en_livraison'
            if (!empty($vehiculeCode)) {
                $stmtUpVeh = $db->prepare("UPDATE vehicules_livraison_avicole SET statut = 'en_livraison' WHERE code_vehicule = :code");
                $stmtUpVeh->execute([':code' => $vehiculeCode]);
            }

            $db->commit();

            $this->json([
                'status' => 'success',
                'message' => 'Livraison planifiée et Bon de Livraison ' . $codeBL . ' généré avec succès !',
                'code_livraison' => $codeBL
            ]);
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $this->error("Erreur serveur: " . $e->getMessage());
        }
    }

    /**
     * Valider la livraison effective (Confirmation déchargement stock)
     */
    public function validerLivraison()
    {
        $this->requireAuth();
        $this->requirePost(true);
        $db = $this->model->getCon();

        $codeBL = trim($this->post('code_livraison'));
        $nomReceptionnaire = trim($this->post('nom_receptionnaire'));
        $notes = trim($this->post('notes_livraison'));

        if (empty($codeBL)) {
            $this->error("Code livraison manquant.");
            return;
        }

        try {
            $db->beginTransaction();

            // Récupérer la livraison
            $stmtLiv = $db->prepare("SELECT * FROM livraisons_avicoles WHERE code_livraison = :code FOR UPDATE");
            $stmtLiv->execute([':code' => $codeBL]);
            $livraison = $stmtLiv->fetch(PDO::FETCH_ASSOC);

            if (!$livraison) {
                $db->rollBack();
                $this->error("Livraison introuvable.");
                return;
            }

            if ($livraison['statut_livraison'] === 'livree') {
                $db->rollBack();
                $this->error("Cette livraison a déjà été validée.");
                return;
            }

            $venteCode = $livraison['vente_code'];

            // Récupérer les détails de la vente pour déstocker
            $stmtDetails = $db->prepare("SELECT * FROM details_ventes_avicoles WHERE vente_code = :code");
            $stmtDetails->execute([':code' => $venteCode]);
            $details = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

            // Générer le mouvement de stock pour chaque article (SORTIE_DISTRIBUTION)
            $stmtStock = $db->prepare("
                INSERT INTO mouvements_stock_aviculture_avicole (
                    code_mouvement, produit_code, categorie_poids_code, type_mouvement,
                    quantite_pieces, poids_total_kg, reference_document, user_code, etablissement_code, notes
                ) VALUES (
                    :code_mvt, :produit, :cat_poids, 'SORTIE_DISTRIBUTION',
                    :qte, :poids, :ref, :user, :etab, :notes
                )
            ");

            foreach ($details as $d) {
                $mvtCode = 'MVT-DIST-' . date('YmdHis') . '-' . rand(100, 999);
                $qteSortie = -abs((int)$d['quantite']);
                $poidsSortie = -abs((float)$d['poids_total_kg']);

                $stmtStock->execute([
                    ':code_mvt' => $mvtCode,
                    ':produit' => $d['produit_code'],
                    ':cat_poids' => $d['categorie_poids_code'] ?? null,
                    ':qte' => $qteSortie,
                    ':poids' => $poidsSortie,
                    ':ref' => $codeBL,
                    ':user' => $this->getUserCode(),
                    ':etab' => $this->getEtablissementCode(),
                    ':notes' => 'Livraison effective BL ' . $codeBL . ' pour la vente ' . $venteCode
                ]);
            }

            // Mettre à jour la livraison
            $stmtUpLiv = $db->prepare("
                UPDATE livraisons_avicoles
                SET statut_livraison = 'livree',
                    date_livraison_effective = NOW(),
                    nom_receptionnaire = :rec,
                    notes_livraison = CONCAT(IFNULL(notes_livraison, ''), ' | ', :notes)
                WHERE code_livraison = :code
            ");
            $stmtUpLiv->execute([
                ':rec' => $nomReceptionnaire,
                ':notes' => $notes,
                ':code' => $codeBL
            ]);

            // Mettre à jour la vente
            $stmtUpV = $db->prepare("UPDATE ventes_avicoles SET statut_livraison = 'livree' WHERE code_vente_avicole = :code");
            $stmtUpV->execute([':code' => $venteCode]);

            // Libérer le véhicule si applicable
            if (!empty($livraison['vehicule_code'])) {
                $stmtUpVeh = $db->prepare("UPDATE vehicules_livraison_avicole SET statut = 'disponible' WHERE code_vehicule = :code");
                $stmtUpVeh->execute([':code' => $livraison['vehicule_code']]);
            }

            $db->commit();

            $this->json([
                'status' => 'success',
                'message' => 'Livraison validée et stock déchargé avec succès !'
            ]);
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $this->error("Erreur lors de la validation: " . $e->getMessage());
        }
    }

    /**
     * Impression A4 Officielle du Bon de Livraison (BL)
     */
    public function imprimerBL($param = null)
    {
        $this->requireAuth();
        $db = $this->model->getCon();
        $code = $param ?? $this->get('code', '');

        if (empty($code)) {
            echo "Référence de livraison introuvable.";
            exit();
        }

        // Récupérer les données complètes de la livraison
        $stmtBL = $db->prepare("
            SELECT l.*, v.code_vente_avicole, v.date_vente, v.montant_total_net, v.type_reglement, v.statut_reglement,
                   c.nom_client_avicole AS nom_client, c.type_client_avicole, c.telephone_client_avicole AS telephone_client, c.adresse_client_avicole AS adresse_client, c.ville_client_avicole AS ville_client,
                   u.nom_user AS livreur_nom, u.prenom_user AS livreur_prenom, u.telephone_user AS livreur_tel,
                   veh.libelle_vehicule, veh.immatriculation,
                   e.nom_etablissement, e.adresse_etablissement, e.telephone_etablissement, e.email_etablissement
            FROM livraisons_avicoles l
            INNER JOIN ventes_avicoles v ON l.vente_code = v.code_vente_avicole
            LEFT JOIN clients_avicoles c ON v.client_avicole_code = c.code_client_avicole
            LEFT JOIN users u ON l.livreur_user_code = u.code_user
            LEFT JOIN vehicules_livraison_avicole veh ON l.vehicule_code = veh.code_vehicule
            LEFT JOIN etablissements e ON v.etablissement_code = e.code_etablissement
            WHERE l.code_livraison = :code OR l.vente_code = :code
            LIMIT 1
        ");
        $stmtBL->execute([':code' => $code]);
        $bl = $stmtBL->fetch(PDO::FETCH_ASSOC);

        if (!$bl) {
            echo "Bon de Livraison non trouvé pour la référence " . htmlspecialchars($code);
            exit();
        }

        // Récupérer les articles
        $stmtItems = $db->prepare("
            SELECT d.*, p.libelle_produit, cp.libelle_categorie_poids
            FROM details_ventes_avicoles d
            LEFT JOIN produits_aviculture_avicole p ON d.produit_code = p.code_produit_aviculture
            LEFT JOIN grilles_tarifs_poids_avicole cp ON d.categorie_poids_code = cp.code_categorie_poids
            WHERE d.vente_code = :vente
        ");
        $stmtItems->execute([':vente' => $bl['vente_code']]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/aviculture/bon_livraison.php', [
            'bl' => $bl,
            'items' => $items
        ]);
    }

    /**
     * Gestion de la Flotte de Véhicules de Livraison
     */
    public function vehicules()
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        $stmt = $db->query("SELECT * FROM vehicules_livraison_avicole ORDER BY created_at DESC");
        $vehicules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/aviculture/vehicules.php', [
            'vehicules' => $vehicules
        ]);
    }

    /**
     * Ajouter un véhicule
     */
    public function addVehicule()
    {
        $this->requireAuth();
        $this->requirePost(true);
        $db = $this->model->getCon();

        $immat = trim($this->post('immatriculation'));
        $libelle = trim($this->post('libelle_vehicule'));
        $capacite = (float)$this->post('capacite_max_kg', 0);

        if (empty($immat) || empty($libelle)) {
            $this->error("L'immatriculation et le nom du véhicule sont obligatoires.");
            return;
        }

        try {
            $codeVeh = 'VEH-' . strtoupper(substr(md5(uniqid()), 0, 6));
            $stmt = $db->prepare("
                INSERT INTO vehicules_livraison_avicole (code_vehicule, immatriculation, libelle_vehicule, capacite_max_kg)
                VALUES (:code, :immat, :libelle, :capa)
            ");
            $stmt->execute([
                ':code' => $codeVeh,
                ':immat' => $immat,
                ':libelle' => $libelle,
                ':capa' => $capacite
            ]);

            $this->json(['status' => 'success', 'message' => 'Véhicule enregistré avec succès !']);
        } catch (Exception $e) {
            $this->error("Erreur: " . $e->getMessage());
        }
    }
}
