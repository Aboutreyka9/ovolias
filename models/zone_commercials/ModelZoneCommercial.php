<?php

class ModelZoneCommercial extends BaseModel
{
    protected string $table = 'zone_commercials';
    protected string $primaryKey = 'id_zone_commercial';
    protected ?string $statusField = 'statut_zone_commercial';
    protected ?string $createdAtField = 'created_at_zone_commercial';

    public function getAllWithDetails(): array
    {
        try {
            $sql = "
                SELECT zc.*, 
                       z.libelle_zone, 
                       u.nom_user, u.prenom_user, u.telephone_user
                FROM zone_commercials zc
                LEFT JOIN zones z ON z.code_zone = zc.zone_code
                LEFT JOIN users u ON u.code_user = zc.commercial_code
                ORDER BY zc.created_at_zone_commercial DESC
            ";
            return $this->getCon()->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelZoneCommercial::getAllWithDetails error: " . $e->getMessage());
            return [];
        }
    }
}
