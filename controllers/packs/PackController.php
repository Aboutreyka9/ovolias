<?php

class PackController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelPack();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/packs/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $sql = "
            SELECT p.*, 
                   c.libelle_categorie_pack,
                   s.libelle_session,
                   s.nombre_jour_session,
                   z.libelle_zone,
                   a.libelle_annee
            FROM packs p
            LEFT JOIN categorie_packs c ON c.code_categorie_pack = p.categorie_pack_code
            LEFT JOIN sessions s ON s.code_session = p.session_code
            LEFT JOIN zones z ON z.code_zone = p.zone_code
            LEFT JOIN annees a ON a.code_annee = p.annee_code
            ORDER BY p.annee_code ASC, p.zone_code ASC, p.id_pack DESC
        ";
        $items = $this->model->getCon()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $data = [];

        foreach ($items as $i) {
            $id = $i['id_pack'];
            $idCrypte = $this->validator->crypter($id);
            $prixCotis = (float)($i['prix_cotisation_pack'] ?? 0);
            $nbJours = (int)($i['nombre_jour_session'] ?? 0);
            $montantTotal = $prixCotis * $nbJours;

            $data[] = array_merge($i, [
                'id' => $id,
                'editId' => $idCrypte,
                'libelle_annee' => $i['libelle_annee'] ?? ($i['annee_code'] ?? '-'),
                'libelle_categorie' => $i['libelle_categorie_pack'] ?? ($i['categorie_pack_code'] ?? '-'),
                'libelle_session' => $i['libelle_session'] ?? ($i['session_code'] ?? '-'),
                'libelle_zone' => $i['libelle_zone'] ?? ($i['zone_code'] ?? '-'),
                'nombre_jour_session' => $nbJours,
                'montant_total' => $montantTotal
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

        if (!empty($data['libelle_pack'])) {
            $conditions = [
                'session_code' => $data['session_code'] ?? '',
                'categorie_pack_code' => $data['categorie_pack_code'] ?? '',
                'zone_code' => $data['zone_code'] ?? '',
                'libelle_pack' => $data['libelle_pack'] ?? ''
            ];
            if (!$this->checkUniquePair('packs', $conditions, 'Pack (Session + Catégorie + Zone + Nom)')) return;
        }

        $userCode = Context::user() ?? '';
        $anneeCode = Context::annee();
        $etabCode = Context::etablissement();

        if (empty($data['code_pack'])) {
            $data['code_pack'] = $this->validator->generateCode('packs', 'code_pack', 'PCK-', 8);
        }
        $data['statut_pack'] = $data['statut_pack'] ?? 'actif';
        $data['created_at_pack'] = date('Y-m-d H:i:s');
        if (isset($data['montant_pack'])) {
            unset($data['montant_pack']);
        }

        if (isset($_FILES['image_pack']) && $_FILES['image_pack']['error'] === UPLOAD_ERR_OK && !empty($_FILES['image_pack']['name'])) {
            $uploadDir = __DIR__ . '/../../public/assets/images/packs/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['image_pack']['name'], PATHINFO_EXTENSION));
            $filename = 'pack_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image_pack']['tmp_name'], $uploadDir . $filename)) {
                $data['image_pack'] = $filename;
            }
        }

        $cols = $this->model->getCon()->query("DESCRIBE packs")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('user_code', $cols)) $data['user_code'] = $userCode;
        if (in_array('etablissement_code', $cols)) $data['etablissement_code'] = $etabCode;
        if (in_array('annee_code', $cols) && empty($data['annee_code'])) $data['annee_code'] = $anneeCode;

        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->create($filteredData)) {
            // Traiter la liste des articles du pack s'il y en a
            if (!empty($_POST['articles']) && is_array($_POST['articles'])) {
                $this->model->syncArticles($data['code_pack'], $_POST['articles'], $anneeCode, $etabCode);
            }
            $this->success('Pack créé avec succès!');
        } else {
            $this->error('Erreur lors de la création du pack');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id_pack');
        if (!$id) { $this->error('Identifiant invalide'); return; }
        $data = $_POST;
        unset($data['csrf_token']);

        $pack = $this->model->getById($id);
        if (!$pack) { $this->error('Pack introuvable'); return; }

        if (!empty($data['libelle_pack'])) {
            $conditions = [
                'session_code' => $data['session_code'] ?? $pack['session_code'],
                'categorie_pack_code' => $data['categorie_pack_code'] ?? $pack['categorie_pack_code'],
                'zone_code' => $data['zone_code'] ?? $pack['zone_code'],
                'libelle_pack' => $data['libelle_pack'] ?? $pack['libelle_pack']
            ];
            if (!$this->checkUniquePair('packs', $conditions, 'Pack (Session + Catégorie + Zone + Nom)', 'id_pack', $id)) return;
        }

        $data['updated_at_pack'] = date('Y-m-d H:i:s');
        if (isset($data['montant_pack'])) {
            unset($data['montant_pack']);
        }

        if (isset($_FILES['image_pack']) && $_FILES['image_pack']['error'] === UPLOAD_ERR_OK && !empty($_FILES['image_pack']['name'])) {
            $uploadDir = __DIR__ . '/../../public/assets/images/packs/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['image_pack']['name'], PATHINFO_EXTENSION));
            $filename = 'pack_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image_pack']['tmp_name'], $uploadDir . $filename)) {
                $data['image_pack'] = $filename;
            }
        }

        $cols = $this->model->getCon()->query("DESCRIBE packs")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->update($filteredData, $id)) {
            if (isset($_POST['articles']) && is_array($_POST['articles'])) {
                $anneeCode = Context::annee();
                $etabCode = Context::etablissement();
                $this->model->syncArticles($pack['code_pack'], $_POST['articles'], $anneeCode, $etabCode);
            }
            $this->success('Pack modifié avec succès!');
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
            $this->error('Pack introuvable');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) {
                $this->renderNotFound("Le pack demandé est introuvable.");
                return;
            }
            $packArticles = $this->model->getArticles($item['code_pack']);
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            $this->renderNotFound("Le pack demandé est introuvable.");
            return;
        }
        $this->loadView('../views/packs/details.php', [
            'item' => $item,
            'packArticles' => $packArticles,
            'encryptedId' => $encryptedId
        ]);
    }

    public function edition($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) { header('Location: ' . RACINE . 'pack/list'); exit(); }
            $packArticles = $this->model->getArticles($item['code_pack']);
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'pack/list'); exit();
        }

        $categories = $this->model->getCon()->query("SELECT code_categorie_pack, libelle_categorie_pack FROM categorie_packs WHERE statut_categorie_pack='actif'")->fetchAll(PDO::FETCH_ASSOC);
        $sessions = $this->model->getCon()->query("SELECT code_session, libelle_session, nombre_jour_session FROM sessions WHERE statut_session='actif'")->fetchAll(PDO::FETCH_ASSOC);
        $zones = $this->model->getCon()->query("SELECT code_zone, libelle_zone FROM zones WHERE statut_zone='actif'")->fetchAll(PDO::FETCH_ASSOC);
        $articles = $this->model->getCon()->query("SELECT code_article, libelle_article FROM articles WHERE statut_article='actif'")->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/packs/edit.php', [
            'item' => $item,
            'packArticles' => $packArticles,
            'categories' => $categories,
            'sessions' => $sessions,
            'zones' => $zones,
            'articles' => $articles,
            'encryptedId' => $encryptedId
        ]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $categories = $this->model->getCon()->query("SELECT code_categorie_pack, libelle_categorie_pack FROM categorie_packs WHERE statut_categorie_pack='actif'")->fetchAll(PDO::FETCH_ASSOC);
        $sessions = $this->model->getCon()->query("SELECT code_session, libelle_session, nombre_jour_session FROM sessions WHERE statut_session='actif'")->fetchAll(PDO::FETCH_ASSOC);
        $zones = $this->model->getCon()->query("SELECT code_zone, libelle_zone FROM zones WHERE statut_zone='actif'")->fetchAll(PDO::FETCH_ASSOC);
        $articles = $this->model->getCon()->query("SELECT code_article, libelle_article FROM articles WHERE statut_article='actif'")->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/packs/edit.php', [
            'item' => [],
            'packArticles' => [],
            'categories' => $categories,
            'sessions' => $sessions,
            'zones' => $zones,
            'articles' => $articles
        ]);
    }
}
