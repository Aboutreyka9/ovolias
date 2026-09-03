<?php

class ModelClient extends BaseModel
{
    protected string $table = 'clients';
    protected string $primaryKey = 'id_client';
    protected ?string $statusField = 'statut_client';
    protected ?string $createdAtField = 'created_at_client';

    public function getAllWithZone(): array
    {
        try {
            $sql = "
                SELECT c.*, z.libelle_zone
                FROM clients c
                LEFT JOIN zones z ON z.code_zone = c.zone_code
                ORDER BY c.created_at_client DESC
            ";
            return $this->getCon()->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelClient::getAllWithZone error: " . $e->getMessage());
            return [];
        }
    }
}
