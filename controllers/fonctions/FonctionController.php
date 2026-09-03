<?php

class FonctionController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelFonction();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/fonctions/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $items = $this->model->getAll();
        $data = [];
        foreach ($items as $i) {
            $id = $i['id_fonction'];
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
        $userCode = Context::user() ?? '';
        $anneeCode = Context::annee();
        $etabCode = '5454544456';
        $data = $_POST;
        unset($data['csrf_token']);

        if (!empty($data['libelle_fonction'])) {
            if (!$this->checkUnique('fonctions', 'libelle_fonction', $data['libelle_fonction'], 'Intitulé de la fonction')) return;
        }

        if (empty($data['code_fonction'])) {
            $data['code_fonction'] = $this->validator->generateCode('fonctions', 'code_fonction', 'FCT-', 8);
        }
        $data['statut_fonction'] = $data['statut_fonction'] ?? 'actif';
        $data['created_at_fonction'] = date('Y-m-d H:i:s');

        $cols = $this->model->getCon()->query("DESCRIBE fonctions")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('user_code', $cols)) $data['user_code'] = $userCode;
        if (in_array('etablissement_code', $cols)) $data['etablissement_code'] = $etabCode;
        if (in_array('annee_code', $cols)) $data['annee_code'] = $anneeCode;
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->create($filteredData)) {
            $this->success('Fonction créée avec succès!');
        } else {
            $this->error('Erreur lors de la création');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id_fonction');
        if (!$id) { $this->error('Identifiant invalide'); return; }
        $data = $_POST;
        unset($data['csrf_token']);

        if (!empty($data['libelle_fonction'])) {
            if (!$this->checkUnique('fonctions', 'libelle_fonction', $data['libelle_fonction'], 'Intitulé de la fonction', 'id_fonction', $id)) return;
        }

        $cols = $this->model->getCon()->query("DESCRIBE fonctions")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->update($filteredData, $id)) {
            $this->success('Fonction modifiée avec succès!');
        } else {
            $this->error('Erreur lors de la modification');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) { 
                $this->renderNotFound("La fonction demandée est introuvable.");
                return;
            }

            // Utilisateurs / Personnel occupant cette fonction
            $stmtUsers = $this->model->getCon()->prepare("
                SELECT u.*, r.libelle_role 
                FROM users u
                LEFT JOIN user_roles ur ON ur.user_code = u.code_user
                LEFT JOIN roles r ON r.code_role = ur.role_code
                WHERE u.fonction_code = ?
                ORDER BY u.nom_user ASC, u.prenom_user ASC
            ");
            $stmtUsers->execute([$item['code_fonction']]);
            $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            error_log("FonctionController::details error: " . $e->getMessage());
            $this->renderNotFound("La fonction demandée est introuvable.");
            return;
        }
        $this->loadView('../views/fonctions/details.php', [
            'item' => $item, 
            'users' => $users,
            'encryptedId' => $encryptedId
        ]);
    }

    public function edition($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) { header('Location: ' . RACINE . 'fonction/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'fonction/list'); exit();
        }
        $this->loadView('../views/fonctions/edit.php', ['item' => $item, 'encryptedId' => $encryptedId]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $this->loadView('../views/fonctions/edit.php', ['item' => []]);
    }

    public function changer()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id');
        $statut = $this->post('statut') ?: $this->post('status');
        if ($id && $this->model->getById($id)) {
            $success = !empty($statut) ? $this->model->updateStatus($id, $statut, 'statut_fonction') : $this->model->toggleStatus($id);
            if ($success) {
                $this->success('Statut mis à jour avec succès!', ['reload' => true]);
            } else {
                $this->error('Erreur lors de la mise à jour du statut');
            }
        } else {
            $this->error('Fonction introuvable');
        }
    }
}
