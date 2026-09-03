<?php

class ModelRole extends BaseModel
{
    protected string $table = 'roles';
    protected string $primaryKey = 'id';
    protected ?string $statusField = 'statut_role';

    public function getAllWithPermissions(): array
    {
        try {
            $sql = "SELECT r.*, 
                    GROUP_CONCAT(rp.permission_code SEPARATOR ',') as permission_codes
                    FROM {$this->table} r
                    LEFT JOIN role_permissions rp ON r.code_role = rp.role_code
                    GROUP BY r.id
                    ORDER BY r.id ASC";
            $stmt = $this->getCon()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log('[ModelRole::getAllWithPermissions] ' . $e->getMessage());
            return [];
        }
    }

    public function getPermissions(string $roleCode): array
    {
        try {
            $sql = "SELECT p.code_permission, p.libelle_permission, p.module_permission
                    FROM permissions p
                    INNER JOIN role_permissions rp ON p.code_permission = rp.permission_code
                    WHERE rp.role_code = ? AND p.statut_permission = 'actif'
                    ORDER BY p.code_permission ASC";
            $stmt = $this->getCon()->prepare($sql);
            $stmt->execute([$roleCode]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log('[ModelRole::getPermissions] ' . $e->getMessage());
            return [];
        }
    }

    public function assignPermission(string $roleCode, string $permissionCode): bool
    {
        try {
            $sql = "INSERT INTO role_permissions (role_code, permission_code)
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE permission_code = VALUES(permission_code)";
            $stmt = $this->getCon()->prepare($sql);
            return $stmt->execute([$roleCode, $permissionCode]);
        } catch (Exception $e) {
            error_log('[ModelRole::assignPermission] ' . $e->getMessage());
            return false;
        }
    }

    public function syncPermissions(string $roleCode, array $permissionCodes): bool
    {
        try {
            $this->getCon()->beginTransaction();

            $sql = "DELETE FROM role_permissions WHERE role_code = ?";
            $stmt = $this->getCon()->prepare($sql);
            $stmt->execute([$roleCode]);

            foreach ($permissionCodes as $permissionCode) {
                $this->assignPermission($roleCode, $permissionCode);
            }

            $this->getCon()->commit();
            return true;
        } catch (Exception $e) {
            error_log('[ModelRole::syncPermissions] ' . $e->getMessage());
            $this->getCon()->rollBack();
            return false;
        }
    }
}
