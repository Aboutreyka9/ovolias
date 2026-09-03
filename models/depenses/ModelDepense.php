<?php

class ModelDepense extends BaseModel
{
    protected string $table = 'depenses';
    protected string $primaryKey = 'id_depense';
    protected ?string $statusField = 'statut_depense';
    protected ?string $createdAtField = 'created_at_depense';

    public function getAllWithDetails(): array
    {
        try {
            $sql = "
                SELECT d.*, 
                       td.libelle_type_depense,
                       u.nom_user, u.prenom_user
                FROM depenses d
                LEFT JOIN type_depenses td ON td.code_type_depense = d.type_depense_code
                LEFT JOIN users u ON u.code_user = d.user_code
                ORDER BY d.created_at_depense DESC, d.id_depense DESC
            ";
            return $this->getCon()->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelDepense::getAllWithDetails error: " . $e->getMessage());
            return [];
        }
    }
}
