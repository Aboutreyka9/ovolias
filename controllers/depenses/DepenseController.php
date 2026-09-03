<?php

class DepenseController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelDepense();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/depenses/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $items = $this->model->getAllWithDetails();
        $data = [];

        foreach ($items as $d) {
            $id = $d['id_depense'];
            $idCrypte = $this->validator->crypter($id);
            $data[] = array_merge($d, [
                'id' => $id,
                'editId' => $idCrypte,
                'nom_auteur_complet' => trim(($d['nom_user'] ?? '') . ' ' . ($d['prenom_user'] ?? ''))
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

        if (empty($data['type_depense_code']) || empty($data['montant_depense'])) {
            $this->error('Veuillez renseigner le type de dépense et le montant !');
            return;
        }

        $userCode = Context::user() ?? '';
        $anneeCode = Context::annee();
        $etabCode = '5454544456';
        $codeDepense = $this->validator->generateCode('depenses', 'code_depense', 'DEP-', 8);

        // Upload pièce justificative si existante
        $filename = null;
        if (!empty($_FILES['piece_justificative']['name'])) {
            $uploadDir = __DIR__ . '/../../public/assets/images/depenses/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = pathinfo($_FILES['piece_justificative']['name'], PATHINFO_EXTENSION);
            $filename = 'pj_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['piece_justificative']['tmp_name'], $uploadDir . $filename);
        }

        $statutInitial = Context::isCommercial() ? 'inactif' : 'actif';

        $depenseData = [
            'code_depense' => $codeDepense,
            'type_depense_code' => $data['type_depense_code'],
            'description_depense' => $data['description_depense'] ?? ($data['motif_depense'] ?? ''),
            'montant_depense' => (float)$data['montant_depense'],
            'periode_depense' => !empty($data['periode_depense']) ? $data['periode_depense'] : (!empty($data['date_depense']) ? $data['date_depense'] . ' ' . date('H:i:s') : date('Y-m-d H:i:s')),
            'annee_code' => $anneeCode,
            'etablissement_code' => $etabCode,
            'user_code' => $userCode,
            'statut_depense' => $data['statut_depense'] ?? $statutInitial,
            'created_at_depense' => date('Y-m-d H:i:s')
        ];

        $cols = $this->model->getCon()->query("DESCRIBE depenses")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($depenseData, array_flip($cols));

        if ($this->model->create($filteredData)) {
            $this->success('Dépense enregistrée avec succès !', ['code' => $codeDepense]);
        } else {
            $this->error('Erreur lors de l\'enregistrement de la dépense');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id_depense');
        if (!$id) { $this->error('Identifiant invalide'); return; }
        $data = $_POST;
        unset($data['csrf_token']);

        $data['updated_at_depense'] = date('Y-m-d H:i:s');
        $cols = $this->model->getCon()->query("DESCRIBE depenses")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->update($filteredData, $id)) {
            $this->success('Dépense modifiée avec succès!');
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
            $this->error('Dépense introuvable');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) {
                $this->renderNotFound("La dépense demandée est introuvable.");
                return;
            }

            $stmtTd = $this->model->getCon()->prepare("SELECT * FROM type_depenses WHERE code_type_depense = ?");
            $stmtTd->execute([$item['type_depense_code']]);
            $typeDepense = $stmtTd->fetch(PDO::FETCH_ASSOC);

            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            $this->renderNotFound("La dépense demandée est introuvable.");
            return;
        }
        $this->loadView('../views/depenses/details.php', [
            'item' => $item,
            'typeDepense' => $typeDepense,
            'encryptedId' => $encryptedId
        ]);
    }

    public function edition($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) { header('Location: ' . RACINE . 'depense/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'depense/list'); exit();
        }
        $typeDepenses = $this->model->getCon()->query("SELECT code_type_depense, libelle_type_depense FROM type_depenses ORDER BY libelle_type_depense ASC")->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/depenses/edit.php', [
            'item' => $item,
            'typeDepenses' => $typeDepenses,
            'encryptedId' => $encryptedId
        ]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $typeDepenses = $this->model->getCon()->query("SELECT code_type_depense, libelle_type_depense FROM type_depenses ORDER BY libelle_type_depense ASC")->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/depenses/edit.php', [
            'item' => [],
            'typeDepenses' => $typeDepenses
        ]);
    }
}
