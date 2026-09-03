<?php

class VersementController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelVersement();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/versements/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();

        $sql = "
            SELECT v.*, 
                   uc.nom_user as nom_commercial, uc.prenom_user as prenom_commercial,
                   uv.nom_user as nom_validator, uv.prenom_user as prenom_validator,
                   z.libelle_zone
            FROM versements_commerciaux v
            LEFT JOIN users uc ON uc.code_user = v.commercial_code
            LEFT JOIN users uv ON uv.code_user = v.user_validate
            LEFT JOIN zones z ON z.code_zone = v.zone_code
            WHERE 1=1
        ";
        $params = [];

        // RÈGLE RBAC : Le commercial ne voit que ses propres versements
        if (Context::isCommercial()) {
            $sql .= " AND (v.commercial_code = ? OR v.user_code = ?)";
            $params[] = Context::user();
            $params[] = Context::user();
        }

        $sql .= " ORDER BY v.created_at_versement DESC";

        $stmt = $this->model->getCon()->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $data = [];

        foreach ($items as $v) {
            $id = $v['id_versement'];
            $idCrypte = $this->validator->crypter($id);
            $data[] = array_merge($v, [
                'id' => $id,
                'editId' => $idCrypte,
                'nom_commercial_complet' => trim(($v['nom_commercial'] ?? '') . ' ' . ($v['prenom_commercial'] ?? '')),
                'nom_validator_complet' => trim(($v['nom_validator'] ?? '') . ' ' . ($v['prenom_validator'] ?? ''))
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
        $etabCode = Context::etablissement();
        $codeVersement = $this->validator->generateCode('versements_commerciaux', 'code_versement_commercial', 'VRS-', 8);

        $commercialCode = !empty($data['commercial_code']) ? $data['commercial_code'] : $userCode;
        if (empty($commercialCode) || empty($data['montant_versement'])) {
            $this->error('Veuillez renseigner le commercial et le montant versé !');
            return;
        }

        $versementData = [
            'code_versement_commercial' => $codeVersement,
            'reference_versement' => $data['reference_versement'] ?? $codeVersement,
            'montant_versement' => (int)$data['montant_versement'],
            'commercial_code' => $commercialCode,
            'periode_versement_debut' => !empty($data['periode_versement_debut']) ? $data['periode_versement_debut'] : date('Y-m-d'),
            'periode_versement_fin' => !empty($data['periode_versement_fin']) ? $data['periode_versement_fin'] : date('Y-m-d'),
            'zone_code' => $data['zone_code'] ?? Context::zone() ?? '',
            'statut_versement' => 'En attente',
            'etablissement_code' => $etabCode,
            'user_code' => $userCode,
            'created_at_versement' => date('Y-m-d H:i:s'),
            'user_validate' => '',
            'date_validation' => '1000-01-01 00:00:00',
            'commentaire_validation' => ''
        ];

        $cols = $this->model->getCon()->query("DESCRIBE versements_commerciaux")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($versementData, array_flip($cols));

        if ($this->model->create($filteredData)) {
            $this->success('Versement de caisse transmis avec succès (En attente de validation finance) !', ['code' => $codeVersement]);
        } else {
            $this->error('Erreur lors de l\'enregistrement du versement');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();

        if (Context::isCommercial()) {
            $this->error('Action non autorisée. Les commerciaux ne peuvent pas modifier un versement transmis.');
            return;
        }

        $id = (int)$this->post('id_versement');
        if (!$id) { $this->error('Identifiant invalide'); return; }
        $data = $_POST;
        unset($data['csrf_token']);

        $cols = $this->model->getCon()->query("DESCRIBE versements_commerciaux")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->update($filteredData, $id)) {
            $this->success('Versement modifié avec succès!');
        } else {
            $this->error('Erreur lors de la modification');
        }
    }

    public function valider()
    {
        $this->requirePost(false);
        $this->requireAuth();

        // RÈGLE RBAC : Seul le profil Finance / Admin peut valider ou rejeter un versement
        if (!Context::isFinance() && !Context::isAdmin()) {
            $this->error('Action non autorisée. Seul le service Comptabilité / Finance ou l\'Administration peut valider un versement.');
            return;
        }

        $id = (int)$this->post('id_versement');
        $statut = $this->post('statut_versement') ?? 'Valide';
        $commentaire = $this->post('commentaire_validation') ?? 'Validé par la comptabilité';
        $userValidateCode = Context::user() ?? '';

        if (!$id) {
            $this->error('Identifiant de versement invalide');
            return;
        }

        $versement = $this->model->getById($id);
        if (!$versement) {
            $this->error('Versement introuvable.');
            return;
        }

        // Valider le versement et basculer les cotisations associées du commercial en 'valide'
        if ($this->model->validateVersement($id, $userValidateCode, $commentaire)) {
            if (strtolower($statut) === 'valide' || strtolower($statut) === 'validé') {
                $stmtCotis = $this->model->getCon()->prepare("
                    UPDATE cautisation_clients 
                    SET statut_cautisation_client = 'valide', updated_at_cautisation_client = NOW()
                    WHERE commercial_code = ? AND statut_cautisation_client = 'en_attente'
                ");
                $stmtCotis->execute([$versement['commercial_code']]);
            }
            $this->success('Versement validé et cotisations du commercial actualisées avec succès !', ['reload' => true]);
        } else {
            $this->error('Erreur lors de la validation du versement');
        }
    }

    public function changer()
    {
        $this->requirePost(false);
        $this->requireAuth();

        if (!Context::isFinance() && !Context::isAdmin()) {
            $this->error('Action non autorisée. Seul le service Comptabilité / Finance peut modifier le statut d\'un versement.');
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
            $this->error('Versement introuvable');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) {
                $this->renderNotFound("Le versement demandé est introuvable.");
                return;
            }

            $stmtU = $this->model->getCon()->prepare("SELECT * FROM users WHERE code_user = ?");
            $stmtU->execute([$item['commercial_code']]);
            $commercial = $stmtU->fetch(PDO::FETCH_ASSOC);

            $stmtZ = $this->model->getCon()->prepare("SELECT * FROM zones WHERE code_zone = ?");
            $stmtZ->execute([$item['zone_code']]);
            $zone = $stmtZ->fetch(PDO::FETCH_ASSOC);

            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            $this->renderNotFound("Le versement demandé est introuvable.");
            return;
        }
        $this->loadView('../views/versements/details.php', [
            'item' => $item,
            'commercial' => $commercial,
            'zone' => $zone,
            'encryptedId' => $encryptedId
        ]);
    }

    public function edition($details)
    {
        $this->requireAuth();
        if (Context::isCommercial()) {
            header('Location: ' . RACINE . 'versement/list');
            exit();
        }

        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) { header('Location: ' . RACINE . 'versement/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'versement/list'); exit();
        }
        $commerciaux = $this->model->getCon()->query("SELECT code_user, nom_user, prenom_user FROM users WHERE statut_user='actif'")->fetchAll(PDO::FETCH_ASSOC);
        $zones = $this->model->getCon()->query("SELECT code_zone, libelle_zone FROM zones WHERE statut_zone='actif'")->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/versements/edit.php', [
            'item' => $item,
            'commerciaux' => $commerciaux,
            'zones' => $zones,
            'encryptedId' => $encryptedId
        ]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $commerciaux = $this->model->getCon()->query("SELECT code_user, nom_user, prenom_user FROM users WHERE statut_user='actif'")->fetchAll(PDO::FETCH_ASSOC);
        $zones = $this->model->getCon()->query("SELECT code_zone, libelle_zone FROM zones WHERE statut_zone='actif'")->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/versements/edit.php', [
            'item' => [],
            'commerciaux' => $commerciaux,
            'zones' => $zones
        ]);
    }
}
