<?php

class AvicultureController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelAviculture();
    }

    /**
     * Vue des catégories de poids et grilles tarifaires OVOLIA
     */
    public function categoriesPoids()
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        $stmt = $db->query("SELECT * FROM categories_poids_avicole ORDER BY poids_min ASC");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmtP = $db->query("
            SELECT g.*, p.libelle_produit, c.libelle_categorie_poids 
            FROM grilles_tarifs_poids_avicole g
            JOIN produits_aviculture_avicole p ON g.produit_code = p.code_produit_aviculture
            JOIN categories_poids_avicole c ON g.categorie_poids_code = c.code_categorie_poids
            ORDER BY p.libelle_produit ASC, g.poids_min ASC
        ");
        $grilles = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/aviculture/categories_poids.php', [
            'categories' => $categories,
            'grilles' => $grilles
        ]);
    }

    /**
     * Vue du registre des pesées et étiquettes
     */
    public function pesees()
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        $produits = $db->query("SELECT * FROM produits_aviculture_avicole WHERE statut_produit = 'actif' ORDER BY libelle_produit ASC")->fetchAll(PDO::FETCH_ASSOC);
        $categories = $db->query("SELECT * FROM categories_poids_avicole WHERE statut_categorie_poids = 'actif' ORDER BY poids_min ASC")->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/aviculture/pesees.php', [
            'produits' => $produits,
            'categories' => $categories
        ]);
    }

    /**
     * API Liste JSON des pesées pour DataTables
     */
    public function apiListPesees()
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        $sql = "
            SELECT 
                p.*,
                prod.libelle_produit,
                cat.libelle_categorie_poids,
                u.nom_user, u.prenom_user
            FROM pesees_etiquettes_avicole p
            LEFT JOIN produits_aviculture_avicole prod ON p.produit_code = prod.code_produit_aviculture
            LEFT JOIN categories_poids_avicole cat ON p.categorie_poids_code = cat.code_categorie_poids
            LEFT JOIN users u ON p.user_code = u.code_user
            ORDER BY p.id_pesee DESC
        ";

        $stmt = $db->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        foreach ($rows as $row) {
            $idCrypte = $this->validator->crypter($row['id_pesee']);
            $row['editId'] = $idCrypte;
            $row['agent_nom'] = trim(($row['nom_user'] ?? '') . ' ' . ($row['prenom_user'] ?? ''));
            $data[] = $row;
        }

        $this->json(['data' => $data]);
    }

    /**
     * Ajout d'une pesée avec détermination automatique de la catégorie selon le poids net
     */
    public function addPesee()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $db = $this->model->getCon();

        $produit_code = $this->post('produit_code');
        $poids_net = (float)$this->post('poids_net_reel');
        $numero_lot = trim($this->post('numero_lot') ?? '');
        $dlc = $this->post('date_limite_consommation') ?: date('Y-m-d', strtotime('+3 days'));

        if (empty($produit_code) || $poids_net <= 0) {
            $this->error("Veuillez sélectionner un produit et saisir un poids net valide.");
            return;
        }

        // 1. Déterminer la catégorie de poids d'après le poids net mesuré
        $stmtCat = $db->prepare("
            SELECT code_categorie_poids, libelle_categorie_poids, prix_vente_defaut 
            FROM categories_poids_avicole 
            WHERE :poids BETWEEN poids_min AND poids_max
            LIMIT 1
        ");
        $stmtCat->execute([':poids' => $poids_net]);
        $catMatch = $stmtCat->fetch(PDO::FETCH_ASSOC);

        if (!$catMatch) {
            // Chercher par proximité minimale/maximale
            $stmtCatNear = $db->prepare("
                SELECT code_categorie_poids, libelle_categorie_poids, prix_vente_defaut 
                FROM categories_poids_avicole 
                ORDER BY ABS(poids_min - :poids) ASC 
                LIMIT 1
            ");
            $stmtCatNear->execute([':poids' => $poids_net]);
            $catMatch = $stmtCatNear->fetch(PDO::FETCH_ASSOC);
        }

        $categorie_code = $catMatch['code_categorie_poids'] ?? 'CATP-ESSENTIEL';

        // 2. Chercher le tarif dans la grille pour ce produit et cette catégorie
        $stmtTarif = $db->prepare("
            SELECT prix_vente FROM grilles_tarifs_poids_avicole 
            WHERE produit_code = :prod AND categorie_poids_code = :cat AND statut_grille = 'actif'
            LIMIT 1
        ");
        $stmtTarif->execute([':prod' => $produit_code, ':cat' => $categorie_code]);
        $grilleRow = $stmtTarif->fetch(PDO::FETCH_ASSOC);

        $prix_applique = $grilleRow ? (float)$grilleRow['prix_vente'] : (float)$catMatch['prix_vente_defaut'];

        // 3. Génération des codes uniques
        $code_pesee = 'PESEE-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        $code_etiquette = 'OVO-' . date('Ymd') . '-' . rand(1000, 9999);
        $user_code = $_SESSION[USERS_AUTH]['code_user'] ?? 'USR-GEST-001';
        $etablissement_code = $_SESSION[USERS_AUTH]['etablissement_code'] ?? '5454544456';
        $zone_code = $_SESSION[USERS_AUTH]['zone_user'] ?? 'DEFAULT';

        // 4. Insérer dans `pesees_etiquettes_avicole`
        $insertStmt = $db->prepare("
            INSERT INTO pesees_etiquettes_avicole (
                code_pesee, code_etiquette, produit_code, categorie_poids_code, 
                poids_net_reel, prix_unitaire_applique, numero_lot, date_pesee, 
                date_limite_consommation, statut_pesee, user_code, etablissement_code, zone_code
            ) VALUES (
                :code_pesee, :code_etiquette, :produit_code, :categorie_poids_code, 
                :poids_net_reel, :prix_unitaire_applique, :numero_lot, NOW(), 
                :dlc, 'en_stock', :user_code, :etablissement_code, :zone_code
            )
        ");

        $success = $insertStmt->execute([
            ':code_pesee' => $code_pesee,
            ':code_etiquette' => $code_etiquette,
            ':produit_code' => $produit_code,
            ':categorie_poids_code' => $categorie_code,
            ':poids_net_reel' => $poids_net,
            ':prix_unitaire_applique' => $prix_applique,
            ':numero_lot' => $numero_lot ?: 'LOT-' . date('Ymd'),
            ':dlc' => $dlc,
            ':user_code' => $user_code,
            ':etablissement_code' => $etablissement_code,
            ':zone_code' => $zone_code
        ]);

        if ($success) {
            // Record Stock Entry Movement
            $mvmStmt = $db->prepare("
                INSERT INTO mouvements_stock_aviculture_avicole (
                    code_mouvement, produit_code, categorie_poids_code, type_mouvement, 
                    quantite_pieces, poids_total_kg, reference_document, date_mouvement, 
                    user_code, etablissement_code, zone_code
                ) VALUES (
                    :code_mvm, :produit_code, :categorie_code, 'ENTREE_ABATTAGE', 
                    1, :poids_net, :ref, NOW(), :user_code, :etablissement_code, :zone_code
                )
            ");
            $mvmStmt->execute([
                ':code_mvm' => 'MVM-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                ':produit_code' => $produit_code,
                ':categorie_code' => $categorie_code,
                ':poids_net' => $poids_net,
                ':ref' => $code_etiquette,
                ':user_code' => $user_code,
                ':etablissement_code' => $etablissement_code,
                ':zone_code' => $zone_code
            ]);

            $this->success("Pesée enregistrée avec succès ! Étiquette générée : {$code_etiquette}", [
                'code_etiquette' => $code_etiquette,
                'categorie' => $catMatch['libelle_categorie_poids'],
                'prix' => number_format($prix_applique, 0, ',', ' ') . ' FCFA',
                'poids_net' => number_format($poids_net, 3, ',', ' ') . ' kg'
            ]);
        } else {
            $this->error("Erreur lors de l'enregistrement de la pesée.");
        }
    }

    /**
     * Impression / Prévisualisation de l'étiquette au poids net réel
     */
    public function etiquettePrint($param = null)
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        $id = $this->validator->decrypter($param) ?: (int)$param;

        $stmt = $db->prepare("
            SELECT 
                p.*,
                prod.libelle_produit,
                cat.libelle_categorie_poids,
                e.libelle_etablissement, e.telephone_etablissement
            FROM pesees_etiquettes_avicole p
            LEFT JOIN produits_aviculture_avicole prod ON p.produit_code = prod.code_produit_aviculture
            LEFT JOIN categories_poids_avicole cat ON p.categorie_poids_code = cat.code_categorie_poids
            LEFT JOIN etablissements e ON p.etablissement_code = e.code_etablissement
            WHERE p.id_pesee = :id OR p.code_etiquette = :param
            LIMIT 1
        ");
        $stmt->execute([':id' => $id, ':param' => $param]);
        $pesee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pesee) {
            echo "<div style='padding:20px; font-family:sans-serif; color:red;'>Étiquette non trouvée.</div>";
            return;
        }

        // Rendu HTML d'étiquette au format standard 100mm x 60mm
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Étiquette Produit OVOLIA - <?= htmlspecialchars($pesee['code_etiquette']) ?></title>
            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    background: #f1f5f9;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                }
                .label-card {
                    width: 360px;
                    background: #fff;
                    border: 2px solid #0f172a;
                    border-radius: 8px;
                    padding: 16px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                }
                .label-header {
                    border-bottom: 2px solid #059669;
                    padding-bottom: 8px;
                    margin-bottom: 12px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .brand-title {
                    font-size: 20px;
                    font-weight: 900;
                    color: #059669;
                    letter-spacing: 1px;
                }
                .category-badge {
                    background: #0f172a;
                    color: #fff;
                    padding: 4px 10px;
                    border-radius: 4px;
                    font-size: 12px;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                .prod-name {
                    font-size: 16px;
                    font-weight: bold;
                    color: #1e293b;
                    margin-bottom: 10px;
                }
                .weight-box {
                    background: #ecfdf5;
                    border: 1.5px dashed #059669;
                    border-radius: 6px;
                    padding: 10px;
                    text-align: center;
                    margin-bottom: 12px;
                }
                .weight-val {
                    font-size: 26px;
                    font-weight: 900;
                    color: #047857;
                }
                .price-val {
                    font-size: 20px;
                    font-weight: bold;
                    color: #b91c1c;
                }
                .label-details {
                    font-size: 11px;
                    color: #475569;
                    line-height: 1.5;
                    margin-bottom: 10px;
                }
                .barcode-sim {
                    text-align: center;
                    border-top: 1px solid #cbd5e1;
                    padding-top: 8px;
                    font-family: monospace;
                    letter-spacing: 3px;
                    font-size: 14px;
                    font-weight: bold;
                }
                @media print {
                    body { background: #fff; }
                    .label-card { box-shadow: none; border: 1px solid #000; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div style="position: absolute; top: 15px;" class="no-print">
                <button onclick="window.print()" style="padding: 8px 16px; background: #059669; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                    🖨️ Imprimer l'Étiquette
                </button>
            </div>
            <div class="label-card">
                <div class="label-header">
                    <div class="brand-title">OVOLIA</div>
                    <div class="category-badge"><?= htmlspecialchars($pesee['libelle_categorie_poids']) ?></div>
                </div>
                <div class="prod-name"><?= htmlspecialchars($pesee['libelle_produit']) ?></div>
                <div class="weight-box">
                    <div style="font-size: 11px; text-transform: uppercase; color: #047857; font-weight: bold;">Poids Net Réel</div>
                    <div class="weight-val"><?= number_format($pesee['poids_net_reel'], 3, ',', ' ') ?> kg</div>
                    <div class="price-val"><?= number_format($pesee['prix_unitaire_applique'], 0, ',', ' ') ?> FCFA</div>
                </div>
                <div class="label-details">
                    <div><strong>N° Lot :</strong> <?= htmlspecialchars($pesee['numero_lot']) ?></div>
                    <div><strong>Date Pesée :</strong> <?= date('d/m/Y H:i', strtotime($pesee['date_pesee'])) ?></div>
                    <div><strong>DLC :</strong> <?= date('d/m/Y', strtotime($pesee['date_limite_consommation'])) ?></div>
                    <div><em>Fraîcheur, qualité et hygiène garantie OVOLIA.</em></div>
                </div>
                <div class="barcode-sim">
                    ||||| |||| || |||||| ||| ||<br>
                    <span style="font-size: 11px; font-weight: normal; letter-spacing: 1px; color: #000;">
                        <?= htmlspecialchars($pesee['code_etiquette']) ?>
                    </span>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}
