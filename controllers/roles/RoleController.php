<?php

class RoleController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelRole();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/roles/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $sql = "SELECT r.*,
                       COUNT(DISTINCT ur.user_code) as nb_users,
                       COUNT(DISTINCT rp.permission_code) as nb_permissions
                FROM roles r
                LEFT JOIN user_roles ur ON ur.role_code = r.code_role
                LEFT JOIN role_permissions rp ON rp.role_code = r.code_role
                GROUP BY r.id
                ORDER BY r.id ASC";
        $items = $this->model->getCon()->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        foreach ($items as $i) {
            $id = $i['id'];
            $idCrypte = $this->validator->crypter($id);
            $data[] = array_merge($i, [
                'id' => $id,
                'editId' => $idCrypte
            ]);
        }
        $this->json(['data' => $data]);
    }

    public function add()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $data = $_POST;
        unset($data['csrf_token']);

        $libelle = trim($data['libelle_role'] ?? '');
        $codeRole = trim($data['code_role'] ?? '');
        $module = trim($data['module'] ?? 'ADMINISTRATION');
        $groupe = trim($data['groupe'] ?? 'Direction');
        $description = trim($data['description'] ?? '');
        $permissions = $data['permissions'] ?? [];

        if (empty($libelle)) {
            $this->error('Le libellé du rôle est obligatoire.');
            return;
        }

        if (empty($codeRole)) {
            $codeRole = 'ROLE_' . strtoupper(preg_replace('/[^A-Za-z0-9]/', '_', $libelle));
        }

        $stmtCheck = $this->model->getCon()->prepare("SELECT id FROM roles WHERE code_role = ?");
        $stmtCheck->execute([$codeRole]);
        if ($stmtCheck->fetch()) {
            $this->error('Ce code de rôle existe déjà.');
            return;
        }

        $stmtIns = $this->model->getCon()->prepare("
            INSERT INTO roles (code_role, libelle_role, module, groupe, description, statut_role)
            VALUES (?, ?, ?, ?, ?, 'actif')
        ");
        if ($stmtIns->execute([$codeRole, $libelle, $module, $groupe, $description])) {
            if (!empty($permissions) && is_array($permissions)) {
                $this->model->syncPermissions($codeRole, $permissions);
            }
            $this->success('Rôle créé avec succès !');
        } else {
            $this->error('Erreur lors de la création du rôle.');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id');
        if (!$id) { $this->error('Identifiant invalide'); return; }

        $role = $this->model->getById($id);
        if (!$role) { $this->error('Rôle introuvable'); return; }

        $data = $_POST;
        unset($data['csrf_token']);

        $libelle = trim($data['libelle_role'] ?? '');
        $module = trim($data['module'] ?? $role['module']);
        $groupe = trim($data['groupe'] ?? $role['groupe']);
        $description = trim($data['description'] ?? $role['description']);
        $statut = $data['statut_role'] ?? 'actif';
        $permissions = $data['permissions'] ?? [];

        if (empty($libelle)) {
            $this->error('Le libellé du rôle est obligatoire.');
            return;
        }

        $stmtUp = $this->model->getCon()->prepare("
            UPDATE roles 
            SET libelle_role = ?, module = ?, groupe = ?, description = ?, statut_role = ?
            WHERE id = ?
        ");
        if ($stmtUp->execute([$libelle, $module, $groupe, $description, $statut, $id])) {
            if (isset($data['permissions']) && is_array($permissions)) {
                $this->model->syncPermissions($role['code_role'], $permissions);
            }
            $this->success('Rôle et permissions mis à jour avec succès !');
        } else {
            $this->error('Erreur lors de la modification du rôle.');
        }
    }

    public function changer()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = $this->post('id');
        if ($id && $this->model->getById($id)) {
            if ($this->model->toggleStatus($id)) {
                $this->success('Statut mis à jour avec succès!', ['reload' => true]);
            } else {
                $this->error('Erreur lors de la mise à jour du statut');
            }
        } else {
            $this->error('Item introuvable');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $role = $this->model->getById($id);
            if (!$role) { header('Location: ' . RACINE . 'role/list'); exit(); }

            $permissions = $this->model->getPermissions($role['code_role']);
            $stmtUsers = $this->model->getCon()->prepare("
                SELECT u.* FROM users u
                INNER JOIN user_roles ur ON ur.user_code = u.code_user
                WHERE ur.role_code = ?
            ");
            $stmtUsers->execute([$role['code_role']]);
            $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'role/list'); exit();
        }
        $this->loadView('../views/roles/details.php', [
            'role' => $role,
            'permissions' => $permissions,
            'users' => $users,
            'encryptedId' => $encryptedId
        ]);
    }

    public function edition($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $role = $this->model->getById($id);
            if (!$role) { header('Location: ' . RACINE . 'role/list'); exit(); }

            $allPermissions = (new ModelPermission())->getGrouped();
            $assignedPerms = $this->model->getPermissions($role['code_role']);
            $assignedCodes = array_column($assignedPerms, 'code_permission');

            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'role/list'); exit();
        }
        $this->loadView('../views/roles/edit.php', [
            'role' => $role,
            'allPermissions' => $allPermissions,
            'assignedCodes' => $assignedCodes,
            'encryptedId' => $encryptedId
        ]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $allPermissions = (new ModelPermission())->getGrouped();
        $this->loadView('../views/roles/edit.php', [
            'role' => [],
            'allPermissions' => $allPermissions,
            'assignedCodes' => []
        ]);
    }
}