<?php

class AchatAvicoleController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelAchatAvicole();
    }

    public function list()
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        $fournisseurs = $db->query("SELECT * FROM fournisseurs_avicoles WHERE statut_fournisseur_avicole = 'actif' ORDER BY nom_fournisseur_avicole ASC")->fetchAll(PDO::FETCH_ASSOC);

        $this->loadView('../views/aviculture/achats_avicoles.php', [
            'fournisseurs' => $fournisseurs
        ]);
    }

    public function apiList()
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        $stmt = $db->query("
            SELECT a.*, f.nom_fournisseur_avicole, f.categorie_intrants, u.nom_user, u.prenom_user
            FROM achats_avicoles a
            LEFT JOIN fournisseurs_avicoles f ON a.fournisseur_avicole_code = f.code_fournisseur_avicole
            LEFT JOIN users u ON a.user_code = u.code_user
            ORDER BY a.id_achat_avicole DESC
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        foreach ($rows as $row) {
            $row['editId'] = $this->validator->crypter($row['id_achat_avicole']);
            $row['fournisseur_nom'] = $row['nom_fournisseur_avicole'] ?? 'Fournisseur Général';
            $row['agent_nom'] = trim(($row['nom_user'] ?? '') . ' ' . ($row['prenom_user'] ?? ''));
            $data[] = $row;
        }

        $this->json(['data' => $data]);
    }

    public function addAchat()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $db = $this->model->getCon();

        $fournisseur_code = $this->post('fournisseur_avicole_code');
        $num_facture = trim($this->post('numero_facture_fournisseur') ?? '');
        $categorie = $this->post('categorie_intrant') ?? 'aliments';
        $article = trim($this->post('libelle_article') ?? '');
        $qte = (float)$this->post('quantite');
        $unite = trim($this->post('unite_mesure') ?? 'sac');
        $pu = (float)$this->post('prix_unitaire');
        $statut_reg = $this->post('statut_reglement') ?? 'paye';

        if (empty($fournisseur_code) || empty($article) || $qte <= 0 || $pu <= 0) {
            $this->error("Veuillez remplir tous les champs obligatoires (fournisseur, désignation, quantité, prix unitaire).");
            return;
        }

        $montant_total = $qte * $pu;
        $montant_paye = ($statut_reg === 'paye') ? $montant_total : 0;
        $code_achat = 'ACH-AV-' . date('Ymd') . '-' . rand(1000, 9999);

        $user_code = $_SESSION[USERS_AUTH]['code_user'] ?? 'USR-ADMIN-001';
        $etab_code = $_SESSION[USERS_AUTH]['etablissement_code'] ?? '5454544456';
        $zone_code = $_SESSION[USERS_AUTH]['zone_user'] ?? 'DEFAULT';

        // 1. Insérer l'en-tête d'achat
        $stmtA = $db->prepare("
            INSERT INTO achats_avicoles (
                code_achat_avicole, fournisseur_avicole_code, numero_facture_fournisseur, 
                categorie_intrant, montant_total, montant_paye, statut_reglement, 
                date_achat, user_code, etablissement_code, zone_code
            ) VALUES (
                :code, :frs, :num_fac, 
                :cat, :tot, :paye, :statut_reg, 
                NOW(), :user, :etab, :zone
            )
        ");
        $stmtA->execute([
            ':code' => $code_achat,
            ':frs' => $fournisseur_code,
            ':num_fac' => $num_facture,
            ':cat' => $categorie,
            ':tot' => $montant_total,
            ':paye' => $montant_paye,
            ':statut_reg' => $statut_reg,
            ':user' => $user_code,
            ':etab' => $etab_code,
            ':zone' => $zone_code
        ]);

        // 2. Insérer la ligne de détail
        $stmtDet = $db->prepare("
            INSERT INTO details_achats_avicoles (
                achat_code, libelle_article_intrant, quantite, 
                unite_mesure, prix_unitaire, montant_total
            ) VALUES (
                :code, :article, :qte, 
                :unite, :pu, :tot
            )
        ");
        $stmtDet->execute([
            ':code' => $code_achat,
            ':article' => $article,
            ':qte' => $qte,
            ':unite' => $unite,
            ':pu' => $pu,
            ':tot' => $montant_total
        ]);

        $this->success("Achat d'intrant enregistré avec succès ! Référence : {$code_achat}", [
            'code_achat' => $code_achat,
            'montant_total' => number_format($montant_total, 0, ',', ' ') . ' FCFA'
        ]);
    }
}
