<?php

class SessionController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelSession();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/sessions/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $stmt = $this->model->getCon()->query("
            SELECT s.*, a.libelle_annee, z.libelle_zone 
            FROM sessions s 
            LEFT JOIN annees a ON a.code_annee = s.annee_code 
            LEFT JOIN zones z ON z.code_zone = s.zone_code 
            ORDER BY s.id_session DESC
        ");
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        foreach ($items as $i) {
            $id = $i['id_session'];
            $idCrypte = $this->validator->crypter($id);

            $days = (int)($i['nombre_jour_session'] ?? 0);
            if ($days <= 0 && !empty($i['date_debut_session']) && !empty($i['date_fin_session'])) {
                try {
                    $d1 = new DateTime($i['date_debut_session']);
                    $d2 = new DateTime($i['date_fin_session']);
                    $days = $d1->diff($d2)->days + 1;
                } catch (\Throwable $e) {
                    $days = 0;
                }
            }
            $i['nombre_jour_session'] = $days;

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
        $data = $_POST;
        unset($data['csrf_token']);

        $userCode = Context::user() ?? '';
        $anneeCode = $data['annee_code'] ?? ($_SESSION['annee_active_code'] ?? 'VL0hWQ');
        $etabCode = '5454544456';

        if (empty($data['code_session'])) {
            $data['code_session'] = $this->validator->generateCode('sessions', 'code_session', 'SES-', 8);
        }
        $data['statut_session'] = $data['statut_session'] ?? 'inactif';
        $data['created_at_session'] = date('Y-m-d H:i:s');
        
        $cols = $this->model->getCon()->query("DESCRIBE sessions")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('user_code', $cols)) $data['user_code'] = $userCode;
        if (in_array('etablissement_code', $cols)) $data['etablissement_code'] = $etabCode;
        if (in_array('annee_code', $cols)) $data['annee_code'] = $anneeCode;

        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->create($filteredData)) {
            $this->success('Session d\'activité créée avec succès!');
        } else {
            $this->error('Erreur lors de la création de la session');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id_session');
        if (!$id) { $this->error('Identifiant invalide'); return; }
        $data = $_POST;
        unset($data['csrf_token']);
        $data['updated_at_session'] = date('Y-m-d H:i:s');

        $cols = $this->model->getCon()->query("DESCRIBE sessions")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->update($filteredData, $id)) {
            $this->success('Session d\'activité modifiée avec succès!');
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
            $this->error('Session introuvable');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) {
                $this->renderNotFound("La session demandée est introuvable.");
                return;
            }
            
            $sessionCode = $item['code_session'];

            // Année liée
            $stmtAnnee = $this->model->getCon()->prepare("SELECT * FROM annees WHERE code_annee = ?");
            $stmtAnnee->execute([$item['annee_code'] ?? '']);
            $annee = $stmtAnnee->fetch(PDO::FETCH_ASSOC) ?: [];

            // Packs de cette session
            $stmtPacks = $this->model->getCon()->prepare("SELECT * FROM packs WHERE session_code = ? ORDER BY libelle_pack ASC");
            $stmtPacks->execute([$sessionCode]);
            $packs = $stmtPacks->fetchAll(PDO::FETCH_ASSOC);

            // Souscriptions de cette session
            $stmtSous = $this->model->getCon()->prepare("
                SELECT s.*, c.nom_client, c.prenom_client, p.libelle_pack 
                FROM souscriptions s 
                LEFT JOIN clients c ON c.code_client = s.client_code 
                LEFT JOIN packs p ON p.code_pack = s.pack_code 
                WHERE s.session_code = ? 
                ORDER BY s.id_souscription DESC 
                LIMIT 50
            ");
            $stmtSous->execute([$sessionCode]);
            $souscriptions = $stmtSous->fetchAll(PDO::FETCH_ASSOC);

            // Stats financières
            $stmtStats = $this->model->getCon()->prepare("
                SELECT 
                    COUNT(DISTINCT s.id_souscription) as total_souscriptions,
                    COALESCE(SUM(s.montant_total_cotise), 0) as total_cotise,
                    COALESCE(SUM(s.montant_total_prevu), 0) as total_prevu
                FROM souscriptions s 
                WHERE s.session_code = ?
            ");
            $stmtStats->execute([$sessionCode]);
            $stats = $stmtStats->fetch(PDO::FETCH_ASSOC) ?: ['total_souscriptions' => 0, 'total_cotise' => 0, 'total_prevu' => 0];

            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            $this->renderNotFound("La session demandée est introuvable.");
            return;
        }

        $this->loadView('../views/sessions/details.php', [
            'item' => $item,
            'annee' => $annee,
            'packs' => $packs,
            'souscriptions' => $souscriptions,
            'stats' => $stats,
            'encryptedId' => $encryptedId
        ]);
    }

    public function edition($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) { header('Location: ' . RACINE . 'session/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'session/list'); exit();
        }

        $currentAnneeCode = $item['annee_code'] ?? '';
        $sqlAnnees = "SELECT * FROM annees WHERE statut_annee = 'actif'";
        if (!empty($currentAnneeCode)) {
            $sqlAnnees .= " OR code_annee = " . $this->model->getCon()->quote($currentAnneeCode);
        }
        $sqlAnnees .= " ORDER BY id_annee DESC";

        $annees = $this->model->getCon()->query($sqlAnnees)->fetchAll(PDO::FETCH_ASSOC);
        $zones = $this->model->getCon()->query("SELECT * FROM zones ORDER BY libelle_zone ASC")->fetchAll(PDO::FETCH_ASSOC);
        $this->loadView('../views/sessions/edit.php', [
            'item' => $item, 
            'annees' => $annees,
            'zones' => $zones,
            'encryptedId' => $encryptedId
        ]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $annees = $this->model->getCon()->query("SELECT * FROM annees WHERE statut_annee = 'actif' ORDER BY id_annee DESC")->fetchAll(PDO::FETCH_ASSOC);
        $zones = $this->model->getCon()->query("SELECT * FROM zones ORDER BY libelle_zone ASC")->fetchAll(PDO::FETCH_ASSOC);
        $this->loadView('../views/sessions/edit.php', [
            'item' => [],
            'annees' => $annees,
            'zones' => $zones
        ]);
    }
}
