<?php

class ClientAvicoleController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelClientAvicole();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/aviculture/clients_avicoles.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $db = $this->model->getCon();

        $stmt = $db->query("SELECT * FROM clients_avicoles ORDER BY id_client_avicole DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        foreach ($rows as $row) {
            $row['editId'] = $this->validator->crypter($row['id_client_avicole']);
            $data[] = $row;
        }

        $this->json(['data' => $data]);
    }

    public function add()
    {
        $this->requirePost(false);
        $this->requireAuth();

        $nom = trim($this->post('nom_client_avicole') ?? '');
        $type = $this->post('type_client_avicole') ?? 'particulier';
        $tel = trim($this->post('telephone_client_avicole') ?? '');
        $email = trim($this->post('email_client_avicole') ?? '');
        $adresse = trim($this->post('adresse_client_avicole') ?? '');

        if (empty($nom)) {
            $this->error("Le nom du client avicole est obligatoire.");
            return;
        }

        $code = 'CLT-AV-' . rand(100, 999) . rand(10, 99);
        $db = $this->model->getCon();

        $stmt = $db->prepare("
            INSERT INTO clients_avicoles (
                code_client_avicole, nom_client_avicole, type_client_avicole, 
                telephone_client_avicole, email_client_avicole, adresse_client_avicole, 
                user_code, etablissement_code
            ) VALUES (
                :code, :nom, :type, :tel, :email, :adresse, :user, :etab
            )
        ");

        $user_code = $_SESSION[USERS_AUTH]['code_user'] ?? 'USR-ADMIN-001';
        $etab_code = $_SESSION[USERS_AUTH]['etablissement_code'] ?? '5454544456';

        if ($stmt->execute([
            ':code' => $code,
            ':nom' => $nom,
            ':type' => $type,
            ':tel' => $tel,
            ':email' => $email,
            ':adresse' => $adresse,
            ':user' => $user_code,
            ':etab' => $etab_code
        ])) {
            $this->success("Client avicole enregistré avec succès !");
        } else {
            $this->error("Erreur lors de la création du client avicole.");
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
            $this->error('Client introuvable');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id_client_avicole');
        if (!$id) {
            $this->error('Identifiant client invalide.');
            return;
        }

        $nom = trim($this->post('nom_client_avicole') ?? '');
        $type = $this->post('type_client_avicole') ?? 'particulier';
        $tel = trim($this->post('telephone_client_avicole') ?? '');
        $email = trim($this->post('email_client_avicole') ?? '');
        $adresse = trim($this->post('adresse_client_avicole') ?? '');

        if (empty($nom)) {
            $this->error("Le nom du client avicole est obligatoire.");
            return;
        }

        $db = $this->model->getCon();
        $stmt = $db->prepare("
            UPDATE clients_avicoles 
            SET nom_client_avicole = :nom,
                type_client_avicole = :type,
                telephone_client_avicole = :tel,
                email_client_avicole = :email,
                adresse_client_avicole = :adresse
            WHERE id_client_avicole = :id
        ");

        if ($stmt->execute([
            ':nom' => $nom,
            ':type' => $type,
            ':tel' => $tel,
            ':email' => $email,
            ':adresse' => $adresse,
            ':id' => $id
        ])) {
            $this->success("Client avicole mis à jour avec succès !");
        } else {
            $this->error("Erreur lors de la modification du client.");
        }
    }
}
