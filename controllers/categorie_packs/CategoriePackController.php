<?php

class CategoriePackController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelCategoriePack();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/categorie_packs/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $items = $this->model->getAll();
        $data = [];
        foreach ($items as $i) {
            $id = $i['id_categorie_pack'];
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

        if (!empty($data['libelle_categorie_pack'])) {
            if (!$this->checkUnique('categorie_packs', 'libelle_categorie_pack', $data['libelle_categorie_pack'], 'Catégorie de pack')) return;
        }

        $userCode = Context::user() ?? '';
        $etabCode = '5454544456';

        if (empty($data['code_categorie_pack'])) {
            $data['code_categorie_pack'] = $this->validator->generateCode('categorie_packs', 'code_categorie_pack', 'CPK-', 8);
        }
        $data['statut_categorie_pack'] = $data['statut_categorie_pack'] ?? 'actif';
        $data['created_at_categorie_pack'] = date('Y-m-d H:i:s');

        $cols = $this->model->getCon()->query("DESCRIBE categorie_packs")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('user_code', $cols)) $data['user_code'] = $userCode;
        if (in_array('etablissement_code', $cols)) $data['etablissement_code'] = $etabCode;

        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->create($filteredData)) {
            $this->success('Catégorie créée avec succès!');
        } else {
            $this->error('Erreur lors de la création de la catégorie');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id_categorie_pack');
        if (!$id) { $this->error('Identifiant invalide'); return; }
        $data = $_POST;
        unset($data['csrf_token']);

        if (!empty($data['libelle_categorie_pack'])) {
            if (!$this->checkUnique('categorie_packs', 'libelle_categorie_pack', $data['libelle_categorie_pack'], 'Catégorie de pack', 'id_categorie_pack', $id)) return;
        }

        $data['updated_at_categorie_pack'] = date('Y-m-d H:i:s');
        $cols = $this->model->getCon()->query("DESCRIBE categorie_packs")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->update($filteredData, $id)) {
            $this->success('Catégorie modifiée avec succès!');
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
            $this->error('Catégorie introuvable');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) {
                $this->renderNotFound("La catégorie demandée est introuvable.");
                return;
            }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            $this->renderNotFound("La catégorie demandée est introuvable.");
            return;
        }
        $this->loadView('../views/categorie_packs/details.php', [
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
            if (!$item) { header('Location: ' . RACINE . 'categorie_pack/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'categorie_pack/list'); exit();
        }
        $this->loadView('../views/categorie_packs/edit.php', ['item' => $item, 'encryptedId' => $encryptedId]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $this->loadView('../views/categorie_packs/edit.php', ['item' => []]);
    }
}
