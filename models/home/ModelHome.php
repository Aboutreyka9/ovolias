<?php

class ModelHome extends BaseModel
{
    protected string $table = 'ventes_avicoles';
    protected string $primaryKey = 'id_vente';

    public function __construct()
    {
        parent::__construct();
    }

    public function getStats(?string $anneeCode = null, ?string $userCode = null, ?string $roleCode = null): array
    {
        try {
            $db = $this->pdo->getCon();

            // Filtrage par utilisateur si le rôle est commercial ou caissier
            $isCommercialOrCaissier = in_array($roleCode, ['ROLE_COMMERCIAL', 'ROLE_CAISSIER'], true);
            $userFilter = $isCommercialOrCaissier ? ($userCode ?? null) : null;

            // 1. Clients Avicoles
            $sqlClients = "SELECT COUNT(*) FROM clients_avicoles WHERE statut_client = 'actif'";
            $pClients = [];
            if ($userFilter) {
                $sqlClients .= " AND (created_by = ? OR user_code = ?)";
                $pClients[] = $userFilter;
                $pClients[] = $userFilter;
            }
            $stmt = $db->prepare($sqlClients);
            $stmt->execute($pClients);
            $totalClients = (int)($stmt->fetchColumn() ?: 0);

            // 2. Produits Avicoles au Catalogue
            $totalProduits = (int)($db->query("SELECT COUNT(*) FROM produits_aviculture_avicole WHERE statut_produit = 'actif'")->fetchColumn() ?: 0);

            // 3. Ventes Avicoles & Chiffre d'Affaires POS
            $sqlVentes = "SELECT COUNT(*) AS total_ventes, COALESCE(SUM(montant_total_net), 0) AS ca_ventes FROM ventes_avicoles WHERE statut_vente != 'annulee'";
            $pVentes = [];
            if ($userFilter) {
                $sqlVentes .= " AND user_code = ?";
                $pVentes[] = $userFilter;
            }
            $stmt = $db->prepare($sqlVentes);
            $stmt->execute($pVentes);
            $rowV = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
            $totalVentes = (int)($rowV['total_ventes'] ?? 0);
            $caVentes = (float)($rowV['ca_ventes'] ?? 0);

            // 4. Achats & Approvisionnements Avicoles
            $sqlAchats = "SELECT COUNT(*) AS total_achats, COALESCE(SUM(montant_total), 0) AS montant_achats FROM achats_avicoles WHERE statut_achat != 'annule'";
            $rowA = $db->query($sqlAchats)->fetch(PDO::FETCH_ASSOC) ?: [];
            $totalAchats = (int)($rowA['total_achats'] ?? 0);
            $montantAchats = (float)($rowA['montant_achats'] ?? 0);

            // 5. Pesées & Étiquettes enregistrées
            $sqlPesees = "SELECT COUNT(*) AS total_pesees, COALESCE(SUM(poids_net), 0) AS poids_total FROM pesees_etiquettes_avicole WHERE statut_pesee = 'valide'";
            $rowP = $db->query($sqlPesees)->fetch(PDO::FETCH_ASSOC) ?: [];
            $totalPesees = (int)($rowP['total_pesees'] ?? 0);
            $poidsTotalPesees = (float)($rowP['poids_total'] ?? 0);

            // 6. Mouvements de Stock Avicole
            $totalMouvementsStock = (int)($db->query("SELECT COUNT(*) FROM mouvements_stock_aviculture_avicole")->fetchColumn() ?: 0);

            // 7. Dépenses d'Exploitation
            $totalDepenses = (float)($db->query("SELECT COALESCE(SUM(montant_depense), 0) FROM depenses WHERE statut_depense = 'actif'")->fetchColumn() ?: 0);

            // 8. Solde Net Trésorerie
            $soldeNet = $caVentes - $totalDepenses;

            return [
                'total_clients' => $totalClients,
                'total_produits' => $totalProduits,
                'total_ventes' => $totalVentes,
                'ca_ventes' => $caVentes,
                'total_achats' => $totalAchats,
                'montant_achats' => $montantAchats,
                'total_pesees' => $totalPesees,
                'poids_total_pesees' => $poidsTotalPesees,
                'total_mouvements_stock' => $totalMouvementsStock,
                'total_depenses' => $totalDepenses,
                'solde_net' => $soldeNet
            ];
        } catch (Exception $e) {
            error_log("ModelHome::getStats error: " . $e->getMessage());
            return [
                'total_clients' => 0,
                'total_produits' => 0,
                'total_ventes' => 0,
                'ca_ventes' => 0,
                'total_achats' => 0,
                'montant_achats' => 0,
                'total_pesees' => 0,
                'poids_total_pesees' => 0,
                'total_mouvements_stock' => 0,
                'total_depenses' => 0,
                'solde_net' => 0
            ];
        }
    }

    public function getRecentVentesAvicoles(int $limit = 5, ?string $userCode = null, ?string $roleCode = null): array
    {
        try {
            $db = $this->pdo->getCon();
            $sql = "SELECT v.*, c.nom_client AS client_nom
                    FROM ventes_avicoles v
                    LEFT JOIN clients_avicoles c ON v.client_code = c.code_client
                    WHERE 1=1";
            $params = [];

            if (in_array($roleCode, ['ROLE_COMMERCIAL', 'ROLE_CAISSIER'], true) && $userCode) {
                $sql .= " AND v.user_code = ?";
                $params[] = $userCode;
            }

            $limitInt = max(1, (int)$limit);
            $sql .= " ORDER BY v.id_vente DESC LIMIT $limitInt";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelHome::getRecentVentesAvicoles error: " . $e->getMessage());
            return [];
        }
    }

    public function getRecentPeseesAvicoles(int $limit = 5): array
    {
        try {
            $db = $this->pdo->getCon();
            $limitInt = max(1, (int)$limit);
            $sql = "SELECT p.*, pr.designation_produit, c.libelle_categorie
                    FROM pesees_etiquettes_avicole p
                    LEFT JOIN produits_aviculture_avicole pr ON p.produit_code = pr.code_produit
                    LEFT JOIN categories_poids_avicole c ON p.categorie_poids_code = c.code_categorie
                    ORDER BY p.id_pesee DESC LIMIT $limitInt";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelHome::getRecentPeseesAvicoles error: " . $e->getMessage());
            return [];
        }
    }

    public function getRecentAchatsAvicoles(int $limit = 5): array
    {
        try {
            $db = $this->pdo->getCon();
            $limitInt = max(1, (int)$limit);
            $sql = "SELECT a.*, f.nom_fournisseur
                    FROM achats_avicoles a
                    LEFT JOIN fournisseurs_avicoles f ON a.fournisseur_code = f.code_fournisseur
                    ORDER BY a.id_achat DESC LIMIT $limitInt";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelHome::getRecentAchatsAvicoles error: " . $e->getMessage());
            return [];
        }
    }

    public function getRecentDepenses(int $limit = 5): array
    {
        try {
            $db = $this->pdo->getCon();
            $limitInt = max(1, (int)$limit);
            $sql = "SELECT d.*, t.libelle_type_depense
                    FROM depenses d
                    LEFT JOIN type_depenses t ON d.type_depense_code = t.code_type_depense
                    ORDER BY d.id_depense DESC LIMIT $limitInt";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelHome::getRecentDepenses error: " . $e->getMessage());
            return [];
        }
    }
}
