<?php

class ModelDepense extends BaseModel
{
    protected string $table = 'depenses';
    protected string $primaryKey = 'id_depense';
    protected ?string $statusField = 'statut_depense';
    protected ?string $createdAtField = 'created_at_depense';

    public function getAllWithDetails(?string $zoneCode = null): array
    {
        try {
            $sql = "
                SELECT d.*, 
                       td.libelle_type_depense,
                       u.nom_user, u.prenom_user
                FROM depenses d
                LEFT JOIN type_depenses td ON td.code_type_depense = d.type_depense_code
                LEFT JOIN users u ON u.code_user = d.user_code
                WHERE 1=1
            ";
            $params = [];
            if (!empty($zoneCode)) {
                $sql .= " AND d.zone_code = ?";
                $params[] = $zoneCode;
            }
            $sql .= " ORDER BY d.id_depense DESC";
            $stmt = $this->getCon()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelDepense::getAllWithDetails error: " . $e->getMessage());
            return [];
        }
    }
}
