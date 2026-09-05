<?php

class ModelUser extends BaseModel
{
    protected string $table = 'users';
    protected string $primaryKey = 'id_user';
    protected ?string $statusField = 'statut_user';

    /**
     * Récupère tous les rôles et privilèges d'un utilisateur
     */
    public function getUserRoles(string $userCode): array
    {
        try {
            $sql = "SELECT ur.*, r.libelle_role, r.module, r.groupe, r.description 
                    FROM user_roles ur
                    INNER JOIN roles r ON r.code_role = ur.role_code
                    WHERE ur.user_code = ?
                    ORDER BY CASE 
                        WHEN ur.role_code = 'ROLE_SUPERADMIN' THEN 1 
                        WHEN ur.role_code = 'ROLE_ADMIN' THEN 2
                        WHEN ur.role_code = 'ROLE_DIR_GENERAL' THEN 3
                        WHEN ur.role_code = 'ROLE_GESTIONNAIRE' THEN 4
                        WHEN ur.role_code = 'ROLE_COMMERCIAL' THEN 5
                        WHEN ur.role_code = 'ROLE_FINANCE' THEN 6
                        WHEN ur.role_code = 'ROLE_COMPTABLE' THEN 7
                        WHEN ur.role_code = 'ROLE_CAISSIER' THEN 8
                        ELSE 9 END, ur.id ASC";
            $stmt = $this->getCon()->prepare($sql);
            $stmt->execute([$userCode]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelUser::getUserRoles error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère la liste simple des codes de rôles d'un utilisateur
     */
    public function getUserRoleCodes(string $userCode): array
    {
        $roles = $this->getUserRoles($userCode);
        return array_values(array_unique(array_filter(array_column($roles, 'role_code'))));
    }

    /**
     * Récupère le rôle principal (le plus haut dans la hiérarchie) d'un utilisateur
     */
    public function getUserRole(string $userCode): ?array
    {
        $roles = $this->getUserRoles($userCode);
        return !empty($roles) ? $roles[0] : null;
    }

    /**
     * Synchronise une liste de rôles multiples avec permissions CRUD spécifiques par rôle
     * @param string $userCode Code de l'utilisateur
     * @param array $rolesData [ 'ROLE_CODE' => ['create' => 1, 'edit' => 1, ...], ... ] ou ['ROLE_1', 'ROLE_2']
     * @param array $defaultCrud Permissions par défaut si non fournies
     */
    public function syncUserRoles(string $userCode, array $rolesData, array $defaultCrud = []): bool
    {
        try {
            $pdo = $this->getCon();
            $pdo->beginTransaction();

            $stmtDel = $pdo->prepare("DELETE FROM user_roles WHERE user_code = ?");
            $stmtDel->execute([$userCode]);

            if (empty($rolesData)) {
                $rolesData = ['ROLE_SCOLARITE' => ['create' => 1, 'edit' => 1, 'show' => 1, 'delete' => 0]];
            }

            $maxId = (int)($pdo->query("SELECT MAX(id) FROM user_roles")->fetchColumn() ?: 0);
            $nextId = $maxId + 1;

            $stmtIns = $pdo->prepare("
                INSERT INTO user_roles (id, user_code, role_code, create_permission, edit_permission, show_permission, delete_permission) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($rolesData as $key => $val) {
                if (is_array($val)) {
                    $roleCode = trim($key);
                    $createP = !empty($val['create']) ? 1 : 0;
                    $editP   = !empty($val['edit']) ? 1 : 0;
                    $showP   = isset($val['show']) ? (!empty($val['show']) ? 1 : 0) : 1;
                    $deleteP = !empty($val['delete']) ? 1 : 0;
                } else {
                    $roleCode = trim($val);
                    $createP = isset($defaultCrud['create']) ? (int)$defaultCrud['create'] : 1;
                    $editP   = isset($defaultCrud['edit']) ? (int)$defaultCrud['edit'] : 1;
                    $showP   = isset($defaultCrud['show']) ? (int)$defaultCrud['show'] : 1;
                    $deleteP = isset($defaultCrud['delete']) ? (int)$defaultCrud['delete'] : 0;
                }

                if (!empty($roleCode)) {
                    $stmtIns->execute([$nextId++, $userCode, $roleCode, $createP, $editP, $showP, $deleteP]);
                }
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            if ($this->getCon()->inTransaction()) {
                $this->getCon()->rollBack();
            }
            error_log("ModelUser::syncUserRoles error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Attribue un rôle unique ou ajoute un rôle (rétrocompatibilité)
     */
    public function setUserRole(string $userCode, string $roleCode, int $create = 1, int $edit = 1, int $show = 1, int $delete = 0): bool
    {
        return $this->syncUserRoles($userCode, [$roleCode], [
            'create' => $create,
            'edit' => $edit,
            'show' => $show,
            'delete' => $delete
        ]);
    }

    /**
     * Met à jour le mot de passe d'un utilisateur
     */
    public function updatePassword(string $hashPassword, int $userId): bool
    {
        try {
            $sql = "UPDATE users SET password_user = ?, updated_at_user = ? WHERE id_user = ?";
            $stmt = $this->getCon()->prepare($sql);
            return $stmt->execute([$hashPassword, date('Y-m-d H:i:s'), $userId]);
        } catch (Exception $e) {
            error_log("ModelUser::updatePassword error: " . $e->getMessage());
            return false;
        }
    }
}
