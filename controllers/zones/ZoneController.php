<?php

class ZoneController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelZone();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/zones/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $items = $this->model->getAll();
        $data = [];
        foreach ($items as $i) {
            $id = $i['id_zone'];
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

        if (!empty($data['libelle_zone'])) {
            if (!$this->checkUnique('zones', 'libelle_zone', $data['libelle_zone'], 'Zone commerciale')) return;
        }

        $userCode = Context::user() ?? '';
        $etabCode = '5454544456';

        if (empty($data['code_zone'])) {
            $data['code_zone'] = $this->validator->generateCode('zones', 'code_zone', 'ZON-', 8);
        }
        $data['statut_zone'] = $data['statut_zone'] ?? 'actif';
        $data['created_at_zone'] = date('Y-m-d H:i:s');

        $cols = $this->model->getCon()->query("DESCRIBE zones")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('user_code', $cols)) $data['user_code'] = $userCode;
        if (in_array('etablissement_code', $cols)) $data['etablissement_code'] = $etabCode;

        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->create($filteredData)) {
            $this->success('Zone créée avec succès!');
        } else {
            $this->error('Erreur lors de la création de la zone');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id_zone');
        if (!$id) { $this->error('Identifiant invalide'); return; }
        $data = $_POST;
        unset($data['csrf_token']);

        if (!empty($data['libelle_zone'])) {
            if (!$this->checkUnique('zones', 'libelle_zone', $data['libelle_zone'], 'Zone commerciale', 'id_zone', $id)) return;
        }

        $data['updated_at_zone'] = date('Y-m-d H:i:s');
        $cols = $this->model->getCon()->query("DESCRIBE zones")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->update($filteredData, $id)) {
            $this->success('Zone modifiée avec succès!');
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
            $this->error('Zone introuvable');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) {
                $this->renderNotFound("La zone demandée est introuvable.");
                return;
            }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            $this->renderNotFound("La zone demandée est introuvable.");
            return;
        }
        $this->loadView('../views/zones/details.php', [
            'item' => $item,
            'encryptedId' => $encryptedId
        ]);
    }

    public function edition($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) { header('Location: ' . RACINE . 'zone/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'zone/list'); exit();
        }
        $this->loadView('../views/zones/edit.php', ['item' => $item, 'encryptedId' => $encryptedId]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $this->loadView('../views/zones/edit.php', ['item' => []]);
    }
}
