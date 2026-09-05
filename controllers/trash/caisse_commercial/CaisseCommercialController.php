<?php

class CaisseCommercialController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelCaisse();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/caisse_commercial/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $sql = "
            SELECT c.*, u.nom_user, u.prenom_user, val.nom_user as nom_validator, val.prenom_user as prenom_validator
            FROM caisses c
            LEFT JOIN users u ON u.code_user = c.user_code
            LEFT JOIN users val ON val.code_user = c.user_confirm
            WHERE 1=1
        ";
        $params = [];
        if (Context::isCommercial()) {
            $sql .= " AND c.user_code = ?";
            $params[] = Context::user();
        }
        $sql .= " ORDER BY c.date_ouverture DESC, c.id_caisse DESC";
        $stmt = $this->model->getCon()->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $data = [];
        foreach ($items as $i) {
            $id = $i['id_caisse'];
            $idCrypte = $this->validator->crypter($id);
            $totalDepot = (float)($i['montant_total_depot'] ?? 0);
            $data[] = array_merge($i, [
                'id_cloture' => $id,
                'code_cloture' => $i['code_caisse'],
                'date_cloture' => $i['date_cloture'] ? date('d/m/Y H:i', strtotime($i['date_cloture'])) : date('d/m/Y H:i', strtotime($i['date_ouverture'])),
                'total_especes' => $totalDepot,
                'total_mobile_money' => 0,
                'total_cheque_virement' => 0,
                'total_general' => $totalDepot,
                'statut_cloture' => $i['decission_caisse'] ?: 'attente',
                'editId' => $idCrypte,
                'nom_auteur_complet' => trim(($i['nom_user'] ?? '') . ' ' . ($i['prenom_user'] ?? '')),
                'nom_validator_complet' => trim(($i['nom_validator'] ?? '') . ' ' . ($i['prenom_validator'] ?? ''))
            ]);
        }
        $this->json(['data' => $data]);
    }

    public function getDailyTotals()
    {
        $this->requireAuth();
        $date = $_GET['date'] ?? ($_POST['date'] ?? date('Y-m-d'));
        $db = $this->model->getCon();

        $stmt = $db->prepare("
            SELECT mode_paiement, SUM(montant_paiement) as sum_mode, COUNT(*) as count_mode
            FROM paiements
            WHERE DATE(date_paiement) = ? AND statut_paiement != 'annule'
            GROUP BY mode_paiement
        ");
        $stmt->execute([$date]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalEspeces = 0;
        $totalMobileMoney = 0;
        $totalChequeVirement = 0;
        $nbEncaissements = 0;

        foreach ($rows as $r) {
            $mode = strtolower($r['mode_paiement'] ?? '');
            $sum = (float)($r['sum_mode'] ?? 0);
            $cnt = (int)($r['count_mode'] ?? 0);

            $nbEncaissements += $cnt;

            if ($mode === 'espece' || $mode === 'especes') {
                $totalEspeces += $sum;
            } elseif ($mode === 'mobile_money' || $mode === 'wave' || $mode === 'orange' || $mode === 'mtn' || $mode === 'moov') {
                $totalMobileMoney += $sum;
            } else {
                $totalChequeVirement += $sum;
            }
        }

        $totalGeneral = $totalEspeces + $totalMobileMoney + $totalChequeVirement;

        $stmtCheck = $db->prepare("SELECT * FROM caisses WHERE DATE(date_ouverture) = ? AND statut_caisse = 'cloture' LIMIT 1");
        $stmtCheck->execute([$date]);
        $alreadyClosed = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        $this->json([
            'status' => 1,
            'data' => [
                'date' => $date,
                'is_already_closed' => !empty($alreadyClosed),
                'existing_code' => $alreadyClosed['code_caisse'] ?? null,
                'total_especes' => $totalEspeces,
                'total_mobile_money' => $totalMobileMoney,
                'total_cheque_virement' => $totalChequeVirement,
                'total_general' => $totalGeneral,
                'nb_encaissements' => $nbEncaissements,
                'total_especes_fmt' => number_format($totalEspeces, 0, ',', ' ') . ' FCFA',
                'total_mobile_money_fmt' => number_format($totalMobileMoney, 0, ',', ' ') . ' FCFA',
                'total_cheque_virement_fmt' => number_format($totalChequeVirement, 0, ',', ' ') . ' FCFA',
                'total_general_fmt' => number_format($totalGeneral, 0, ',', ' ') . ' FCFA'
            ]
        ]);
    }

    public function apiGetCommercialSession()
    {
        $this->requireAuth();
        $userCode = Context::user() ?? '';
        $dateToday = date('Y-m-d');
        $db = $this->model->getCon();

        // 1. Chercher s'il y a une caisse OUVERTE aujourd'hui
        $stmtOuv = $db->prepare("
            SELECT * FROM caisses 
            WHERE user_code = ? AND DATE(date_ouverture) = ? AND statut_caisse = 'ouverte'
            ORDER BY id_caisse DESC LIMIT 1
        ");
        $stmtOuv->execute([$userCode, $dateToday]);
        $activeSession = $stmtOuv->fetch(PDO::FETCH_ASSOC);

        if ($activeSession) {
            $codeCaisse = $activeSession['code_caisse'];

            // Calculer les totaux réels des cotisations rattachées à cette caisse ou faites aujourd'hui
            $stmtCotis = $db->prepare("
                SELECT c.*, cli.nom_client, cli.telephone_client
                FROM cautisation_clients c
                LEFT JOIN clients cli ON cli.code_client = c.client_code
                WHERE (c.commercial_code = ? OR c.user_code = ?) 
                  AND (c.caisse_code = ? OR DATE(c.date_cautisation) = ?)
                  AND c.statut_cautisation_client != 'annule'
                ORDER BY c.date_cautisation DESC
            ");
            $stmtCotis->execute([$userCode, $userCode, $codeCaisse, $dateToday]);
            $cotisations = $stmtCotis->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $totalEspeces = 0;
            $totalMobileMoney = 0;
            $totalChequeVirement = 0;
            $cotisationList = [];

            foreach ($cotisations as $cot) {
                $m = (float)($cot['montant_cautisation_client'] ?? 0);
                $mode = strtolower($cot['mode_paiement'] ?? 'espece');
                if (in_array($mode, ['espece', 'especes', 'cash'])) {
                    $totalEspeces += $m;
                } elseif (in_array($mode, ['mobile_money', 'wave', 'orange', 'mtn', 'moov'])) {
                    $totalMobileMoney += $m;
                } else {
                    $totalChequeVirement += $m;
                }

                $nbJ = (int)($cot['nombre_jour'] ?? 1);
                $dateCot = $cot['date_cautisation'];
                $prochainRdv = date('Y-m-d', strtotime($dateCot . " + $nbJ days"));

                $cotisationList[] = [
                    'code_cautisation' => $cot['code_cautisation_client'],
                    'code_souscription' => $cot['souscription_code'],
                    'nom_client' => $cot['nom_client'] ?? 'Client Inconnu',
                    'telephone_client' => $cot['telephone_client'] ?? '-',
                    'montant' => $m,
                    'montant_fmt' => number_format($m, 0, ',', ' ') . ' FCFA',
                    'mode_paiement' => strtoupper($cot['mode_paiement'] ?? 'ESPECES'),
                    'date_cautisation' => date('d/m/Y H:i', strtotime($dateCot)),
                    'date_prochain_rdv' => date('d/m/Y', strtotime($prochainRdv)),
                    'statut' => $cot['statut_cautisation_client'] ?? 'en_attente'
                ];
            }

            $totalGeneral = $totalEspeces + $totalMobileMoney + $totalChequeVirement;
            $fondInitial = (float)($activeSession['montant_total_attendu'] ?? 0);

            $this->json([
                'status' => 1,
                'has_active_session' => true,
                'session' => [
                    'code_caisse' => $codeCaisse,
                    'date_caisse' => date('d/m/Y', strtotime($activeSession['date_ouverture'])),
                    'heure_ouverture' => date('H:i', strtotime($activeSession['date_ouverture'])),
                    'fond_initial' => $fondInitial,
                    'fond_initial_fmt' => number_format($fondInitial, 0, ',', ' ') . ' FCFA',
                    'total_especes' => $totalEspeces,
                    'total_mobile_money' => $totalMobileMoney,
                    'total_cheque_virement' => $totalChequeVirement,
                    'total_general' => $totalGeneral,
                    'total_general_fmt' => number_format($totalGeneral, 0, ',', ' ') . ' FCFA',
                    'nb_cotisations' => count($cotisations),
                    'cotisations' => $cotisationList
                ]
            ]);
            return;
        }

        // Aucune session active aujourd'hui : Récupérer le bilan de la dernière clôture
        $stmtLast = $db->prepare("
            SELECT c.*, val.nom_user as nom_validator, val.prenom_user as prenom_validator
            FROM caisses c
            LEFT JOIN users val ON val.code_user = c.user_confirm
            WHERE c.user_code = ? AND c.statut_caisse = 'cloture'
            ORDER BY c.date_cloture DESC, c.id_caisse DESC LIMIT 1
        ");
        $stmtLast->execute([$userCode]);
        $lastCloture = $stmtLast->fetch(PDO::FETCH_ASSOC);

        $lastCotisations = [];
        if ($lastCloture) {
            $dateLast = $lastCloture['date_cloture'] ? date('Y-m-d', strtotime($lastCloture['date_cloture'])) : date('Y-m-d', strtotime($lastCloture['date_ouverture']));
            $stmtCotis = $db->prepare("
                SELECT c.*, cli.nom_client, cli.telephone_client
                FROM cautisation_clients c
                LEFT JOIN clients cli ON cli.code_client = c.client_code
                WHERE (c.commercial_code = ? OR c.user_code = ?) 
                  AND (c.caisse_code = ? OR DATE(c.date_cautisation) = ?)
                  AND c.statut_cautisation_client != 'annule'
                ORDER BY c.date_cautisation DESC
            ");
            $stmtCotis->execute([$userCode, $userCode, $lastCloture['code_caisse'] ?? '', $dateLast]);
            $rawCotis = $stmtCotis->fetchAll(PDO::FETCH_ASSOC) ?: [];
            foreach ($rawCotis as $cot) {
                $m = (float)($cot['montant_cautisation_client'] ?? 0);
                $nbJ = (int)($cot['nombre_jour'] ?? 1);
                $dateCot = $cot['date_cautisation'];
                $prochainRdv = date('Y-m-d', strtotime($dateCot . " + $nbJ days"));
                $lastCotisations[] = [
                    'code_cautisation' => $cot['code_cautisation_client'],
                    'code_souscription' => $cot['souscription_code'],
                    'nom_client' => $cot['nom_client'] ?? 'Client Inconnu',
                    'telephone_client' => $cot['telephone_client'] ?? '-',
                    'montant' => $m,
                    'montant_fmt' => number_format($m, 0, ',', ' ') . ' FCFA',
                    'mode_paiement' => strtoupper($cot['mode_paiement'] ?? 'ESPECES'),
                    'date_cautisation' => date('d/m/Y H:i', strtotime($dateCot)),
                    'date_prochain_rdv' => date('d/m/Y', strtotime($prochainRdv)),
                    'statut' => $cot['statut_cautisation_client'] ?? 'en_attente'
                ];
            }
        }

        $totalLast = (float)($lastCloture['montant_total_depot'] ?? 0);

        $this->json([
            'status' => 1,
            'has_active_session' => false,
            'last_cloture' => $lastCloture ? [
                'code_cloture' => $lastCloture['code_caisse'],
                'date_cloture' => date('d/m/Y', strtotime($lastCloture['date_cloture'] ?? $lastCloture['date_ouverture'])),
                'total_general' => $totalLast,
                'total_general_fmt' => number_format($totalLast, 0, ',', ' ') . ' FCFA',
                'statut_cloture' => $lastCloture['decission_caisse'] ?? 'attente',
                'validator_nom' => trim(($lastCloture['nom_validator'] ?? '') . ' ' . ($lastCloture['prenom_validator'] ?? '')),
                'cotisations' => $lastCotisations
            ] : null
        ]);
    }

    public function ouvrirMaCaisse()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $userCode = Context::user() ?? '';
        $anneeCode = Context::annee();
        $etabCode = Context::etablissement();
        $dateToday = date('Y-m-d');
        $fondInitial = (float)($this->post('fond_initial') ?? 0);
        $db = $this->model->getCon();

        // Vérifier s'il y a déjà une caisse ouverte aujourd'hui
        $stmtCheck = $db->prepare("SELECT id_caisse FROM caisses WHERE user_code = ? AND DATE(date_ouverture) = ? AND statut_caisse = 'ouverte'");
        $stmtCheck->execute([$userCode, $dateToday]);
        if ($stmtCheck->fetch()) {
            $this->error('Vous avez déjà une caisse OUVERTE aujourd\'hui !');
            return;
        }

        $codeCaisse = $this->validator->generateCode('caisses', 'code_caisse', 'CAISSE-', 8);
        $data = [
            'code_caisse' => $codeCaisse,
            'date_ouverture' => date('Y-m-d H:i:s'),
            'montant_total_attendu' => $fondInitial,
            'montant_total_depot' => 0,
            'decission_caisse' => 'attente',
            'statut_caisse' => 'ouverte',
            'user_code' => $userCode,
            'annee_code' => $anneeCode,
            'etablissement_code' => $etabCode,
            'zone_code' => Context::zone() ?? 'DEFAULT'
        ];

        $modelOuv = new ModelCaisse();
        if ($modelOuv->create($data)) {
            $this->success('Votre caisse du jour est maintenant OUVERTE ! Vous pouvez démarrer vos encaissements.', ['reload' => true]);
        } else {
            $this->error('Erreur lors de l\'ouverture de caisse.');
        }
    }

    public function add()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $userCode = Context::user() ?? '';
        $db = $this->model->getCon();

        // Chercher la caisse ouverte active pour l'utilisateur
        $stmtOuv = $db->prepare("SELECT * FROM caisses WHERE user_code = ? AND statut_caisse = 'ouverte' ORDER BY id_caisse DESC LIMIT 1");
        $stmtOuv->execute([$userCode]);
        $activeCaisse = $stmtOuv->fetch(PDO::FETCH_ASSOC);

        if (!$activeCaisse) {
            $this->error("Aucune caisse ouverte à clôturer.");
            return;
        }

        $codeCaisse = $activeCaisse['code_caisse'];
        $dateOpening = date('Y-m-d', strtotime($activeCaisse['date_ouverture']));

        // Calculer les totaux réels des encaissements
        $stmtCotis = $db->prepare("
            SELECT c.* 
            FROM cautisation_clients c
            WHERE (c.commercial_code = ? OR c.user_code = ?) 
              AND (c.caisse_code = ? OR DATE(c.date_cautisation) = ?)
              AND c.statut_cautisation_client != 'annule'
        ");
        $stmtCotis->execute([$userCode, $userCode, $codeCaisse, $dateOpening]);
        $cotisations = $stmtCotis->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $totalGeneral = 0;
        foreach ($cotisations as $cot) {
            $totalGeneral += (float)($cot['montant_cautisation_client'] ?? 0);
        }

        $updateData = [
            'date_cloture' => date('Y-m-d H:i:s'),
            'montant_total_depot' => $totalGeneral,
            'statut_caisse' => 'cloture',
            'decission_caisse' => 'attente'
        ];

        if ($this->model->update($updateData, (int)$activeCaisse['id_caisse'])) {
            $this->success('Clôture de caisse effectuée avec succès et transmise à la comptabilité pour validation !', ['reload' => true]);
        } else {
            $this->error('Erreur lors de l\'enregistrement de la clôture de caisse.');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id_caisse');
        if (!$id) { $this->error('Identifiant invalide'); return; }
        $data = $_POST;
        unset($data['csrf_token']);
        $cols = $this->model->getCon()->query("DESCRIBE caisses")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->update($filteredData, $id)) {
            $this->success('Item modifié avec succès!');
        } else {
            $this->error('Erreur lors de la modification');
        }
    }

    public function changer()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id');
        $statut = $this->post('statut') ?: $this->post('status');
        if ($id && $this->model->getById($id)) {
            $allowed = ['attente', 'valide', 'rejete'];
            if (!empty($statut) && in_array($statut, $allowed, true)) {
                $userCode = $_SESSION[USERS_AUTH]['code_user'] ?? null;
                $extraData = [
                    'decission_caisse' => $statut,
                    'date_validation' => date('Y-m-d H:i:s'),
                    'user_confirm' => $userCode,
                    'date_confirm' => date('Y-m-d H:i:s')
                ];
                $success = $this->model->update($extraData, $id);
            } else {
                $success = false;
            }
            if ($success) {
                $this->success('Statut de la caisse mis à jour avec succès!', ['reload' => true]);
            } else {
                $this->error('Erreur lors de la mise à jour du statut');
            }
        } else {
            $this->error('Caisse introuvable');
        }
    }

    public function details($param)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($param);
            $stmt = $this->model->getCon()->prepare("
                SELECT c.*, u.nom_user, u.prenom_user
                FROM caisses c
                LEFT JOIN users u ON u.code_user = c.user_code
                WHERE c.id_caisse = ?
            ");
            $stmt->execute([$id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$item) {
                $this->renderNotFound("Le procès-verbal de clôture de caisse demandé est introuvable.");
                return;
            }

            $encryptedId = $this->validator->crypter($id);
            
            $sqlP = "
                SELECT cc.*, cl.nom_client, cl.prenom_client, cl.telephone_client
                FROM cautisation_clients cc
                LEFT JOIN clients cl ON cl.code_client = cc.client_code
                WHERE cc.caisse_code = ? OR DATE(cc.date_cautisation) = DATE(?)
                ORDER BY cc.date_cautisation DESC
            ";
            $stmtP = $this->model->getCon()->prepare($sqlP);
            $stmtP->execute([$item['code_caisse'] ?? '', $item['date_ouverture'] ?? '']);
            $paiements = $stmtP->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("CaisseCommercialController::details error: " . $e->getMessage());
            $this->renderNotFound("Le procès-verbal de clôture de caisse demandé est introuvable.");
            return;
        }
        $this->loadView('../views/caisse_commercial/details.php', [
            'item' => $item, 
            'paiements' => $paiements,
            'encryptedId' => $encryptedId
        ]);
    }

    public function edition($param)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($param);
            $item = $this->model->getById($id);
            if (!$item) { header('Location: ' . RACINE . 'caisse_commercial/list'); exit(); }
            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'caisse_commercial/list'); exit();
        }
        $this->loadView('../views/caisse_commercial/edit.php', ['item' => $item, 'encryptedId' => $encryptedId]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $this->loadView('../views/caisse_commercial/edit.php', ['item' => []]);
    }
}
