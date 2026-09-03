<?php

class FournisseurAvicoleController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelFournisseurAvicole();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/aviculture/fournisseurs_avicoles.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        $stmt = $db->query("SELECT * FROM fournisseurs_avicoles ORDER BY id_fournisseur_avicole DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        foreach ($rows as $row) {
            $row['editId'] = $this->validator->crypter($row['id_fournisseur_avicole']);
            $data[] = $row;
        }

        $this->json(['data' => $data]);
    }

    public function add()
    {
        $this->requirePost(false);
        $this->requireAuth();

        $nom = trim($this->post('nom_fournisseur_avicole') ?? '');
        $cat = $this->post('categorie_intrants') ?? 'aliments';
        $tel = trim($this->post('telephone_fournisseur_avicole') ?? '');
        $email = trim($this->post('email_fournisseur_avicole') ?? '');
        $adresse = trim($this->post('adresse_fournisseur_avicole') ?? '');

        if (empty($nom)) {
            $this->error("Le nom du fournisseur avicole est obligatoire.");
            return;
        }

        $code = 'FRS-AV-' . rand(100, 999) . rand(10, 99);
        $db = $this->model->getCon();

        $stmt = $db->prepare("
            INSERT INTO fournisseurs_avicoles (
                code_fournisseur_avicole, nom_fournisseur_avicole, categorie_intrants, 
                telephone_fournisseur_avicole, email_fournisseur_avicole, adresse_fournisseur_avicole, 
                user_code, etablissement_code
            ) VALUES (
                :code, :nom, :cat, :tel, :email, :adresse, :user, :etab
            )
        ");

        $user_code = $_SESSION[USERS_AUTH]['code_user'] ?? 'USR-ADMIN-001';
        $etab_code = $_SESSION[USERS_AUTH]['etablissement_code'] ?? '5454544456';

        if ($stmt->execute([
            ':code' => $code,
            ':nom' => $nom,
            ':cat' => $cat,
            ':tel' => $tel,
            ':email' => $email,
            ':adresse' => $adresse,
            ':user' => $user_code,
            ':etab' => $etab_code
        ])) {
            $this->success("Fournisseur avicole enregistré avec succès !");
        } else {
            $this->error("Erreur lors de la création du fournisseur avicole.");
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
            $this->error('Fournisseur introuvable');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id_fournisseur_avicole');
        if (!$id) {
            $this->error('Identifiant fournisseur invalide.');
            return;
        }

        $nom = trim($this->post('nom_fournisseur_avicole') ?? '');
        $cat = $this->post('categorie_intrants') ?? 'aliments';
        $tel = trim($this->post('telephone_fournisseur_avicole') ?? '');
        $email = trim($this->post('email_fournisseur_avicole') ?? '');
        $adresse = trim($this->post('adresse_fournisseur_avicole') ?? '');

        if (empty($nom)) {
            $this->error("Le nom du fournisseur avicole est obligatoire.");
            return;
        }

        $db = $this->model->getCon();
        $stmt = $db->prepare("
            UPDATE fournisseurs_avicoles 
            SET nom_fournisseur_avicole = :nom,
                categorie_intrants = :cat,
                telephone_fournisseur_avicole = :tel,
                email_fournisseur_avicole = :email,
                adresse_fournisseur_avicole = :adresse
            WHERE id_fournisseur_avicole = :id
        ");

        if ($stmt->execute([
            ':nom' => $nom,
            ':cat' => $cat,
            ':tel' => $tel,
            ':email' => $email,
            ':adresse' => $adresse,
            ':id' => $id
        ])) {
            $this->success("Fournisseur avicole mis à jour avec succès !");
        } else {
            $this->error("Erreur lors de la modification du fournisseur.");
        }
    }
}
