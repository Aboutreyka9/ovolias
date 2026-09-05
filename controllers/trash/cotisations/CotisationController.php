<?php

class CotisationController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelCotisation();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/cotisations/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        
        $sql = "
            SELECT c.*, 
                   cl.nom_client, cl.telephone_client,
                   u.nom_user as nom_commercial, u.prenom_user as prenom_commercial
            FROM cautisation_clients c
            LEFT JOIN clients cl ON cl.code_client = c.client_code
            LEFT JOIN users u ON u.code_user = c.commercial_code
            WHERE 1=1
        ";
        $params = [];

        // Application du périmètre de données RBAC
        if (Context::isCommercial()) {
            $sql .= " AND (c.commercial_code = ? OR c.user_code = ?)";
            $params[] = Context::user();
            $params[] = Context::user();
        }

        if (Context::annee() && Context::annee() !== '0GklBk07waYoLB6pHwY') {
            $sql .= " AND c.annee_code = ?";
            $params[] = Context::annee();
        }

        $sql .= " ORDER BY c.date_cautisation DESC, c.id_cautisation_client DESC";

        $stmt = $this->model->getCon()->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $data = [];

        foreach ($items as $c) {
            $id = $c['id_cautisation_client'];
            $idCrypte = $this->validator->crypter($id);
            $data[] = array_merge($c, [
                'id' => $id,
                'editId' => $idCrypte,
                'nom_client_complet' => trim(($c['nom_client'] ?? '')),
                'nom_commercial_complet' => trim(($c['nom_commercial'] ?? '') . ' ' . ($c['prenom_commercial'] ?? ''))
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

        if (empty($data['souscription_code']) || empty($data['montant_cautisation'])) {
            $this->error('Veuillez renseigner la souscription et le montant versé !');
            return;
        }

        $stmtSous = $this->model->getCon()->prepare("SELECT * FROM souscriptions WHERE code_souscription = ?");
        $stmtSous->execute([$data['souscription_code']]);
        $sous = $stmtSous->fetch(PDO::FETCH_ASSOC);

        if (!$sous) {
            $this->error('Souscription introuvable !');
            return;
        }

        if ($sous['statut_souscription'] === 'solde') {
            $this->error('Cette souscription est déjà soldée, aucune cotisation supplémentaire possible.');
            return;
        }

        if ($sous['statut_souscription'] === 'annule') {
            $this->error('Cette souscription est annulée, impossible d\'ajouter une cotisation.');
            return;
        }

        $userCode = Context::user() ?? '';
        $anneeCode = Context::annee();
        $etabCode = Context::etablissement();
        $codeCotisation = $this->validator->generateCode('cautisation_clients', 'code_cautisation_client', 'COT-', 8);

        $cotisJour = (float)($sous['montant_cotisation_journaliere'] ?: 1000);
        $montant = (float)$data['montant_cautisation'];
        $nbJours = (int)($data['nombre_jour_paye'] ?: ($cotisJour > 0 ? ceil($montant / $cotisJour) : 1));

        $filename = null;
        if (!empty($_FILES['photo_recu']['name'])) {
            $uploadDir = __DIR__ . '/../../public/assets/images/recus/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = pathinfo($_FILES['photo_recu']['name'], PATHINFO_EXTENSION);
            $filename = 'recu_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['photo_recu']['tmp_name'], $uploadDir . $filename);
        }

        // RÈGLE STRICTE RBAC : Les cotisations saisies par un commercial restent 'en_attente' jusqu'à validation caisse
        $statutInitial = Context::isCommercial() ? 'en_attente' : 'valide';

        $cotisationData = [
            'code_cautisation_client' => $codeCotisation,
            'souscription_code' => $data['souscription_code'],
            'client_code' => $sous['client_code'],
            'montant_cautisation_client' => $montant,
            'nombre_jour' => $nbJours,
            'mode_paiement' => $data['mode_paiement'] ?? 'espece',
            'date_cautisation' => $data['date_cautisation'] ?: date('Y-m-d'),
            'commercial_code' => $userCode,
            'reference_paiement' => $data['reference_paiement'] ?? '',
            'recu_numero' => $data['recu_numero'] ?? $codeCotisation,
            'photo_recu' => $filename,
            'statut_cautisation_client' => $statutInitial,
            'annee_code' => $anneeCode,
            'etablissement_code' => $etabCode,
            'user_code' => $userCode,
            'created_at_cautisation_client' => date('Y-m-d H:i:s'),
            'updated_at_cautisation_client' => date('Y-m-d H:i:s')
        ];

        if ($this->model->createCotisation($cotisationData)) {
            $msg = Context::isCommercial() 
                ? 'Cotisation enregistrée avec succès (En attente de validation de la caisse/comptabilité).' 
                : 'Cotisation enregistrée et validée avec succès !';
            $this->success($msg, ['code' => $codeCotisation]);
        } else {
            $this->error('Erreur lors de l\'enregistrement de la cotisation');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();

        // RÈGLE STRICTE RBAC : Un commercial ne peut PAS modifier les cotisations
        if (Context::isCommercial()) {
            $this->error('Action non autorisée. Les commerciaux ne peuvent pas modifier les cotisations.');
            return;
        }

        $id = (int)$this->post('id_cautisation_client');
        if (!$id) { $this->error('Identifiant invalide'); return; }
        $data = $_POST;
        unset($data['csrf_token']);

        $data['updated_at_cautisation_client'] = date('Y-m-d H:i:s');
        $cols = $this->model->getCon()->query("DESCRIBE cautisation_clients")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->update($filteredData, $id)) {
            $this->success('Cotisation modifiée avec succès!');
        } else {
            $this->error('Erreur lors de la modification');
        }
    }

    public function changer()
    {
        $this->requirePost(false);
        $this->requireAuth();

        if (Context::isCommercial()) {
            $this->error('Action non autorisée. Les commerciaux ne peuvent pas changer le statut d\'une cotisation.');
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
            $this->error('Cotisation introuvable');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) {
                $this->renderNotFound("La cotisation demandée est introuvable.");
                return;
            }

            $stmtSous = $this->model->getCon()->prepare("
                SELECT s.*, c.nom_client, p.libelle_pack 
                FROM souscriptions s 
                LEFT JOIN clients c ON c.code_client = s.client_code 
                LEFT JOIN pack_souscriptions ps ON ps.souscription_code = s.code_souscription 
                LEFT JOIN packs p ON p.code_pack = ps.pack_code 
                WHERE s.code_souscription = ?
            ");
            $stmtSous->execute([$item['souscription_code']]);
            $souscription = $stmtSous->fetch(PDO::FETCH_ASSOC);

            $stmtCommercial = $this->model->getCon()->prepare("SELECT * FROM users WHERE code_user = ?");
            $stmtCommercial->execute([$item['commercial_code'] ?? '']);
            $commercial = $stmtCommercial->fetch(PDO::FETCH_ASSOC);

            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            $this->renderNotFound("La cotisation demandée est introuvable.");
            return;
        }
        $this->loadView('../views/cotisations/details.php', [
            'item' => $item,
            'souscription' => $souscription,
            'commercial' => $commercial,
            'encryptedId' => $encryptedId
        ]);
    }

    public function edition($details)
    {
        $this->requireAuth();
        if (Context::isCommercial()) {
            header('Location: ' . RACINE . 'cotisation/list');
            exit();
        }

        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) { header('Location: ' . RACINE . 'cotisation/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'cotisation/list'); exit();
        }
        $souscriptions = $this->model->getCon()->query("
            SELECT s.code_souscription, c.nom_client, p.libelle_pack 
            FROM souscriptions s 
            LEFT JOIN clients c ON c.code_client = s.client_code 
            LEFT JOIN pack_souscriptions ps ON ps.souscription_code = s.code_souscription 
            LEFT JOIN packs p ON p.code_pack = ps.pack_code 
            WHERE s.statut_souscription IN ('valide', 'reconduite')
        ")->fetchAll(PDO::FETCH_ASSOC);
        $commerciaux = $this->model->getCon()->query("SELECT code_user, nom_user, prenom_user FROM users WHERE statut_user='actif'")->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/cotisations/edit.php', [
            'item' => $item,
            'souscriptions' => $souscriptions,
            'commerciaux' => $commerciaux,
            'encryptedId' => $encryptedId
        ]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $souscriptions = $this->model->getCon()->query("
            SELECT s.code_souscription, s.montant_cotisation_journaliere, s.montant_total_cotise, s.montant_total_prevu, s.nombre_jour_total, s.nombre_jour_cotise, c.nom_client, p.libelle_pack 
            FROM souscriptions s 
            LEFT JOIN clients c ON c.code_client = s.client_code 
            LEFT JOIN pack_souscriptions ps ON ps.souscription_code = s.code_souscription 
            LEFT JOIN packs p ON p.code_pack = ps.pack_code 
            WHERE s.statut_souscription IN ('valide', 'reconduite')
        ")->fetchAll(PDO::FETCH_ASSOC);
        $commerciaux = $this->model->getCon()->query("SELECT code_user, nom_user, prenom_user FROM users WHERE statut_user='actif'")->fetchAll(PDO::FETCH_ASSOC);

        $selectedSouscription = $_GET['souscription'] ?? '';

        $this->loadView('../views/cotisations/edit.php', [
            'item' => ['souscription_code' => $selectedSouscription],
            'souscriptions' => $souscriptions,
            'commerciaux' => $commerciaux
        ]);
    }
}
