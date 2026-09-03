<?php

class ArticleController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelArticle();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/articles/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $items = $this->model->getAll();
        $data = [];

        foreach ($items as $a) {
            $id = $a['id_article'];
            $idCrypte = $this->validator->crypter($id);
            $data[] = array_merge($a, [
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

        if (!empty($data['libelle_article'])) {
            if (!$this->checkUnique('articles', 'libelle_article', $data['libelle_article'], 'Article')) return;
        }

        $userCode = Context::user() ?? '';
        $etabCode = Context::etablissement();

        if (empty($data['code_article'])) {
            $data['code_article'] = $this->validator->generateCode('articles', 'code_article', 'ART-', 8);
        }
        $data['statut_article'] = $data['statut_article'] ?? 'actif';
        $data['created_at_article'] = date('Y-m-d H:i:s');

        $cols = $this->model->getCon()->query("DESCRIBE articles")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('user_code', $cols)) $data['user_code'] = $userCode;
        if (in_array('etablissement_code', $cols)) $data['etablissement_code'] = $etabCode;

        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->create($filteredData)) {
            $this->success('Article créé avec succès!');
        } else {
            $this->error('Erreur lors de la création de l\'article');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id_article');
        if (!$id) { $this->error('Identifiant invalide'); return; }
        $data = $_POST;
        unset($data['csrf_token']);

        if (!empty($data['libelle_article'])) {
            if (!$this->checkUnique('articles', 'libelle_article', $data['libelle_article'], 'Article', 'id_article', $id)) return;
        }

        $data['updated_at_article'] = date('Y-m-d H:i:s');
        $cols = $this->model->getCon()->query("DESCRIBE articles")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->update($filteredData, $id)) {
            $this->success('Article modifié avec succès!');
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
            $this->error('Article introuvable');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) {
                $this->renderNotFound("L'article demandé est introuvable.");
                return;
            }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            $this->renderNotFound("L'article demandé est introuvable.");
            return;
        }
        $this->loadView('../views/articles/details.php', [
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
            if (!$item) { header('Location: ' . RACINE . 'article/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'article/list'); exit();
        }
        $this->loadView('../views/articles/edit.php', ['item' => $item, 'encryptedId' => $encryptedId]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $this->loadView('../views/articles/edit.php', ['item' => []]);
    }
}