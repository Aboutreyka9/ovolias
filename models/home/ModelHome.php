<?php

class ModelHome extends BaseModel
{
    protected string $table = 'clients';
    protected string $primaryKey = 'id_client';

    public function __construct()
    {
        parent::__construct();
    }

    public function getStats(?string $anneeCode = null, ?string $userCode = null, ?string $roleCode = null): array
    {
        try {
            $db = $this->pdo->getCon();

            if (!$anneeCode) {
                $anneeCode = $_SESSION['annee_active_code'] ?? null;
            }
            if (!$anneeCode) {
                $stmtA = $db->query("SELECT code_annee, libelle_annee FROM annees WHERE statut_annee = 'actif' ORDER BY id_annee DESC LIMIT 1");
                $activeRow = $stmtA->fetch(PDO::FETCH_ASSOC);
                if ($activeRow) {
                    $anneeCode = $activeRow['code_annee'];
                    $_SESSION['annee_active_code'] = $activeRow['code_annee'];
                    $_SESSION['annee_active_libelle'] = $activeRow['libelle_annee'];
                }
            }

            $userCodeFilter = Context::isCommercial() ? Context::user() : ($userCode ?? null);

            // 1. Clients
            $sqlClients = "SELECT COUNT(*) FROM clients WHERE 1=1";
            $pClients = [];
            if ($userCodeFilter) {
                $sqlClients .= " AND user_code = ?";
                $pClients[] = $userCodeFilter;
            }
            $stmt = $db->prepare($sqlClients);
            $stmt->execute($pClients);
            $totalClients = (int)$stmt->fetchColumn();

            // 2. Packs & Articles
            $totalPacks = (int)$db->query("SELECT COUNT(*) FROM packs WHERE statut_pack = 'actif'")->fetchColumn();
            $totalArticles = (int)$db->query("SELECT COUNT(*) FROM articles WHERE statut_article = 'actif'")->fetchColumn();

            // 3. Souscriptions
            $sqlSouscr = "SELECT COUNT(*) FROM souscriptions WHERE statut_souscription IN ('valide', 'reconduite')";
            $pSouscr = [];
            if ($userCodeFilter) {
                $sqlSouscr .= " AND user_code = ?";
                $pSouscr[] = $userCodeFilter;
            }
            $stmt = $db->prepare($sqlSouscr);
            $stmt->execute($pSouscr);
            $totalSouscriptions = (int)$stmt->fetchColumn();

            $sqlSoldees = "SELECT COUNT(*) FROM souscriptions WHERE statut_souscription = 'solde'";
            $pSoldees = [];
            if ($userCodeFilter) {
                $sqlSoldees .= " AND user_code = ?";
                $pSoldees[] = $userCodeFilter;
            }
            $stmt = $db->prepare($sqlSoldees);
            $stmt->execute($pSoldees);
            $totalSouscriptionsSoldees = (int)$stmt->fetchColumn();

            // 4. Cotisations
            $sqlCotis = "SELECT COALESCE(SUM(montant_cautisation_client), 0) FROM cautisation_clients WHERE statut_cautisation_client != 'ennule'";
            $pCotis = [];
            if ($userCodeFilter) {
                $sqlCotis .= " AND (user_code = ? OR commercial_code = ?)";
                $pCotis[] = $userCodeFilter;
                $pCotis[] = $userCodeFilter;
            }
            $stmt = $db->prepare($sqlCotis);
            $stmt->execute($pCotis);
            $totalCotisations = (float)($stmt->fetchColumn() ?: 0);

            $totalPaiements = (float)($db->query("SELECT COALESCE(SUM(montant_paiement), 0) FROM paiements WHERE statut_paiement = 'confirme'")->fetchColumn() ?: 0);
            $caEncaisse = $totalCotisations + $totalPaiements;

            // 5. Versements
            $sqlVersVal = "SELECT COALESCE(SUM(montant_versement), 0) FROM versements_commerciaux WHERE statut_versement = 'valide'";
            $pVersVal = [];
            if ($userCodeFilter) {
                $sqlVersVal .= " AND (user_code = ? OR commercial_code = ?)";
                $pVersVal[] = $userCodeFilter;
                $pVersVal[] = $userCodeFilter;
            }
            $stmt = $db->prepare($sqlVersVal);
            $stmt->execute($pVersVal);
            $totalVersements = (float)($stmt->fetchColumn() ?: 0);

            $sqlVersAtt = "SELECT COALESCE(SUM(montant_versement), 0) FROM versements_commerciaux WHERE statut_versement = 'En attente'";
            $pVersAtt = [];
            if ($userCodeFilter) {
                $sqlVersAtt .= " AND (user_code = ? OR commercial_code = ?)";
                $pVersAtt[] = $userCodeFilter;
                $pVersAtt[] = $userCodeFilter;
            }
            $stmt = $db->prepare($sqlVersAtt);
            $stmt->execute($pVersAtt);
            $totalVersementsEnAttente = (float)($stmt->fetchColumn() ?: 0);

            // 6. Dépenses & Solde Net
            $totalDepenses = (float)($db->query("SELECT COALESCE(SUM(montant_depense), 0) FROM depenses WHERE statut_depense != 'inactif'")->fetchColumn() ?: 0);
            $soldeNet = $caEncaisse - $totalDepenses;

            // 7. Distributions
            $totalDistributions = (int)$db->query("SELECT COUNT(*) FROM distributions")->fetchColumn();
            $totalDistributionsValidees = (int)$db->query("SELECT COUNT(*) FROM distributions WHERE statut_distribution = 'valide'")->fetchColumn();

            // 8. Ventes Avicoles OVOLIA
            $sqlV = "SELECT COUNT(*) AS total_ventes, COALESCE(SUM(montant_total_net), 0) AS ca_ventes FROM ventes_avicoles WHERE statut_vente != 'annulee'";
            $pV = [];
            if ($userCodeFilter) {
                $sqlV .= " AND user_code = ?";
                $pV[] = $userCodeFilter;
            }
            $stmtV = $db->prepare($sqlV);
            $stmtV->execute($pV);
            $rowV = $stmtV->fetch(PDO::FETCH_ASSOC) ?: [];
            $totalVentesAvicoles = (int)($rowV['total_ventes'] ?? 0);
            $caVentesAvicoles = (float)($rowV['ca_ventes'] ?? 0);

            return [
                'annee_code' => $anneeCode,
                'total_clients' => $totalClients,
                'total_packs' => $totalPacks,
                'total_souscriptions' => $totalSouscriptions,
                'total_souscriptions_soldees' => $totalSouscriptionsSoldees,
                'total_articles' => $totalArticles,
                'total_cotisations' => $totalCotisations,
                'total_paiements' => $totalPaiements,
                'ca_encaisse' => $caEncaisse,
                'total_versements' => $totalVersements,
                'total_versements_en_attente' => $totalVersementsEnAttente,
                'total_depenses' => $totalDepenses,
                'total_distributions' => $totalDistributions,
                'total_distributions_validees' => $totalDistributionsValidees,
                'total_ventes_avicoles' => $totalVentesAvicoles,
                'ca_ventes_avicoles' => $caVentesAvicoles,
                'solde_net' => $soldeNet + $caVentesAvicoles
            ];
        } catch (Exception $e) {
            error_log("ModelHome::getStats error: " . $e->getMessage());
            return [
                'annee_code' => $anneeCode,
                'total_clients' => 0,
                'total_packs' => 0,
                'total_souscriptions' => 0,
                'total_souscriptions_soldees' => 0,
                'total_articles' => 0,
                'total_cotisations' => 0,
                'total_paiements' => 0,
                'ca_encaisse' => 0,
                'total_versements' => 0,
                'total_versements_en_attente' => 0,
                'total_depenses' => 0,
                'total_distributions' => 0,
                'total_distributions_validees' => 0,
                'solde_net' => 0
            ];
        }
    }

