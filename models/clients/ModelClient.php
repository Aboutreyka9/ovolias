<?php

class ModelClient extends BaseModel
{
    protected string $table = 'clients_avicoles';
    protected string $primaryKey = 'id_client';
    protected ?string $statusField = 'statut_client';
    protected ?string $createdAtField = 'created_at_client';

    public function getAllWithZone(?string $zoneCode = null): array
    {
        try {
            $sql = "
                SELECT c.*, z.libelle_zone
                FROM clients_avicoles c
                LEFT JOIN zones z ON z.code_zone = c.zone_code
                WHERE 1=1
            ";
            $params = [];
            if (!empty($zoneCode)) {
                $sql .= " AND c.zone_code = ?";
                $params[] = $zoneCode;
            }
            $sql .= " ORDER BY c.id_client DESC";
            $stmt = $this->getCon()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelClient::getAllWithZone error: " . $e->getMessage());
            return [];
        }
    }
}
