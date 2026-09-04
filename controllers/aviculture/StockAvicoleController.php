<?php

class StockAvicoleController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelAchatAvicole();
    }

    /**
     * Page Principale : État des Stocks & Disponibilités
     */
    public function stock()
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        // 1. Synthèse globale des stocks groupés par produit et catégorie de poids
        $sql = "
            SELECT 
                p.code_produit_aviculture,
                p.libelle_produit,
                p.unite_mesure,
                c.code_categorie_poids,
                c.libelle_categorie_poids,
                c.poids_min,
                c.poids_max,
                c.prix_vente_defaut,
                COALESCE(SUM(
                    CASE 
                        WHEN m.type_mouvement IN ('ENTREE_ACHAT', 'ENTREE_ABATTAGE') THEN m.quantite_pieces
                        WHEN m.type_mouvement = 'AJUSTEMENT_INVENTAIRE' AND m.quantite_pieces > 0 THEN m.quantite_pieces
                        WHEN m.type_mouvement = 'AJUSTEMENT_INVENTAIRE' AND m.quantite_pieces < 0 THEN m.quantite_pieces
                        WHEN m.type_mouvement IN ('SORTIE_VENTE_DIRECTE', 'SORTIE_DISTRIBUTION', 'PERTE_REFORME') THEN -m.quantite_pieces
                        ELSE 0
                    END
                ), 0) AS stock_pieces,
                COALESCE(SUM(
                    CASE 
                        WHEN m.type_mouvement IN ('ENTREE_ACHAT', 'ENTREE_ABATTAGE') THEN m.poids_total_kg
                        WHEN m.type_mouvement = 'AJUSTEMENT_INVENTAIRE' AND m.poids_total_kg > 0 THEN m.poids_total_kg
                        WHEN m.type_mouvement = 'AJUSTEMENT_INVENTAIRE' AND m.poids_total_kg < 0 THEN m.poids_total_kg
                        WHEN m.type_mouvement IN ('SORTIE_VENTE_DIRECTE', 'SORTIE_DISTRIBUTION', 'PERTE_REFORME') THEN -m.poids_total_kg
                        ELSE 0
                    END
                ), 0) AS stock_poids_kg,
                MAX(m.date_mouvement) AS dernier_mouvement
            FROM produits_aviculture_avicole p
            LEFT JOIN mouvements_stock_aviculture_avicole m ON p.code_produit_aviculture = m.produit_code
            LEFT JOIN categories_poids_avicole c ON m.categorie_poids_code = c.code_categorie_poids
            WHERE p.statut_produit = 'actif'
            GROUP BY p.code_produit_aviculture, c.code_categorie_poids
            ORDER BY p.libelle_produit ASC, c.poids_min ASC
        ";
        
        $stocks = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        // 2. Calcul des indicateurs KPIs
        $totPieces = 0;
        $totPoidsKg = 0;
        $valeurEstimee = 0;
        $nbAlertes = 0;

        foreach ($stocks as $s) {
            $qte = (int)$s['stock_pieces'];
            $poids = (float)$s['stock_poids_kg'];
            $prix = (float)($s['prix_vente_defaut'] ?? 0);

            $totPieces += max(0, $qte);
            $totPoidsKg += max(0, $poids);
            $valeurEstimee += (max(0, $qte) * $prix);

            if ($qte <= 5) {
                $nbAlertes++;
            }
        }

        // Listes de référence pour les modals d'ajustement
        $produits = $db->query("SELECT * FROM produits_aviculture_avicole WHERE statut_produit = 'actif' ORDER BY libelle_produit ASC")->fetchAll(PDO::FETCH_ASSOC);
        $categoriesPoids = $db->query("SELECT * FROM categories_poids_avicole WHERE statut_categorie_poids = 'actif' ORDER BY poids_min ASC")->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/aviculture/stock.php', [
            'stocks'          => $stocks,
            'totPieces'       => $totPieces,
            'totPoidsKg'      => $totPoidsKg,
            'valeurEstimee'   => $valeurEstimee,
            'nbAlertes'       => $nbAlertes,
            'produits'        => $produits,
            'categoriesPoids' => $categoriesPoids
        ]);
    }

    /**
     * Page du Journal des Mouvements de Stock
     */
    public function mouvements()
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        $dateDebut = trim($this->get('date_debut', date('Y-m-01')));
        $dateFin = trim($this->get('date_fin', date('Y-m-d')));
        $typeMvt = trim($this->get('type_mouvement', ''));

        $where = ["DATE(m.date_mouvement) BETWEEN :d_deb AND :d_fin"];
        $params = [
            ':d_deb' => $dateDebut,
            ':d_fin' => $dateFin
        ];

        if (!empty($typeMvt)) {
            $where[] = "m.type_mouvement = :tmvt";
            $params[':tmvt'] = $typeMvt;
        }

        $whereClause = implode(" AND ", $where);

        $sql = "
            SELECT m.*, 
                   p.libelle_produit, p.unite_mesure,
                   c.libelle_categorie_poids, c.poids_min, c.poids_max,
                   u.nom_user, u.prenom_user
            FROM mouvements_stock_aviculture_avicole m
            LEFT JOIN produits_aviculture_avicole p ON m.produit_code = p.code_produit_aviculture
            LEFT JOIN categories_poids_avicole c ON m.categorie_poids_code = c.code_categorie_poids
            LEFT JOIN users u ON m.user_code = u.code_user
            WHERE {$whereClause}
            ORDER BY m.date_mouvement DESC, m.id_mouvement DESC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $mouvements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/aviculture/mouvements_stock.php', [
            'mouvements' => $mouvements,
            'dateDebut'  => $dateDebut,
            'dateFin'    => $dateFin,
            'typeMvt'    => $typeMvt
        ]);
    }

    /**
     * Traitement AJAX : Ajustement Manuel / Inventaire de Stock
     */
    public function ajusterStock()
    {
        $this->requireAuth();
        $this->requirePost();

        $user_code = $_SESSION[USERS_AUTH]['code_user'] ?? '';
        $etablissement_code = $_SESSION[USERS_AUTH]['etablissement_code'] ?? '5454544456';
        $zone_code = $_SESSION[USERS_AUTH]['zone_code'] ?? null;

        $produit_code = trim($this->post('produit_code'));
        $categorie_poids_code = trim($this->post('categorie_poids_code'));
        $sens = trim($this->post('sens', 'ENTREE')); // 'ENTREE' ou 'SORTIE'
        $quantite_pieces = (int)$this->post('quantite_pieces', 0);
        $poids_total_kg = (float)$this->post('poids_total_kg', 0);
        $motif = trim($this->post('motif', 'Ajustement d\'inventaire'));

        if (empty($produit_code)) {
            $this->error("Veuillez sélectionner un produit.");
            return;
        }

        if ($quantite_pieces <= 0) {
            $this->error("La quantité doit être supérieure à 0.");
            return;
        }

        $type_mouvement = ($sens === 'SORTIE') ? 'PERTE_REFORME' : 'AJUSTEMENT_INVENTAIRE';
        $signeQte = ($sens === 'SORTIE') ? -$quantite_pieces : $quantite_pieces;
        $signePoids = ($sens === 'SORTIE') ? -$poids_total_kg : $poids_total_kg;

        $code_mouvement = 'MVT-INV-' . strtoupper(substr(uniqid(), -6));
        $ref_doc = 'INV-' . date('Ymd-His');

        try {
            $db = $this->model->getCon();
            $stmt = $db->prepare("
                INSERT INTO mouvements_stock_aviculture_avicole (
                    code_mouvement, produit_code, categorie_poids_code, type_mouvement, quantite_pieces,
                    poids_total_kg, reference_document, date_mouvement, user_code, etablissement_code, zone_code
                ) VALUES (
                    :code_mvt, :pcode, :cpcode, :tmvt, :qte,
                    :poids, :ref, NOW(), :user, :etab, :zone
                )
            ");
            $stmt->execute([
                ':code_mvt' => $code_mouvement,
                ':pcode'    => $produit_code,
                ':cpcode'   => !empty($categorie_poids_code) ? $categorie_poids_code : null,
                ':tmvt'     => $type_mouvement,
                ':qte'      => $signeQte,
                ':poids'    => $signePoids,
                ':ref'      => $ref_doc . ' (' . $motif . ')',
                ':user'     => $user_code,
                ':etab'     => $etablissement_code,
                ':zone'     => $zone_code
            ]);

            $this->json(['status' => 'success', 'message' => 'Ajustement de stock enregistré avec succès !']);
        } catch (\Exception $e) {
            $this->error("Erreur lors de l'enregistrement de l'ajustement : " . $e->getMessage());
        }
    }
}
