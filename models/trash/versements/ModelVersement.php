<?php

class ModelVersement extends BaseModel
{
    protected string $table = 'versements_commerciaux';
    protected string $primaryKey = 'id_versement';
    protected ?string $statusField = 'statut_versement';
    protected ?string $createdAtField = 'created_at_versement';

    public function getAllWithDetails(): array
    {
        try {
            $sql = "
                SELECT v.*, 
                       u.nom_user as nom_commercial, u.prenom_user as prenom_commercial,
                       val.nom_user as nom_validator, val.prenom_user as prenom_validator,
                       z.libelle_zone
                FROM versements_commerciaux v
                LEFT JOIN users u ON u.code_user = v.commercial_code
                LEFT JOIN users val ON val.code_user = v.user_validate
                LEFT JOIN zones z ON z.code_zone = v.zone_code
                ORDER BY v.created_at_versement DESC, v.id_versement DESC
            ";
            return $this->getCon()->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelVersement::getAllWithDetails error: " . $e->getMessage());
            return [];
        }
    }

    public function validateVersement(int $id, string $userValidateCode, string $commentaire = ''): bool
    {
        try {
            $sql = "UPDATE versements_commerciaux 
                    SET statut_versement = 'valide', 
                        user_validate = ?, 
                        date_validation = NOW(), 
                        commentaire_validation = ?
                    WHERE id_versement = ?";
            $stmt = $this->getCon()->prepare($sql);
            return $stmt->execute([$userValidateCode, $commentaire, $id]);
        } catch (Exception $e) {
            error_log("ModelVersement::validateVersement error: " . $e->getMessage());
            return false;
        }
    }
}
