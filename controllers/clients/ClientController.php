<?php

class ClientController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelClient();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/clients/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $anneeCode = Context::annee();
        $zoneCode = Context::zone();
        $userCode = Context::user();

        $sql = "
            SELECT DISTINCT c.*, z.libelle_zone
            FROM clients c
            LEFT JOIN zones z ON z.code_zone = c.zone_code
            LEFT JOIN souscriptions s ON s.client_code = c.code_client
            WHERE 1=1
        ";
        $params = [];

        // Application du filtrage strict selon le rôle RBAC (Context)
        if (Context::isCommercial()) {
            // Le commercial ne voit que ses propres clients créés par lui ou rattachés à ses souscriptions
            $sql .= " AND (c.user_code = ? OR s.user_code = ?)";
            $params[] = $userCode;
            $params[] = $userCode;
        } elseif (Context::isGestionnaire() && !empty($zoneCode)) {
            $sql .= " AND (c.zone_code = ? OR s.zone_code = ?)";
            $params[] = $zoneCode;
            $params[] = $zoneCode;
        }

        if (!empty($anneeCode) && $anneeCode !== '0GklBk07waYoLB6pHwY') {
            $sql .= " AND s.annee_code = ?";
            $params[] = $anneeCode;
        }

        $sql .= " ORDER BY c.created_at_client DESC";

        $stmt = $this->model->getCon()->prepare($sql);
        $stmt->execute($params);
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $data = [];

        foreach ($clients as $c) {
            $id = $c['id_client'];
            $idCrypte = $this->validator->crypter($id);
            $data[] = array_merge($c, [
                'id' => $id,
                'editId' => $idCrypte,
                'nom_complet' => trim($c['nom_client'] ?? '')
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

        // Nettoyage préalable des formats téléphoniques (+225 / 225)
        $this->cleanPhoneFields($data);

        // 1. Contrôle par téléphone (si renseigné)
        if (!empty($data['telephone_client'])) {
            $telClean = $data['telephone_client'];
            $stmtCheck = $this->model->getCon()->prepare("SELECT code_client, nom_client FROM clients WHERE telephone_client = ? LIMIT 1");
            $stmtCheck->execute([$telClean]);
            $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $this->error("Un client existe déjà avec ce numéro de téléphone ({$telClean}) : {$existing['nom_client']} (Code: {$existing['code_client']}).");
                return;
            }
        }

        // 2. Contrôle par numéro CNI (si renseigné)
        $cniVal = trim($data['numero_cni'] ?? ($data['cni_client'] ?? ''));
        if (!empty($cniVal)) {
            $stmtCheckCni = $this->model->getCon()->prepare("SELECT code_client, nom_client FROM clients WHERE numero_cni = ? LIMIT 1");
            $stmtCheckCni->execute([$cniVal]);
            $existingCni = $stmtCheckCni->fetch(PDO::FETCH_ASSOC);

            if ($existingCni) {
                $this->error("Un client existe déjà avec ce numéro de CNI ({$cniVal}) : {$existingCni['nom_client']} (Code: {$existingCni['code_client']}).");
                return;
            }
        }

        // 3. Contrôle anti-doublon par Nom complet + Lieu de résidence
        if (!empty($data['nom_client']) && !empty($data['lieu_residence_client'])) {
            $nom = trim($data['nom_client']);
            $residence = trim($data['lieu_residence_client']);

            $stmtCheckNom = $this->model->getCon()->prepare("SELECT code_client, nom_client, telephone_client FROM clients WHERE LOWER(nom_client) = LOWER(?) AND LOWER(lieu_residence_client) = LOWER(?) LIMIT 1");
            $stmtCheckNom->execute([$nom, $residence]);
            $existingNom = $stmtCheckNom->fetch(PDO::FETCH_ASSOC);

            if ($existingNom) {
                $this->error("Un client nommé '$nom' résidant à '$residence' existe déjà (Contact: {$existingNom['telephone_client']}, Code: {$existingNom['code_client']}).");
                return;
            }
        }

        $userCode = Context::user() ?? '';
        $etabCode = Context::etablissement();
        $zoneCode = Context::zone();
        if (empty($zoneCode)) {
            $stmtDefaultZone = $this->model->getCon()->query("SELECT code_zone FROM zones LIMIT 1");
            $defaultZone = $stmtDefaultZone->fetch(PDO::FETCH_ASSOC);
            $zoneCode = $defaultZone['code_zone'] ?? '6QIlVfXP0LiXE9tBzHownYLAAqDi2';
        }

        if (empty($data['code_client'])) {
            $data['code_client'] = $this->validator->generateCode('clients', 'code_client', 'CLI-', 8);
        }
        $data['statut_client'] = $data['statut_client'] ?? 'actif';
        $data['created_at_client'] = date('Y-m-d H:i:s');

        $cols = $this->model->getCon()->query("DESCRIBE clients")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('user_code', $cols)) $data['user_code'] = $userCode;
        if (in_array('etablissement_code', $cols)) $data['etablissement_code'] = $etabCode;
        if (in_array('zone_code', $cols) && empty($data['zone_code'])) $data['zone_code'] = $zoneCode;

        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->create($filteredData)) {
            $this->success('Client créé avec succès!', ['code_client' => $data['code_client']]);
        } else {
            $this->error('Erreur lors de la création du client');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();

        // RÈGLE STRICTE RBAC : Les commerciaux ne peuvent pas modifier les fiches clients
        if (Context::isCommercial()) {
            $this->error('Action non autorisée. Les commerciaux ne peuvent pas modifier les fiches clients.');
            return;
        }

        $id = (int)$this->post('id_client');
        if (!$id) { $this->error('Identifiant invalide'); return; }
        $data = $_POST;
        unset($data['csrf_token']);

        if (!empty($data['telephone_client'])) {
            if (!$this->checkUnique('clients', 'telephone_client', $data['telephone_client'], 'Téléphone client', 'id_client', $id)) return;
        }

        $data['updated_at_client'] = date('Y-m-d H:i:s');
        $cols = $this->model->getCon()->query("DESCRIBE clients")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->update($filteredData, $id)) {
            $this->success('Client modifié avec succès!');
        } else {
            $this->error('Erreur lors de la modification');
        }
    }

    public function changer()
    {
        $this->requirePost(false);
        $this->requireAuth();

        if (Context::isCommercial()) {
            $this->error('Action non autorisée. Les commerciaux ne peuvent pas changer le statut d\'un client.');
            return;
        }

        $id = $this->post('id');
        if ($id && $this->model->getById($id)) {
            if ($this->model->toggleStatus($id)) {
                $this->success('Statut mis à jour avec succès!', ['reload' => true]);
            } else {
                $this->error('Erreur lors de la mise à jour du statut');
            }
        } else {
            $this->error('Client introuvable');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) {
                $this->renderNotFound("Le client demandé est introuvable.");
                return;
            }

            // Récupérer les souscriptions de ce client avec filtrage par rôle
            $sql = "
                SELECT s.*, p.libelle_pack, z.libelle_zone
                FROM souscriptions s
                LEFT JOIN pack_souscriptions ps ON ps.souscription_code = s.code_souscription
                LEFT JOIN packs p ON p.code_pack = ps.pack_code
                LEFT JOIN zones z ON z.code_zone = s.zone_code
                WHERE s.client_code = ?
            ";
            $params = [$item['code_client']];

            if (Context::isCommercial()) {
                $sql .= " AND s.user_code = ?";
                $params[] = Context::user();
            }

            $sql .= " ORDER BY s.created_at_souscription DESC";

            $stmtSous = $this->model->getCon()->prepare($sql);
            $stmtSous->execute($params);
            $souscriptions = $stmtSous->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            $this->renderNotFound("Le client demandé est introuvable.");
            return;
        }
        $this->loadView('../views/clients/details.php', [
            'item' => $item,
            'souscriptions' => $souscriptions,
            'encryptedId' => $encryptedId
        ]);
    }

    public function edition($details)
    {
        $this->requireAuth();
        if (Context::isCommercial()) {
            header('Location: ' . RACINE . 'client/list');
            exit();
        }

        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) { header('Location: ' . RACINE . 'client/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'client/list'); exit();
        }
        $this->loadView('../views/clients/edit.php', ['item' => $item, 'encryptedId' => $encryptedId]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $this->loadView('../views/clients/edit.php', ['item' => []]);
    }
}