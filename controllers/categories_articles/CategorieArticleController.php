<?php

class CategorieArticleController extends BaseController
{
    use PressingAware;

    protected function resolveModel()
    {
        return new ModelCategorieArticle();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/categories_articles/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $categories = $this->model->getAll();
        $data = [];

        foreach ($categories as $c) {
            $idCrypte = $this->validator->crypter($c['id_categorie_article']);
            $icon = $c['icon_categorie_article'] ?? '';
            $data[] = [
                'code' => $c['code_categorie_article'],
                'libelle' => $c['libelle_categorie_article'],
                'description' => $c['description_categorie_article'] ?? '',
                'icon' => $icon,
                'statut' => $c['statut_categorie_article'],
                'id' => $c['id_categorie_article'],
                'editId' => $idCrypte
            ];
        }

        $this->json(['data' => $data]);
    }

    private function handleIconUpload(?string $existingIcon = null): ?string
    {
        $fileKey = null;
        if (isset($_FILES['icon_file']) && $_FILES['icon_file']['error'] === UPLOAD_ERR_OK) {
            $fileKey = 'icon_file';
        } elseif (isset($_FILES['icon_categorie_article']) && is_array($_FILES['icon_categorie_article']) && $_FILES['icon_categorie_article']['error'] === UPLOAD_ERR_OK) {
            $fileKey = 'icon_categorie_article';
        }

        if ($fileKey !== null) {
            $file = $_FILES[$fileKey];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif', 'ico'];

            if (!in_array($ext, $allowedExtensions, true)) {
                throw new InvalidArgumentException("Format d'image non autorisé. Extensions acceptées : " . implode(', ', $allowedExtensions));
            }

            if ($file['size'] > 5 * 1024 * 1024) {
                throw new InvalidArgumentException("L'icône ne doit pas dépasser 5 Mo.");
            }

            $uploadDir = __DIR__ . '/../../public/assets/images/categories/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = 'cat_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                return 'assets/images/categories/' . $newFileName;
            } else {
                throw new RuntimeException("Erreur lors de l'enregistrement de l'icône sur le serveur.");
            }
        }

        $postedIcon = trim($_POST['icon_categorie_article'] ?? '');
        if ($postedIcon !== '') {
            return $postedIcon;
        }

        return $existingIcon;
    }

    public function add()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $this->requireActiveAbonnement(null, 'créer des catégories');

        $notEmpty = Validator::validateRequiredFields(['libelle_categorie_article' => $_POST['libelle_categorie_article'] ?? '']);

        if ($notEmpty !== true) {
            $this->error('Veuillez renseigner tous les champs!');
            return;
        }

        $libelle = $this->post('libelle_categorie_article');
        if (!$this->checkUnique(TABLES::CATEGORIES_ARTICLES, 'libelle_categorie_article', $libelle, 'nom de catégorie')) return;

        $code = $this->post('code_categorie_article') ?: $this->validator->generateCode(TABLES::CATEGORIES_ARTICLES, 'code_categorie_article', 'CAT-', 6);
        if ($this->validator->getByElement(TABLES::CATEGORIES_ARTICLES, 'code_categorie_article', $code)) {
            $this->error('Ce code catégorie existe déjà !');
            return;
        }

        try {
            $iconPath = $this->handleIconUpload();
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return;
        }

        $data = [
            'code_categorie_article' => $code,
            'libelle_categorie_article' => $this->post('libelle_categorie_article'),
            'description_categorie_article' => $this->post('description_categorie_article') ?? '',
            'icon_categorie_article' => $iconPath ?? '',
            'statut_categorie_article' => 'actif',
            'created_at_categorie_article' => date('Y-m-d H:i:s')
        ];

        if ($this->model->create($data)) {
            $this->success('Catégorie ajoutée avec succès!');
        } else {
            $this->error('Erreur lors de l\'ajout');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $this->requireActiveAbonnement(null, 'modifier des catégories');

        $notEmpty = Validator::validateRequiredFields(['libelle_categorie_article' => $_POST['libelle_categorie_article'] ?? '', 'id_categorie_article' => $_POST['id_categorie_article'] ?? '']);

        if ($notEmpty !== true) {
            $this->error('Veuillez renseigner tous les champs!');
            return;
        }

        $id = (int) $this->post('id_categorie_article');
        $current = $this->model->getById($id);
        if (!$current) {
            $this->error('Catégorie introuvable');
            return;
        }

        try {
            $iconPath = $this->handleIconUpload($current['icon_categorie_article'] ?? null);
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return;
        }

        $statut = ($this->post('actif') == 1) ? 'actif' : 'inactif';

        $data = [
            'id_categorie_article' => $id,
            'libelle_categorie_article' => $this->post('libelle_categorie_article'),
            'description_categorie_article' => $this->post('description_categorie_article') ?? '',
            'icon_categorie_article' => $iconPath ?? '',
            'statut_categorie_article' => $statut,
            'updated_at_categorie_article' => date('Y-m-d H:i:s')
        ];

        if ($this->model->update($data)) {
            $this->success('Catégorie modifiée avec succès!');
        } else {
            $this->error('Erreur lors de la modification');
        }
    }

    public function changer()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $this->requireActiveAbonnement(null, 'activer ou désactiver des catégories');

        $id = $this->post('id');
        if (isset($id) && $this->model->getById($id)) {
            if ($this->model->toggleStatus($id)) {
                $this->success('Statut modifié avec succès!', ['id' => $id, 'reload' => true]);
            } else {
                $this->error('Erreur');
            }
        } else {
            $this->error('Catégorie introuvable!');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) {
                header('Location: ' . RACINE . 'categorie/list');
                exit();
            }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'categorie/list');
            exit();
        }

        $this->loadView('../views/categories_articles/details.php', [
            'categorie' => $item,
            'encryptedId' => $encryptedId
        ]);
    }

    public function edition($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) {
                header('Location: ' . RACINE . 'categorie/list');
                exit();
            }
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'categorie/list');
            exit();
        }

        $this->loadView('../views/categories_articles/edit.php', [
            'categorie' => $item
        ]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $this->loadView('../views/categories_articles/edit.php', [
            'categorie' => []
        ]);
    }
}
