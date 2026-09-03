<?php

class PermissionController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelPermission();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/permissions/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $items = $this->model->getAll();
        $data = [];
        foreach ($items as $i) {
            $id = $i['id_permission'];
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
        if (!empty($data['libelle_permission'])) {
            if (!$this->checkUnique('permissions', 'libelle_permission', $data['libelle_permission'], 'Libelle de la permission')) return;
        }
        if (!empty($data['code_permission'])) {
            if (!$this->checkUnique('permissions', 'code_permission', $data['code_permission'], 'Code permission')) return;
        }

        $userCode = Context::user() ?? '';
        $anneeCode = Context::annee();
        $etabCode = '5454544456';
        if (empty($data['code_permission'])) {
            $data['code_permission'] = $this->validator->generateCode('permissions', 'code_permission', 'PER-', 8);
        }
        $data['statut_permission'] = $data['statut_permission'] ?? 'actif';
        $data['created_at_permission'] = date('Y-m-d H:i:s');
        $cols = $this->model->getCon()->query("DESCRIBE permissions")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('user_code', $cols)) $data['user_code'] = $userCode;
        if (in_array('etablissement_code', $cols)) $data['etablissement_code'] = $etabCode;
        if (in_array('annee_code', $cols)) $data['annee_code'] = $anneeCode;
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->create($filteredData)) {
            $this->success('Item créé avec succès!');
        } else {
            $this->error('Erreur lors de la création');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id_permission');
        if (!$id) { $this->error('Identifiant invalide'); return; }
        $data = $_POST;
        unset($data['csrf_token']);
        if (!empty($data['libelle_permission'])) {
            if (!$this->checkUnique('permissions', 'libelle_permission', $data['libelle_permission'], 'Libelle de la permission', 'id_permission', $id)) return;
        }
        if (!empty($data['code_permission'])) {
            if (!$this->checkUnique('permissions', 'code_permission', $data['code_permission'], 'Code permission', 'id_permission', $id)) return;
        }

        $cols = $this->model->getCon()->query("DESCRIBE permissions")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->update($filteredData, $id)) {
            $this->success('Item modifié avec succès!');
        } else {
            $this->error('Erreur lors de la modification');
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
            $item = $this->model->getById($id);
            if (!$item) { header('Location: ' . RACINE . 'permission/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'permission/list'); exit();
        }
        $this->loadView('../views/permissions/details.php', ['item' => $item, 'encryptedId' => $encryptedId]);
    }

    public function edition($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) { header('Location: ' . RACINE . 'permission/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'permission/list'); exit();
        }
        $this->loadView('../views/permissions/edit.php', ['item' => $item, 'encryptedId' => $encryptedId]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $this->loadView('../views/permissions/edit.php', ['item' => []]);
    }

    public function addModule()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $libelle = trim($this->post('libelle_module') ?? '');
        if ($libelle === '') {
            $this->json(['status' => 0, 'message' => 'Le libellé du module est requis'], 400);
        }

        $code = $libelle; // Use libelle as code if code_module is not provided
        // $code = strtoupper(trim($this->post('code_module') ?? ''));
        // if ($code === '') {
        //     $code = preg_replace('/[^A-Z0-9_]/', '_', strtoupper($libelle));
        // }

        $model = new ModelModuleMetier();
        if ($model->exists('code_module', $code)) {
            $this->json(['status' => 0, 'message' => 'Ce module existe déjà'], 409);
        }

        $data = [
            'code_module' => $code,
            'libelle_module' => $libelle,
            'statut_module' => 'actif',
            'created_at_module' => date('Y-m-d H:i:s')
        ];

        if ($model->create($data)) {
            $this->json(['status' => 1, 'message' => 'Module créé avec succès', 'code_module' => $code, 'libelle_module' => $libelle]);
        } else {
            $this->json(['status' => 0, 'message' => 'Erreur lors de la création du module']);
        }
    }
}