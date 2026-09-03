<?php

class TypeDepenseController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelTypeDepense();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/type_depenses/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $items = $this->model->getAll();
        $data = [];

        foreach ($items as $td) {
            $id = $td['id_type_depense'];
            $idCrypte = $this->validator->crypter($id);
            $data[] = array_merge($td, [
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

        if (empty($data['libelle_type_depense'])) {
            $this->error('Veuillez renseigner le libellé !');
            return;
        }

        $etabCode = '5454544456';
        if (empty($data['code_type_depense'])) {
            $data['code_type_depense'] = $this->validator->generateCode('type_depenses', 'code_type_depense', 'TDP-', 6);
        }
        $data['statut_type_depense'] = $data['statut_type_depense'] ?? 'actif';
        $data['created_at_type_depense'] = date('Y-m-d H:i:s');

        $cols = $this->model->getCon()->query("DESCRIBE type_depenses")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('etablissement_code', $cols)) $data['etablissement_code'] = $etabCode;

        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->create($filteredData)) {
            $this->success('Type de dépense ajouté avec succès!');
        } else {
            $this->error('Erreur lors de l\'ajout du type de dépense');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id_type_depense');
        if (!$id) { $this->error('Identifiant invalide'); return; }
        $data = $_POST;
        unset($data['csrf_token']);

        $data['updated_at_type_depense'] = date('Y-m-d H:i:s');
        $cols = $this->model->getCon()->query("DESCRIBE type_depenses")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->update($filteredData, $id)) {
            $this->success('Type de dépense modifié avec succès!');
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
            $this->error('Type de dépense introuvable');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) { header('Location: ' . RACINE . 'type_depense/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'type_depense/list'); exit();
        }
        $this->loadView('../views/type_depenses/edit.php', ['item' => $item, 'encryptedId' => $encryptedId]);
    }

    public function edition($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) { header('Location: ' . RACINE . 'type_depense/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'type_depense/list'); exit();
        }
        $this->loadView('../views/type_depenses/edit.php', ['item' => $item, 'encryptedId' => $encryptedId]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $this->loadView('../views/type_depenses/edit.php', ['item' => []]);
    }
}