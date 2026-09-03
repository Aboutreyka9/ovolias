<?php

class DistributionController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelDistribution();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/distributions/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $items = $this->model->getAllWithDetails();
        $data = [];

        foreach ($items as $d) {
            $id = $d['id_distribution'];
            $idCrypte = $this->validator->crypter($id);
            $data[] = array_merge($d, [
                'id' => $id,
                'editId' => $idCrypte,
                'nom_client_complet' => trim(($d['nom_client'] ?? '') . ' ' . ($d['prenom_client'] ?? '')),
                'nom_livreur_complet' => trim(($d['nom_livreur'] ?? '') . ' ' . ($d['prenom_livreur'] ?? ''))
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

        if (empty($data['souscription_code'])) {
            $this->error('Veuillez sélectionner la souscription !');
            return;
        }

        $stmtSous = $this->model->getCon()->prepare("SELECT * FROM souscriptions WHERE code_souscription = ?");
        $stmtSous->execute([$data['souscription_code']]);
        $sous = $stmtSous->fetch(PDO::FETCH_ASSOC);

        if (!$sous) {
            $this->error('Souscription introuvable !');
            return;
        }

        if ($sous['statut_souscription'] !== 'solde') {
            $this->error('La distribution n\'est possible que pour les souscriptions soldées (client ayant payé la totalité).');
            return;
        }

        $userCode = Context::user() ?? '';
        $anneeCode = Context::annee();
        $etabCode = '5454544456';
        $codeDistribution = $this->validator->generateCode('distributions', 'code_distribution', 'DST-', 8);

        $filename = null;
        if (!empty($_FILES['pv_reception_photo']['name'])) {
            $uploadDir = __DIR__ . '/../../public/assets/images/distributions/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = pathinfo($_FILES['pv_reception_photo']['name'], PATHINFO_EXTENSION);
            $filename = 'pv_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['pv_reception_photo']['tmp_name'], $uploadDir . $filename);
        }

        $distributionData = [
            'code_distribution' => $codeDistribution,
            'souscription_code' => $data['souscription_code'],
            'zone_code' => $sous['zone_code'] ?? ($data['zone_code'] ?? ''),
            'client_code' => $sous['client_code'],
            'date_distribution_effectuee' => $data['date_distribution_effectuee'] ?: date('Y-m-d H:i:s'),
            'agent_livreur_code' => $data['agent_livreur_code'] ?: $userCode,
            'statut_distribution' => $data['statut_distribution'] ?? 'valide',
            'observation_distribution' => $data['observation_distribution'] ?? '',
            'pv_reception_photo' => $filename,
            'annee_code' => $anneeCode,
            'etablissement_code' => $etabCode,
            'user_code' => $userCode,
            'created_at_distribution' => date('Y-m-d H:i:s')
        ];

        if ($this->model->createDistribution($distributionData)) {
            $this->success('Distribution enregistrée avec succès !', ['code' => $codeDistribution]);
        } else {
            $this->error('Erreur lors de l\'enregistrement de la distribution');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id_distribution');
        if (!$id) { $this->error('Identifiant invalide'); return; }
        $data = $_POST;
        unset($data['csrf_token']);

        $data['updated_at_distribution'] = date('Y-m-d H:i:s');
        $cols = $this->model->getCon()->query("DESCRIBE distributions")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->update($filteredData, $id)) {
            $this->success('Distribution modifiée avec succès!');
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
            $this->error('Distribution introuvable');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) {
                $this->renderNotFound("La distribution demandée est introuvable.");
                return;
            }

            $stmtSous = $this->model->getCon()->prepare("
                SELECT s.*, c.nom_client, c.prenom_client, p.libelle_pack 
                FROM souscriptions s 
                LEFT JOIN clients c ON c.code_client = s.client_code 
                LEFT JOIN pack_souscriptions ps ON ps.souscription_code = s.code_souscription 
                LEFT JOIN packs p ON p.code_pack = ps.pack_code 
                WHERE s.code_souscription = ?
            ");
            $stmtSous->execute([$item['souscription_code']]);
            $souscription = $stmtSous->fetch(PDO::FETCH_ASSOC);

            $stmtLivreur = $this->model->getCon()->prepare("SELECT * FROM users WHERE code_user = ?");
            $stmtLivreur->execute([$item['agent_livreur_code'] ?? '']);
            $livreur = $stmtLivreur->fetch(PDO::FETCH_ASSOC);

            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            $this->renderNotFound("La distribution demandée est introuvable.");
            return;
        }
        $this->loadView('../views/distributions/details.php', [
            'item' => $item,
            'souscription' => $souscription,
            'livreur' => $livreur,
            'encryptedId' => $encryptedId
        ]);
    }

    public function edition($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) { header('Location: ' . RACINE . 'distribution/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'distribution/list'); exit();
        }
        $souscriptions = $this->model->getCon()->query("
            SELECT s.code_souscription, c.nom_client, c.prenom_client, p.libelle_pack, s.statut_souscription 
            FROM souscriptions s 
            LEFT JOIN clients c ON c.code_client = s.client_code 
            LEFT JOIN pack_souscriptions ps ON ps.souscription_code = s.code_souscription 
            LEFT JOIN packs p ON p.code_pack = ps.pack_code
            WHERE s.statut_souscription = 'solde'
        ")->fetchAll(PDO::FETCH_ASSOC);
        $agents = $this->model->getCon()->query("SELECT code_user, nom_user, prenom_user FROM users WHERE statut_user='actif'")->fetchAll(PDO::FETCH_ASSOC);
        $zones = $this->model->getCon()->query("SELECT code_zone, libelle_zone FROM zones WHERE statut_zone='actif'")->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/distributions/edit.php', [
            'item' => $item,
            'souscriptions' => $souscriptions,
            'agents' => $agents,
            'zones' => $zones,
            'encryptedId' => $encryptedId
        ]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $souscriptions = $this->model->getCon()->query("
            SELECT s.code_souscription, c.nom_client, c.prenom_client, p.libelle_pack, s.statut_souscription, s.statut_distribution
            FROM souscriptions s 
            LEFT JOIN clients c ON c.code_client = s.client_code 
            LEFT JOIN pack_souscriptions ps ON ps.souscription_code = s.code_souscription 
            LEFT JOIN packs p ON p.code_pack = ps.pack_code 
            WHERE s.statut_souscription = 'solde' AND s.statut_distribution != 'valide'
        ")->fetchAll(PDO::FETCH_ASSOC);
        $agents = $this->model->getCon()->query("SELECT code_user, nom_user, prenom_user FROM users WHERE statut_user='actif'")->fetchAll(PDO::FETCH_ASSOC);
        $zones = $this->model->getCon()->query("SELECT code_zone, libelle_zone FROM zones WHERE statut_zone='actif'")->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/distributions/edit.php', [
            'item' => [],
            'souscriptions' => $souscriptions,
            'agents' => $agents,
            'zones' => $zones
        ]);
    }
}
