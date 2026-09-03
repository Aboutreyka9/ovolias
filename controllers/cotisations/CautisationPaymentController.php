<?php

class CautisationPaymentController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelCotisation();
    }

    /**
     * Affiche le formulaire de recherche de souscription
     */
    public function searchForm()
    {
        $this->requireAuth();
        $this->loadView('../views/cautisations_payment/search.php');
    }

    /**
     * API: Recherche des souscriptions selon les critères
     * Critères: telephone, nom_client, code_client, code_souscription
     */
    public function search()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Méthode POST requise'], 405);
            return;
        }

        $criteria = $this->post('criteria') ?? '';
        $type = $this->post('type') ?? 'all'; // all, phone, name, code, subscription

        if (empty($criteria)) {
            $this->json(['error' => 'Veuillez entrer un critère de recherche'], 400);
            return;
        }

        $souscriptions = $this->searchSouscriptions($criteria, $type);

        if (empty($souscriptions)) {
            $this->json(['message' => 'Aucune souscription trouvée'], 404);
            return;
        }

        $data = [];
        foreach ($souscriptions as $s) {
            $montantTotal = $this->getTotalPackAmount($s['code_souscription']);
            $data[] = [
                'code_souscription' => $s['code_souscription'],
                'nom_client' => $s['nom_client'] ?? '-',
                'prenom_client' => '',
                'nom_complet' => trim(($s['nom_client'] ?? '')),
                'telephone' => $s['telephone_client'] ?? '-',
                'libelle_session' => $s['libelle_session'] ?? '-',
                'montant_total' => $montantTotal,
                'statut' => $s['statut_souscription'] ?? '-'
            ];
        }

        $this->json(['data' => $data]);
    }

    /**
     * Affiche la situation d'une souscription
     */
    public function situation($codesouscription = null)
    {
        $this->requireAuth();

        $code = $codesouscription ?? ($_GET['code'] ?? null);
        if (!$code) {
            header('Location: ' . RACINE . 'cautisation-payment/search-form');
            exit();
        }

        $souscription = $this->getSouscriptionWithDetails($code);
        if (!$souscription) {
            $this->renderNotFound('Souscription introuvable ou non autorisée.');
            return;
        }

        $this->loadView('../views/cautisations_payment/situation.php', [
            'souscription' => $souscription
        ]);
    }

    /**
     * API: Récupère les détails complets d'une souscription pour affichage
     */
    public function situationDetails()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Méthode POST requise'], 405);
            return;
        }

        $codeSouscription = $this->post('code_souscription') ?? '';
        if (empty($codeSouscription)) {
            $this->json(['error' => 'Code de souscription manquant'], 400);
            return;
        }

        $souscription = $this->getSouscriptionWithDetails($codeSouscription);
        if (!$souscription) {
            $this->json(['error' => 'Souscription introuvable'], 404);
            return;
        }

        $this->json(['data' => $souscription]);
    }

    /**
     * API: Simule l'impact d'un paiement avant de soumettre
     */
    public function simulate()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Méthode POST requise'], 405);
            return;
        }

        $codeSouscription = $this->post('code_souscription') ?? '';
        $montant = (float) ($this->post('montant') ?? 0);

        if (empty($codeSouscription) || $montant <= 0) {
            $this->json(['error' => 'Code souscription et montant valide requis'], 400);
            return;
        }

        $souscription = $this->getSouscriptionWithDetails($codeSouscription);
        if (!$souscription) {
            $this->json(['error' => 'Souscription introuvable'], 404);
            return;
        }

        $cotisationJour = (float) $souscription['prix_cotisation_journaliere'];
        $nombreJoursCalcules = CautisationValidator::calculateDaysFromAmount($montant, $cotisationJour);

        $nouveauTotalCotise = (float) $souscription['total_cotise'] + $montant;
        $nouveauSoldeRestant = max(0, (float) $souscription['montant_total'] - $nouveauTotalCotise);
        $nouveauxJoursPayes = (int) $souscription['nombre_jours_payes'] + $nombreJoursCalcules;
        $nouveauxJoursRestants = max(0, (int) $souscription['duree_totale_jours'] - $nouveauxJoursPayes);
        $nouvelleProgression = CautisationValidator::calculateProgressPercentage($nouveauTotalCotise, (float) $souscription['montant_total']);

        $nouveauStatut = $nouveauSoldeRestant <= 0 ? 'solde' : $souscription['statut_souscription'];
        $dateProchainRdv = CautisationValidator::calculateNextDate($nombreJoursCalcules);

        $this->json([
            'data' => [
                'montant_saisi' => $montant,
                'nombre_jours_payes' => $nombreJoursCalcules,
                'nouveau_total_cotise' => $nouveauTotalCotise,
                'nouveau_solde_restant' => $nouveauSoldeRestant,
                'nouveaux_jours_payes' => $nouveauxJoursPayes,
                'nouveaux_jours_restants' => $nouveauxJoursRestants,
                'nouvelle_progression' => $nouvelleProgression,
                'nouveau_statut' => $nouveauStatut,
                'prochain_rdv' => $dateProchainRdv
            ]
        ]);
    }

    /**
     * API: Traite l'enregistrement d'un paiement
     */
    public function store()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Méthode POST requise'], 405);
            return;
        }

        $codeSouscription = $this->post('code_souscription') ?? '';
        $montant = (float) ($this->post('montant') ?? 0);
        $modePaiement = $this->post('mode_paiement') ?? 'ESPECES';
        $nombreJoursManuel = $this->post('nombre_jours') ? (int) $this->post('nombre_jours') : null;

        if (empty($codeSouscription) || $montant <= 0) {
            $this->error('Code souscription et montant valide requis !');
            return;
        }

        $souscription = $this->getSouscriptionWithDetails($codeSouscription);
        if (!$souscription) {
            $this->error('Souscription introuvable !');
            return;
        }

        if ($souscription['statut_souscription'] === 'solde') {
            $this->error('Cette souscription est déjà entièrement soldée !');
            return;
        }

        $cotisationJour = (float) $souscription['prix_cotisation_journaliere'];

        if ($nombreJoursManuel !== null && $nombreJoursManuel > 0) {
            $nombreJours = $nombreJoursManuel;
        } else {
            $nombreJours = CautisationValidator::calculateDaysFromAmount($montant, $cotisationJour);
        }

        $validation = CautisationValidator::validatePayment($montant, (float) $souscription['solde_restant']);
        if (!$validation['valid']) {
            $this->error($validation['message']);
            return;
        }

        $userCode = Context::user() ?? '';
        $anneeCode = Context::annee();
        $etabCode = Context::etablissement();
        $zoneCode = Context::zone() ?? ($souscription['zone_code'] ?? '');
        $codeCautisation = $this->validator->generateCode('cautisation_clients', 'code_cautisation_client', 'COT-', 8);

        $caisse = $this->getOpenCaisse($zoneCode, $etabCode);
        
        // RÈGLE DE SÉCURITÉ : Si le commercial n'a pas ouvert sa caisse aujourd'hui, bloquer la saisie de cotisation
        if (Context::isCommercial() && !$caisse) {
            $this->error('Votre caisse est actuellement FERMÉE pour aujourd\'hui. Veuillez effectuer l\'ouverture de caisse avant de collecter des cotisations.');
            return;
        }

        $caisseCode = $caisse['code_caisse'] ?? ($caisse['code_ouverture'] ?? 'CAISSE-DEFAULT');

        // RÈGLE RBAC : Statut initial = 'en_attente' pour les commerciaux, 'valide' pour finance/admin
        $statutInitial = Context::isCommercial() ? 'en_attente' : 'valide';

        $cautisationData = [
            'code_cautisation_client' => $codeCautisation,
            'souscription_code' => $codeSouscription,
            'client_code' => $souscription['client_code'] ?? $userCode,
            'commercial_code' => $userCode,
            'date_cautisation' => date('Y-m-d H:i:s'),
            'montant_cautisation_client' => $montant,
            'nombre_jour' => $nombreJours,
            'nombre_jour_paye' => $nombreJours,
            'statut_cautisation_client' => $statutInitial,
            'mode_paiement' => $modePaiement,
            'created_at_cautisation_client' => date('Y-m-d H:i:s'),
            'updated_at_cautisation_client' => date('Y-m-d H:i:s'),
            'etablissement_code' => $etabCode,
            'user_code' => $userCode,
            'annee_code' => $anneeCode,
            'zone_code' => $zoneCode,
            'caisse_code' => $caisseCode
        ];

        if ($this->model->createCotisation($cautisationData)) {
            $dateProchainRdv = CautisationValidator::calculateNextDate($nombreJours);
            $msg = Context::isCommercial()
                ? 'Cotisation enregistrée avec succès (En attente de validation par la caisse/finance).'
                : 'Cotisation enregistrée et validée avec succès !';

            $this->success($msg, [
                'code_cautisation' => $codeCautisation,
                'prochain_rdv' => $dateProchainRdv,
                'reload' => true
            ]);
        } else {
            $this->error('Erreur lors de l\'enregistrement du paiement');
        }
    }

    private function searchSouscriptions(string $criteria, string $type): array
    {
        $con = $this->model->getCon();

        $sql = "
            SELECT s.*, 
                   c.nom_client, c.telephone_client,
                   sess.libelle_session, sess.nombre_jour_session
             FROM souscriptions s
            LEFT JOIN clients c ON c.code_client = s.client_code
            LEFT JOIN sessions sess ON sess.code_session = s.session_code
            WHERE s.statut_souscription = 'valide'
        ";

        $paramsBase = [];
        if (Context::isCommercial()) {
            $sql .= " AND s.user_code = ?";
            $paramsBase[] = Context::user() ?? '';
        }

        if ($type === 'phone' || $type === 'all') {
            $stmt = $con->prepare($sql . " AND c.telephone_client LIKE ?");
            $params = array_merge($paramsBase, ['%' . $criteria . '%']);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($results)) return $results;
        }

        if ($type === 'name' || $type === 'all') {
            $stmt = $con->prepare($sql . " AND c.nom_client LIKE ?");
            $params = array_merge($paramsBase, ['%' . $criteria . '%']);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($results)) return $results;
        }

        if ($type === 'code' || $type === 'all') {
            $stmt = $con->prepare($sql . " AND c.code_client LIKE ?");
            $params = array_merge($paramsBase, ['%' . $criteria . '%']);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($results)) return $results;
        }

        if ($type === 'subscription' || $type === 'all') {
            $stmt = $con->prepare($sql . " AND s.code_souscription LIKE ?");
            $params = array_merge($paramsBase, ['%' . $criteria . '%']);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($results)) return $results;
        }

        return [];
    }

    private function getSouscriptionWithDetails(string $codeSouscription): ?array
    {
        $con = $this->model->getCon();

        $sql = "
            SELECT s.*, 
                   c.nom_client, c.telephone_client, c.code_client,
                   sess.libelle_session, sess.nombre_jour_session,
                   z.libelle_zone, z.code_zone
            FROM souscriptions s
            LEFT JOIN clients c ON c.code_client = s.client_code
            LEFT JOIN sessions sess ON sess.code_session = s.session_code
            LEFT JOIN zones z ON z.code_zone = s.zone_code
            WHERE s.code_souscription = ?
        ";
        $params = [$codeSouscription];

        if (Context::isCommercial()) {
            $sql .= " AND s.user_code = ?";
            $params[] = Context::user();
        }

        $stmt = $con->prepare($sql);
        $stmt->execute($params);
        $souscription = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$souscription) return null;

        $packs = $this->getPacksSouscrits($codeSouscription);
        $prixCotisationJournaliere = (float) array_sum(array_column($packs, 'prix_cotisation_pack'));

        $nombreJourSession = (int) ($souscription['nombre_jour_session'] ?? 0);
        $montantTotalPrevu = (float) ($prixCotisationJournaliere * $nombreJourSession);

        $totaux = $this->getCautisationsTotaux($codeSouscription);
        $totalCotise = (float) ($totaux['total_cotise'] ?? 0);
        $nombreJoursPayes = (int) ($totaux['nombre_jours_payes'] ?? 0);

        $soldeRestant = max(0, $montantTotalPrevu - $totalCotise);
        $joursRestants = max(0, $nombreJourSession - $nombreJoursPayes);
        $progression = CautisationValidator::calculateProgressPercentage($totalCotise, $montantTotalPrevu);

        $historique = $this->getHistoriqueCautisations($codeSouscription);

        return array_merge($souscription, [
            'nom_client' => $souscription['nom_client'] ?? '',
            'prenom_client' => '',
            'nom_complet' => trim(($souscription['nom_client'] ?? '')),
            'packs' => $packs,
            'prix_cotisation_journaliere' => $prixCotisationJournaliere,
            'montant_total' => $montantTotalPrevu,
            'duree_totale_jours' => $nombreJourSession,
            'total_cotise' => $totalCotise,
            'nombre_jours_payes' => $nombreJoursPayes,
            'solde_restant' => $soldeRestant,
            'jours_restants' => $joursRestants,
            'progression' => $progression,
            'historique' => $historique
        ]);
    }

    private function getPacksSouscrits(string $codeSouscription): array
    {
        $con = $this->model->getCon();
        $stmt = $con->prepare("
            SELECT p.code_pack, p.libelle_pack, p.prix_cotisation_pack, cp.libelle_categorie_pack
            FROM pack_souscriptions ps
            JOIN packs p ON p.code_pack = ps.pack_code
            LEFT JOIN categorie_packs cp ON cp.code_categorie_pack = p.categorie_pack_code
            WHERE ps.souscription_code = ?
        ");
        $stmt->execute([$codeSouscription]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function getCautisationsTotaux(string $codeSouscription): array
    {
        $con = $this->model->getCon();
        $stmt = $con->prepare("
            SELECT 
                COALESCE(SUM(montant_cautisation_client), 0) as total_cotise,
                COALESCE(SUM(nombre_jour), 0) as nombre_jours_payes
            FROM cautisation_clients
            WHERE souscription_code = ? AND statut_cautisation_client = 'valide'
        ");
        $stmt->execute([$codeSouscription]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_cotise' => 0, 'nombre_jours_payes' => 0];
    }

    private function getHistoriqueCautisations(string $codeSouscription): array
    {
        $con = $this->model->getCon();
        $stmt = $con->prepare("
            SELECT c.*, u.nom_user as nom_commercial, u.prenom_user as prenom_commercial
            FROM cautisation_clients c
            LEFT JOIN users u ON u.code_user = c.commercial_code
            WHERE c.souscription_code = ?
            ORDER BY c.date_cautisation DESC
        ");
        $stmt->execute([$codeSouscription]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $result = [];
        foreach ($rows as $r) {
            $result[] = [
                'code_cautisation_client' => $r['code_cautisation_client'],
                'date_cautisation' => $r['date_cautisation'],
                'montant' => (float) $r['montant_cautisation_client'],
                'nombre_jours' => (int) $r['nombre_jour'],
                'mode_paiement' => $r['mode_paiement'] ?? 'ESPECES',
                'commercial_nom' => trim(($r['nom_commercial'] ?? '') . ' ' . ($r['prenom_commercial'] ?? '')),
                'statut' => $r['statut_cautisation_client'] ?? 'valide'
            ];
        }
        return $result;
    }

    private function getTotalPackAmount(string $codeSouscription): float
    {
        $con = $this->model->getCon();
        $stmt = $con->prepare("
            SELECT ((SELECT COALESCE(SUM(p2.prix_cotisation_pack), 0) FROM pack_souscriptions ps2 JOIN packs p2 ON p2.code_pack = ps2.pack_code WHERE ps2.souscription_code = s.code_souscription) * COALESCE(sess.nombre_jour_session, 0)) as total
            FROM souscriptions s
            LEFT JOIN sessions sess ON sess.code_session = s.session_code
            WHERE s.code_souscription = ?
        ");
        $stmt->execute([$codeSouscription]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float) ($row['total'] ?? 0);
    }

    private function getOpenCaisse(string $zoneCode, string $etabCode): ?array
    {
        $con = $this->model->getCon();
        $userCode = Context::user();
        $dateToday = date('Y-m-d');

        // 1. Chercher une ouverture de caisse active pour ce commercial aujourd'hui
        if (Context::isCommercial()) {
            $stmt = $con->prepare("
                SELECT code_caisse, montant_total_attendu as fond_initial, date_ouverture
                FROM caisses 
                WHERE user_code = ? AND DATE(date_ouverture) = ? AND statut_caisse = 'ouverte'
                ORDER BY id_caisse DESC LIMIT 1
            ");
            $stmt->execute([$userCode, $dateToday]);
            $ouv = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($ouv) return $ouv;
        }

        // 2. Fallback caisses agence/établissement
        $stmt = $con->prepare("
            SELECT * FROM caisses 
            WHERE etablissement_code = ? AND statut_caisse = 'ouverte'
            ORDER BY id_caisse DESC LIMIT 1
        ");
        $stmt->execute([$etabCode]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
