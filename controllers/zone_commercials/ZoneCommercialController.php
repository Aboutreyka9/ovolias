<?php

class ZoneCommercialController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelZoneCommercial();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/zone_commercials/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $items = $this->model->getAllWithDetails();
        $data = [];

        foreach ($items as $i) {
            $id = $i['id_zone_commercial'];
            $idCrypte = $this->validator->crypter($id);
            $data[] = array_merge($i, [
                'id' => $id,
                'editId' => $idCrypte,
                'nom_commercial' => trim(($i['nom_user'] ?? '') . ' ' . ($i['prenom_user'] ?? '')) ?: $i['commercial_code']
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

        if (empty($data['commercial_code']) || empty($data['zone_code'])) {
            $this->error('Veuillez sélectionner un commercial et une zone !');
            return;
        }

        $etabCode = '5454544456';
        $data['statut_zone_commercial'] = $data['statut_zone_commercial'] ?? 'actif';
        $data['created_at_zone_commercial'] = date('Y-m-d H:i:s');

        $cols = $this->model->getCon()->query("DESCRIBE zone_commercials")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('etablissement_code', $cols)) $data['etablissement_code'] = $etabCode;

        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->create($filteredData)) {
            $this->success('Affectation créée avec succès!');
        } else {
            $this->error('Erreur lors de la création de l\'affectation');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id_zone_commercial');
        if (!$id) { $this->error('Identifiant invalide'); return; }
        $data = $_POST;
        unset($data['csrf_token']);

        $data['updated_at_zone_commercial'] = date('Y-m-d H:i:s');
        $cols = $this->model->getCon()->query("DESCRIBE zone_commercials")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->update($filteredData, $id)) {
            $this->success('Affectation modifiée avec succès!');
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
            $this->error('Affectation introuvable');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) { header('Location: ' . RACINE . 'zone_commercial/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'zone_commercial/list'); exit();
        }
        $this->loadView('../views/zone_commercials/edit.php', ['item' => $item, 'encryptedId' => $encryptedId]);
    }

    public function edition($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) { header('Location: ' . RACINE . 'zone_commercial/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'zone_commercial/list'); exit();
        }
        $zones = $this->model->getCon()->query("SELECT code_zone, libelle_zone FROM zones WHERE statut_zone='actif'")->fetchAll(PDO::FETCH_ASSOC);
        $commerciaux = $this->model->getCon()->query("SELECT code_user, nom_user, prenom_user FROM users WHERE statut_user='actif'")->fetchAll(PDO::FETCH_ASSOC);
        $this->loadView('../views/zone_commercials/edit.php', ['item' => $item, 'zones' => $zones, 'commerciaux' => $commerciaux, 'encryptedId' => $encryptedId]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $zones = $this->model->getCon()->query("SELECT code_zone, libelle_zone FROM zones WHERE statut_zone='actif'")->fetchAll(PDO::FETCH_ASSOC);
        $commerciaux = $this->model->getCon()->query("SELECT code_user, nom_user, prenom_user FROM users WHERE statut_user='actif'")->fetchAll(PDO::FETCH_ASSOC);
        $this->loadView('../views/zone_commercials/edit.php', ['item' => [], 'zones' => $zones, 'commerciaux' => $commerciaux]);
    }
}