    public function getRecentCotisations(int $limit = 5): array
    {
        try {
            $db = $this->pdo->getCon();
            $sql = "SELECT c.*, cl.nom_client, cl.telephone_client, s.code_souscription
                    FROM cautisation_clients c
                    LEFT JOIN souscriptions s ON s.code_souscription = c.souscription_code
                    LEFT JOIN clients cl ON cl.code_client = s.client_code
                    WHERE 1=1";
            $params = [];
            if (Context::isCommercial()) {
                $sql .= " AND (c.user_code = ? OR c.commercial_code = ?)";
                $params[] = Context::user();
                $params[] = Context::user();
            }
            $limitInt = max(1, (int)$limit);
            $sql .= " ORDER BY c.id_cautisation_client DESC LIMIT $limitInt";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelHome::getRecentCotisations error: " . $e->getMessage());
            return [];
        }
    }

    public function getRecentVersements(int $limit = 5): array
    {
        try {
            $db = $this->pdo->getCon();
            $sql = "SELECT v.*, u.nom_user as nom_commercial, u.prenom_user as prenom_commercial, z.libelle_zone
                    FROM versements_commerciaux v
                    LEFT JOIN users u ON u.code_user = v.commercial_code
                    LEFT JOIN zones z ON z.code_zone = v.zone_code
                    WHERE 1=1";
            $params = [];
            if (Context::isCommercial()) {
                $sql .= " AND (v.user_code = ? OR v.commercial_code = ?)";
                $params[] = Context::user();
                $params[] = Context::user();
            }
            $limitInt = max(1, (int)$limit);
            $sql .= " ORDER BY v.id_versement DESC LIMIT $limitInt";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelHome::getRecentVersements error: " . $e->getMessage());
            return [];
        }
    }

    public function getRecentDepenses(int $limit = 5): array
    {
        try {
            $db = $this->pdo->getCon();
            $sql = "SELECT d.*, td.libelle_type_depense
                    FROM depenses d
                    LEFT JOIN type_depenses td ON td.code_type_depense = d.type_depense_code
                    ORDER BY d.id_depense DESC
                    LIMIT $limit";
            return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelHome::getRecentDepenses error: " . $e->getMessage());
            return [];
        }
    }

    public function getRecentVentesAvicoles(int $limit = 5): array
    {
        try {
            $db = $this->pdo->getCon();
            $sql = "SELECT v.*, c.nom_client_avicole, u.nom_user, u.prenom_user
                    FROM ventes_avicoles v
                    LEFT JOIN clients_avicoles c ON c.code_client_avicole = v.client_avicole_code
                    LEFT JOIN users u ON u.code_user = v.user_code
                    WHERE v.statut_vente != 'annulee'";
            $params = [];
            if (Context::isCommercial()) {
                $sql .= " AND v.user_code = ?";
                $params[] = Context::user();
            }
            $limitInt = max(1, (int)$limit);
            $sql .= " ORDER BY v.id_vente_avicole DESC LIMIT $limitInt";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelHome::getRecentVentesAvicoles error: " . $e->getMessage());
            return [];
        }
    }
}
