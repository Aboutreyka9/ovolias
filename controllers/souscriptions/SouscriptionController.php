<?php

class SouscriptionController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelSouscription();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/souscriptions/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();

        // Récupération sécurisée du périmètre d'accès selon le rôle connecté
        $userScopeFilter = Context::isCommercial() ? Context::user() : null;
        $zoneScopeFilter = Context::isGestionnaire() ? Context::zone() : null;
        $anneeScopeFilter = Context::annee();

        $items = $this->model->getAllWithDetails($userScopeFilter, $zoneScopeFilter, $anneeScopeFilter);

        $grouped = [];
        foreach ($items as $s) {
            $code = $s['code_souscription'];
            if (!isset($grouped[$code])) {
                $grouped[$code] = $s;
                $grouped[$code]['packs'] = [];
            }
            if (!empty($s['libelle_pack'])) {
                $grouped[$code]['packs'][] = $s['libelle_pack'];
            }
        }

        $data = [];
        foreach ($grouped as $s) {
            $id = $s['id_souscription'];
            $idCrypte = $this->validator->crypter($id);
            
            $sumPrixCotisation = (float)($s['sum_prix_cotisation_pack'] ?? 0);
            $nombreJourSession = (int)($s['nombre_jour_session'] ?? 0);
            $totaleSouscription = (float)($s['totale_souscription'] ?? ($sumPrixCotisation * $nombreJourSession));
            $montantCotise = (float)($s['montant_total_cotise'] ?? 0);
            $soldeRestant = max(0, $totaleSouscription - $montantCotise);
            $joursRestants = max(0, $nombreJourSession - (int)($s['nombre_jour_cotise'] ?? 0));

            $nbPacks = count($s['packs'] ?? []);
            if ($nbPacks > 1) {
                $packLabel = $s['packs'][0] . ' <small style="color:#64748B;">+' . ($nbPacks - 1) . ' autre(s)</small>';
            } elseif ($nbPacks === 1) {
                $packLabel = $s['packs'][0];
            } else {
                $packLabel = '-';
            }
            $data[] = array_merge($s, [
                'id' => $id,
                'editId' => $idCrypte,
                'nom_client_complet' => trim($s['nom_client'] ?? ''),
                'date_souscription' => isset($s['created_at_souscription']) && !empty($s['created_at_souscription'])
                    ? date('d-m-Y', strtotime($s['created_at_souscription']))
                    : '-',
                'libelle_pack' => $packLabel,
                'nombre_packs' => $nbPacks,
                'sum_prix_cotisation_pack' => $sumPrixCotisation,
                'totale_souscription' => $totaleSouscription,
                'solde_restant' => $soldeRestant,
                'jours_restants' => $joursRestants,
                'progression' => $nombreJourSession > 0 ? round((($s['nombre_jour_cotise'] ?? 0) / $nombreJourSession) * 100) : 0
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

        if (empty($data['client_code']) || empty($data['pack_code'])) {
            $this->error('Veuillez sélectionner un client et un pack !');
            return;
        }

        $stmtPack = $this->model->getCon()->prepare("SELECT * FROM packs WHERE code_pack = ?");
        $stmtPack->execute([$data['pack_code']]);
        $pack = $stmtPack->fetch(PDO::FETCH_ASSOC);

        if (!$pack) {
            $this->error('Pack sélectionné introuvable !');
            return;
        }

        $userCode = Context::user() ?? '';
        $etabCode = Context::etablissement();
        $anneeCode = Context::annee();
        $codeSouscription = $this->validator->generateCode('souscriptions', 'code_souscription', 'SUB-', 8);

        $nbJours = (int)($data['nombre_jour_total'] ?: ($pack['nombre_jour_pack'] ?: 170));
        $cotisJour = (float)($data['montant_cotisation_journaliere'] ?: ($pack['prix_cotisation_pack'] ?: 0));
        $montantTotal = $nbJours * $cotisJour;

        $souscriptionData = [
            'code_souscription' => $codeSouscription,
            'client_code' => $data['client_code'],
            'session_code' => $data['session_code'] ?: ($pack['session_code'] ?: null),
            'zone_code' => $data['zone_code'] ?: ($pack['zone_code'] ?: null),
            'date_debut_souscription' => $data['date_debut_souscription'] ?: date('Y-m-d'),
            'montant_total_prevu' => $montantTotal,
            'montant_cotisation_journaliere' => $cotisJour,
            'nombre_jour_total' => $nbJours,
            'nombre_jour_cotise' => 0,
            'montant_total_cotise' => 0,
            'statut_distribution' => 'En attente',
            'statut_souscription' => 'valide',
            'user_code' => $userCode,
            'etablissement_code' => $etabCode,
            'annee_code' => $anneeCode,
            'created_at_souscription' => date('Y-m-d H:i:s')
        ];

        if ($this->model->createSouscriptionWithPack($souscriptionData, $data['pack_code'], 1, $cotisJour)) {
            $this->success('Souscription créée avec succès !', ['code_souscription' => $codeSouscription]);
        } else {
            $this->error('Erreur lors de la création de la souscription');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();

        // RÈGLE STRICTE RBAC : Les commerciaux ne peuvent pas modifier les souscriptions
        if (Context::isCommercial()) {
            $this->error('Action non autorisée. Les commerciaux ne peuvent pas modifier une souscription.');
            return;
        }

        $id = (int)$this->post('id_souscription');
        if (!$id) { $this->error('Identifiant invalide'); return; }
        $data = $_POST;
        unset($data['csrf_token']);

        $data['updated_at_souscription'] = date('Y-m-d H:i:s');
        $cols = $this->model->getCon()->query("DESCRIBE souscriptions")->fetchAll(PDO::FETCH_COLUMN);
        $filteredData = array_intersect_key($data, array_flip($cols));
        if ($this->model->update($filteredData, $id)) {
            $this->success('Souscription modifiée avec succès!');
        } else {
            $this->error('Erreur lors de la modification');
        }
    }

    public function changer()
    {
        $this->requirePost(false);
        $this->requireAuth();

        if (Context::isCommercial()) {
            $this->error('Action non autorisée. Les commerciaux ne peuvent pas changer le statut d\'une souscription.');
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
            $this->error('Souscription introuvable');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $id = $this->validator->decrypter($details);
            $item = $this->model->getById($id);
            if (!$item) {
                $this->renderNotFound("La souscription demandée est introuvable.");
                return;
            }

            $packSouscrit = $this->model->getPackSouscrit($item['code_souscription']);
            $soldeRestant = $this->model->getSoldeRestant($item['code_souscription']);
            $joursRestants = $this->model->getJoursRestants($item['code_souscription']);

            $stmtClient = $this->model->getCon()->prepare("SELECT * FROM clients WHERE code_client = ?");
            $stmtClient->execute([$item['client_code']]);
            $client = $stmtClient->fetch(PDO::FETCH_ASSOC);

            $stmtCotis = $this->model->getCon()->prepare("
                SELECT c.*, u.nom_user, u.prenom_user
                FROM cautisation_clients c
                LEFT JOIN users u ON u.code_user = c.commercial_code
                WHERE c.souscription_code = ?
                ORDER BY c.date_cautisation DESC
            ");
            $stmtCotis->execute([$item['code_souscription']]);
            $cotisations = $stmtCotis->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $encryptedId = $this->validator->crypter($id);
        } catch (Exception $e) {
            $this->renderNotFound("La souscription demandée est introuvable.");
            return;
        }
        $this->loadView('../views/souscriptions/details.php', [
            'item' => $item,
            'client' => $client,
            'packSouscrit' => $packSouscrit,
            'soldeRestant' => $soldeRestant,
            'joursRestants' => $joursRestants,
            'cotisations' => $cotisations,
            'encryptedId' => $encryptedId
        ]);
    }

    public function wizard()
    {
        $this->requireAuth();
        $this->loadView('../views/souscriptions/wizard.php');
    }

    public function createWizard()
    {
        $this->requirePost(false);
        $this->requireAuth();

        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        if (!$data || empty($data['client_code']) || empty($data['pack_codes']) || !is_array($data['pack_codes'])) {
            $this->error('Données de souscription incomplètes (client ou packs manquants)');
            return;
        }

        $userCode = Context::user() ?? '';
        $etabCode = Context::etablissement();
        $anneeCode = Context::annee();
        $zoneCode = $data['zone_code'] ?? Context::zone() ?? '6QIlVfXP0LiXE9tBzHownYLAAqDi2';
        $sessionCode = $data['session_code'] ?? 'kezoZf6kVz40261eKu';
        $codeSouscription = $this->validator->generateCode('souscriptions', 'code_souscription', 'SUB-', 8);

        $packCodes = $data['pack_codes'];

        $inClause = implode(',', array_fill(0, count($packCodes), '?'));
        $stmtP = $this->model->getCon()->prepare("SELECT SUM(prix_cotisation_pack) as total_prix FROM packs WHERE code_pack IN ($inClause)");
        $stmtP->execute($packCodes);
        $resP = $stmtP->fetch(PDO::FETCH_ASSOC);
        $cotisJour = (float)($resP['total_prix'] ?? 0);

        $stmtS = $this->model->getCon()->prepare("SELECT nombre_jour_session FROM sessions WHERE code_session = ?");
        $stmtS->execute([$sessionCode]);
        $resS = $stmtS->fetch(PDO::FETCH_ASSOC);
        $nbJours = (int)($resS['nombre_jour_session'] ?? 170);

        $montantTotalPrevu = $cotisJour * $nbJours;

        $souscriptionData = [
            'code_souscription' => $codeSouscription,
            'client_code' => $data['client_code'],
            'session_code' => $sessionCode,
            'zone_code' => $zoneCode,
            'date_debut_souscription' => date('Y-m-d'),
            'montant_total_prevu' => $montantTotalPrevu,
            'montant_cotisation_journaliere' => $cotisJour,
            'nombre_jour_total' => $nbJours,
            'nombre_jour_cotise' => 0,
            'montant_total_cotise' => 0,
            'statut_distribution' => 'En attente',
            'statut_souscription' => 'valide',
            'user_code' => $userCode,
            'etablissement_code' => $etabCode,
            'annee_code' => $anneeCode,
            'created_at_souscription' => date('Y-m-d H:i:s')
        ];

        if ($this->model->createSouscriptionWithMultiplePacks($souscriptionData, $packCodes)) {
            $this->success('Souscription enregistrée avec succès !', [
                'code_souscription' => $codeSouscription,
                'redirect' => RACINE . 'souscription/list'
            ]);
        } else {
            $this->error('Erreur lors de la validation de la souscription.');
        }
    }

    public function wizardSubmit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $data = $_POST;
        unset($data['csrf_token']);

        $nomClient = trim($data['nom_client'] ?? '');
        $telClient = Validator::cleanPhone($data['telephone_client'] ?? '');
        $emailClient = trim($data['email_client'] ?? '');
        $sexeClient = trim($data['sexe_client'] ?? '');
        $lieuClient = trim($data['lieu_residence_client'] ?? '');
        $professionClient = trim($data['profession_client'] ?? '');
        $sessionCode = $data['session_code'] ?? '';
        $zoneCode = $data['zone_code'] ?? Context::zone();

        $rawPacks = $data['packs'] ?? '[]';
        $packCodes = is_array($rawPacks) ? $rawPacks : json_decode($rawPacks, true);

        if (empty($nomClient) || empty($telClient) || empty($sexeClient) || empty($lieuClient)) {
            $this->error('Veuillez remplir toutes les informations du client (Nom, Téléphone, Genre, Lieu de résidence).');
            return;
        }

        if (empty($sessionCode)) {
            $this->error('Veuillez sélectionner une session d\'activité.');
            return;
        }

        if (empty($packCodes) || !is_array($packCodes)) {
            $this->error('Veuillez sélectionner au moins un pack.');
            return;
        }

        $db = $this->model->getCon();

        // 1. DÉTECTION ET ANTI-DOUBLON CLIENT : Vérification si le client existe déjà
        $existingClient = null;
        if (!empty($telClient)) {
            $stmtCheck = $db->prepare("SELECT * FROM clients WHERE telephone_client = ? LIMIT 1");
            $stmtCheck->execute([$telClient]);
            $existingClient = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        }

        if (!$existingClient && !empty($nomClient) && !empty($lieuClient)) {
            $stmtCheckNom = $db->prepare("SELECT * FROM clients WHERE LOWER(nom_client) = LOWER(?) AND LOWER(lieu_residence_client) = LOWER(?) LIMIT 1");
            $stmtCheckNom->execute([$nomClient, $lieuClient]);
            $existingClient = $stmtCheckNom->fetch(PDO::FETCH_ASSOC);
        }

        if ($existingClient) {
            // REUTILISATION DU CLIENT EXISTANT (Pas de création de doublon)
            $clientCode = $existingClient['code_client'];

            // Mettre à jour les informations secondaires si manquantes
            $updateFields = [];
            if (empty($existingClient['email_client']) && !empty($emailClient)) $updateFields['email_client'] = $emailClient;
            if (empty($existingClient['profession_client']) && !empty($professionClient)) $updateFields['profession_client'] = $professionClient;
            if (!empty($updateFields)) {
                $updateFields['updated_at_client'] = date('Y-m-d H:i:s');
                $modelClient = new ModelClient();
                $modelClient->update($updateFields, (int)$existingClient['id_client']);
            }
        } else {
            // NOUVEAU CLIENT : Création d'une fiche client unique
            $clientCode = $this->validator->generateCode('clients', 'code_client', 'CLI-', 8);
            if (empty($zoneCode)) {
                $stmtDZ = $db->query("SELECT code_zone FROM zones LIMIT 1");
                $dz = $stmtDZ->fetch(PDO::FETCH_ASSOC);
                $zoneCode = $dz['code_zone'] ?? '6QIlVfXP0LiXE9tBzHownYLAAqDi2';
            }

            $clientData = [
                'code_client' => $clientCode,
                'nom_client' => $nomClient,
                'telephone_client' => $telClient,
                'email_client' => $emailClient,
                'sexe_client' => $sexeClient,
                'lieu_residence_client' => $lieuClient,
                'profession_client' => $professionClient,
                'statut_client' => 'actif',
                'created_at_client' => date('Y-m-d H:i:s'),
                'user_code' => Context::user() ?? '',
                'etablissement_code' => Context::etablissement(),
                'zone_code' => $zoneCode
            ];
            $modelClient = new ModelClient();
            if (!$modelClient->create($clientData)) {
                $this->error('Erreur lors de la création de la fiche client.');
                return;
            }
        }

        // 2. CRÉATION DE LA SOUSCRIPTION
        $userCode = Context::user() ?? '';
        $etabCode = Context::etablissement();
        $anneeCode = Context::annee();
        $codeSouscription = $this->validator->generateCode('souscriptions', 'code_souscription', 'SUB-', 8);

        $inClause = implode(',', array_fill(0, count($packCodes), '?'));
        $stmtP = $db->prepare("SELECT SUM(prix_cotisation_pack) as total_prix FROM packs WHERE code_pack IN ($inClause)");
        $stmtP->execute($packCodes);
        $resP = $stmtP->fetch(PDO::FETCH_ASSOC);
        $cotisJour = (float)($resP['total_prix'] ?? 0);

        $stmtS = $db->prepare("SELECT nombre_jour_session FROM sessions WHERE code_session = ?");
        $stmtS->execute([$sessionCode]);
        $resS = $stmtS->fetch(PDO::FETCH_ASSOC);
        $nbJours = (int)($resS['nombre_jour_session'] ?? 170);

        $montantTotalPrevu = $cotisJour * $nbJours;

        $souscriptionData = [
            'code_souscription' => $codeSouscription,
            'client_code' => $clientCode,
            'session_code' => $sessionCode,
            'zone_code' => $zoneCode ?: (Context::zone() ?? ''),
            'date_debut_souscription' => date('Y-m-d'),
            'montant_total_prevu' => $montantTotalPrevu,
            'montant_cotisation_journaliere' => $cotisJour,
            'nombre_jour_total' => $nbJours,
            'nombre_jour_cotise' => 0,
            'montant_total_cotise' => 0,
            'statut_distribution' => 'En attente',
            'statut_souscription' => 'valide',
            'user_code' => $userCode,
            'etablissement_code' => $etabCode,
            'annee_code' => $anneeCode,
            'created_at_souscription' => date('Y-m-d H:i:s')
        ];

        if ($this->model->createSouscriptionWithMultiplePacks($souscriptionData, $packCodes)) {
            $msgSuccess = $existingClient 
                ? "Souscription rattachée au client existant '{$existingClient['nom_client']}' ($clientCode) avec succès !"
                : "Nouveau client créé ($clientCode) et souscription enregistrée avec succès !";
            $this->success($msgSuccess, [
                'code_souscription' => $codeSouscription,
                'redirect' => RACINE . 'souscription/list'
            ]);
        } else {
            $this->error('Erreur lors de la validation de la souscription.');
        }
    }
}
