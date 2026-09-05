<?php

class ModelDistribution extends BaseModel
{
    protected string $table = 'distributions';
    protected string $primaryKey = 'id_distribution';
    protected ?string $statusField = 'statut_distribution';
    protected ?string $createdAtField = 'created_at_distribution';

    public function getAllWithDetails(): array
    {
        try {
            $sql = "
                SELECT d.*, 
                       c.nom_client, c.telephone_client,
                       s.code_souscription, s.statut_souscription,
                       p.libelle_pack,
                       u.nom_user as nom_livreur, u.prenom_user as prenom_livreur,
                       z.libelle_zone
                FROM distributions d
                LEFT JOIN clients c ON c.code_client = d.client_code
                LEFT JOIN souscriptions s ON s.code_souscription = d.souscription_code
                LEFT JOIN pack_souscriptions ps ON ps.souscription_code = s.code_souscription
                LEFT JOIN packs p ON p.code_pack = ps.pack_code
                LEFT JOIN users u ON u.code_user = d.agent_livreur_code
                LEFT JOIN zones z ON z.code_zone = d.zone_code
                ORDER BY d.created_at_distribution DESC
            ";
            return $this->getCon()->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelDistribution::getAllWithDetails error: " . $e->getMessage());
            return [];
        }
    }

    public function createDistribution(array $data): bool
    {
        try {
            $this->getCon()->beginTransaction();

            $cols = $this->getCon()->query("DESCRIBE distributions")->fetchAll(PDO::FETCH_COLUMN);
            $filteredData = array_intersect_key($data, array_flip($cols));

            $colsStr = implode(',', array_keys($filteredData));
            $paramsStr = implode(',', array_fill(0, count($filteredData), '?'));

            $stmt = $this->getCon()->prepare("INSERT INTO distributions ({$colsStr}) VALUES ({$paramsStr})");
            $stmt->execute(array_values($filteredData));

            $souscriptionCode = $data['souscription_code'] ?? null;
            if ($souscriptionCode) {
                $statut = $data['statut_distribution'] ?? 'valide';
                $stmtUpd = $this->getCon()->prepare("
                    UPDATE souscriptions 
                    SET statut_distribution = ?,
                        updated_at_souscription = ?
                    WHERE code_souscription = ?
                ");
                $stmtUpd->execute([$statut, date('Y-m-d H:i:s'), $souscriptionCode]);
            }

            $this->getCon()->commit();
            return true;
        } catch (Exception $e) {
            if ($this->getCon()->inTransaction()) {
                $this->getCon()->rollBack();
            }
            error_log("ModelDistribution::createDistribution error: " . $e->getMessage());
            return false;
        }
    }

    public function getBySouscription(string $souscriptionCode): ?array
    {
        try {
            $sql = "SELECT * FROM distributions WHERE souscription_code = ? ORDER BY created_at_distribution DESC LIMIT 1";
            $stmt = $this->getCon()->prepare($sql);
            $stmt->execute([$souscriptionCode]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            error_log("ModelDistribution::getBySouscription error: " . $e->getMessage());
            return null;
        }
    }
}
