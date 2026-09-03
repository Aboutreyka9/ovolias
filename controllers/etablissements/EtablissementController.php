<?php

class EtablissementController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelEtablissement();
    }

    private function getSingleItem()
    {
        $items = $this->model->getAll();
        return !empty($items) ? $items[0] : [
            'id_etablissement' => 1,
            'libelle_etablissement' => 'Institut Supérieur GEICG',
            'numero_autorisation_etablissement' => '',
            'telephone_etablissement' => '0708091011',
            'telephone_etablissement2' => '0102030405',
            'email_etablissement' => 'contact@geicg.ci',
            'slogan_etablissement' => 'L\'Excellence au Service de l\'Avenir',
            'adresse_etablissement' => 'Abidjan Cocody Angré 8ème Tranche',
            'statut_etablissement' => 'actif'
        ];
    }

    public function list()
    {
        $this->requireAuth();
        $item = $this->getSingleItem();
        $this->loadView('../views/etablissements/config.php', ['item' => $item]);
    }

    public function config()
    {
        $this->requireAuth();
        $item = $this->getSingleItem();
        $this->loadView('../views/etablissements/config.php', ['item' => $item]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $item = $this->getSingleItem();
        $this->loadView('../views/etablissements/config.php', ['item' => $item]);
    }

    public function edition($details)
    {
        $this->requireAuth();
        $item = $this->getSingleItem();
        $this->loadView('../views/etablissements/config.php', ['item' => $item]);
    }

    public function details($details)
    {
        $this->requireAuth();
        $item = $this->getSingleItem();
        $this->loadView('../views/etablissements/config.php', ['item' => $item]);
    }

    public function apiList()
    {
        $this->requireAuth();
        $item = $this->getSingleItem();
        $id = $item['id_etablissement'] ?? 1;
        $idCrypte = $this->validator->crypter($id);
        $this->json(['data' => [array_merge($item, ['id' => $id, 'editId' => $idCrypte])]]);
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id_etablissement');
        $data = $_POST;
        unset($data['csrf_token']);
        $this->cleanPhoneFields($data);

        if (!empty($_FILES['logo_file']['name']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/logos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            if (in_array($ext, $allowedExts)) {
                $filename = 'logo_' . time() . '.' . $ext;
                $targetFile = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $targetFile)) {
                    $data['logo_etablissement'] = 'public/uploads/logos/' . $filename;
                }
            }
        }

        $cols = $this->model->getCon()->query("DESCRIBE etablissements")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($id && $this->model->getById($id)) {
            if ($this->model->update($filteredData, $id)) {
                if ($isAjax) {
                    $this->success('Configuration de l\'établissement mise à jour avec succès!');
                } else {
                    $_SESSION['flash_success'] = 'Configuration de l\'établissement mise à jour avec succès!';
                    header('Location: ' . RACINE . 'etablissement/config');
                    exit();
                }
            } else {
                if ($isAjax) {
                    $this->error('Erreur lors de la mise à jour de la configuration');
                } else {
                    $_SESSION['flash_error'] = 'Erreur lors de la mise à jour de la configuration';
                    header('Location: ' . RACINE . 'etablissement/config');
                    exit();
                }
            }
        } else {
            $filteredData['code_etablissement'] = $this->validator->generateCode('etablissements', 'code_etablissement', 'ETA-', 8);
            $filteredData['statut_etablissement'] = 'actif';
            $filteredData['created_at_etablissement'] = date('Y-m-d H:i:s');
            if ($this->model->create($filteredData)) {
                if ($isAjax) {
                    $this->success('Configuration enregistrée avec succès!');
                } else {
                    $_SESSION['flash_success'] = 'Configuration enregistrée avec succès!';
                    header('Location: ' . RACINE . 'etablissement/config');
                    exit();
                }
            } else {
                if ($isAjax) {
                    $this->error('Erreur lors de l\'enregistrement');
                } else {
                    $_SESSION['flash_error'] = 'Erreur lors de l\'enregistrement';
                    header('Location: ' . RACINE . 'etablissement/config');
                    exit();
                }
            }
        }
    }

    public function changer()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id');
        $statut = $this->post('statut') ?: $this->post('status');
        if ($id && $this->model->getById($id)) {
            $success = !empty($statut) ? $this->model->updateStatus($id, $statut, 'statut_etablissement') : $this->model->toggleStatus($id);
            if ($success) {
                $this->success('Statut mis à jour avec succès!', ['reload' => true]);
            } else {
                $this->error('Erreur lors de la mise à jour du statut');
            }
        } else {
            $this->error('Établissement introuvable');
        }
    }
}
