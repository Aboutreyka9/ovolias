<?php

class UserController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelUser();
    }

    public function list()
    {
        $this->requireAuth();
        $this->loadView('../views/users/list.php');
    }

    public function apiList()
    {
        $this->requireAuth();
        $sql = "SELECT u.id_user, u.code_user, u.nom_user, u.prenom_user, u.email_user, u.telephone_user, u.statut_user, u.fonction_code, u.token_user, u.zone_user,
                       z.libelle_zone,
                       GROUP_CONCAT(DISTINCT r.libelle_role ORDER BY r.id SEPARATOR '||') as roles_libelles,
                       GROUP_CONCAT(DISTINCT r.code_role ORDER BY r.id SEPARATOR ',') as roles_codes,
                       f.libelle_fonction
                FROM users u
                LEFT JOIN user_roles ur ON ur.user_code = u.code_user
                LEFT JOIN roles r ON r.code_role = ur.role_code
                LEFT JOIN fonctions f ON f.code_fonction = u.fonction_code
                LEFT JOIN zones z ON z.code_zone = u.zone_user
                GROUP BY u.id_user, u.code_user, u.nom_user, u.prenom_user, u.email_user, u.telephone_user, u.statut_user, u.fonction_code, u.token_user, u.zone_user, z.libelle_zone, f.libelle_fonction
                ORDER BY u.id_user DESC";
        $users = $this->model->getCon()->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        foreach ($users as $u) {
            $idCrypte = $this->validator->crypter($u['id_user']);
            $roleNames = !empty($u['roles_libelles']) ? explode('||', $u['roles_libelles']) : [];
            $roleCodes = !empty($u['roles_codes']) ? explode(',', $u['roles_codes']) : [];
            $data[] = [
                'code' => $u['code_user'],
                'nom' => $u['nom_user'],
                'prenom' => $u['prenom_user'] ?? '',
                'email' => $u['email_user'] ?? '',
                'telephone' => $u['telephone_user'] ?? '',
                'fonction' => $u['libelle_fonction'] ?? '-',
                'zone' => !empty($u['libelle_zone']) ? $u['libelle_zone'] : (!empty($u['zone_user']) ? $u['zone_user'] : 'Globale'),
                'zone_code' => $u['zone_user'] ?? '',
                'role' => !empty($roleNames) ? implode(', ', $roleNames) : 'Non attribué',
                'roles_list' => $roleNames,
                'role_code' => !empty($roleCodes) ? $roleCodes[0] : '',
                'roles_codes' => $roleCodes,
                'statut' => $u['statut_user'],
                'token_pending' => !empty($u['token_user']),
                'id' => $u['id_user'],
                'editId' => $idCrypte
            ];
        }

        $this->json(['data' => $data]);
    }

    public function add()
    {
        $this->requirePost(false);
        $this->requireAuth();

        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $telephone = Validator::cleanPhone($_POST['telephone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        // Multi-rôles : supporte tableau roles[] ou role_code simple
        $postedRoles = $_POST['roles'] ?? ($_POST['role_code'] ?? ['ROLE_SCOLARITE']);
        if (!is_array($postedRoles)) {
            $postedRoles = [$postedRoles];
        }
        $postedRoles = array_values(array_unique(array_filter(array_map('trim', $postedRoles))));
        if (empty($postedRoles)) {
            $postedRoles = ['ROLE_SCOLARITE'];
        }

        $fonctionCode = $_POST['fonction_code'] ?? null;
        $rawPassword = !empty($_POST['password']) ? trim($_POST['password']) : ('USR' . rand(100000, 999999));

        if (empty($nom)) {
            $this->error('Le nom de l\'utilisateur est obligatoire !');
            return;
        }

        if (empty($email)) {
            $this->error('L\'adresse email est obligatoire !');
            return;
        }

        if (!empty($email)) {
            $stmtCheckEmail = $this->model->getCon()->prepare("SELECT id_user FROM users WHERE email_user = ?");
            $stmtCheckEmail->execute([$email]);
            if ($stmtCheckEmail->fetch()) {
                $this->error('Cette adresse email est déjà attribuée à un compte !');
                return;
            }
        }

        if (!empty($telephone)) {
            $stmtCheckTel = $this->model->getCon()->prepare("SELECT id_user FROM users WHERE telephone_user = ?");
            $stmtCheckTel->execute([$telephone]);
            if ($stmtCheckTel->fetch()) {
                $this->error('Ce numéro de téléphone est déjà attribué à un compte !');
                return;
            }
        }

        $code_user = $this->validator->generateCode('users', 'code_user', 'USR-', 8);
        $maxId = (int)($this->model->getCon()->query("SELECT MAX(id_user) FROM users")->fetchColumn() ?: 0);
        $id_user = $maxId + 1;
        $password = password_hash($rawPassword, PASSWORD_DEFAULT);
        $etabCode = '5454544456';
        $activationToken = bin2hex(random_bytes(32));

        $data = [
            'id_user' => $id_user,
            'code_user' => $code_user,
            'nom_user' => $nom,
            'prenom_user' => $prenom,
            'telephone_user' => $telephone ?: null,
            'email_user' => $email ?: null,
            'sexe_user' => $_POST['sexe_user'] ?? 'M',
            'password_user' => $password,
            'token_user' => $activationToken,
            'fonction_code' => $fonctionCode,
            'zone_user' => !empty($_POST['zone_user']) ? trim($_POST['zone_user']) : null,
            'etablissement_code' => $etabCode,
            'statut_user' => 'inactif',
            'created_at_user' => date('Y-m-d H:i:s')
        ];

        if ($this->model->create($data)) {
            $rolePerms = $_POST['role_perms'] ?? [];
            $rolesData = [];
            foreach ($postedRoles as $r) {
                if (is_string($r) && !empty($r)) {
                    $rolesData[$r] = [
                        'create' => isset($rolePerms[$r]['create']) ? 1 : (isset($_POST['create_permission']) ? 1 : 1),
                        'edit'   => isset($rolePerms[$r]['edit']) ? 1 : (isset($_POST['edit_permission']) ? 1 : 1),
                        'show'   => isset($rolePerms[$r]['show']) ? 1 : (isset($_POST['show_permission']) ? 1 : 1),
                        'delete' => isset($rolePerms[$r]['delete']) ? 1 : (isset($_POST['delete_permission']) ? 1 : 0),
                    ];
                }
            }

            $this->model->syncUserRoles($code_user, $rolesData);

            // Construction de l'URL d'activation unique
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $activationUrl = $protocol . '://' . $host . RACINE . 'user/activer?token=' . $activationToken;

            // Récupération du libellé de fonction
            $libelleFonction = 'Collaborateur';
            if (!empty($fonctionCode)) {
                try {
                    $stmtF = $this->model->getCon()->prepare("SELECT libelle_fonction FROM fonctions WHERE code_fonction = ?");
                    $stmtF->execute([$fonctionCode]);
                    $libelleFonction = $stmtF->fetchColumn() ?: 'Collaborateur';
                } catch (Exception $e) {}
            }

            // Récupération de la zone
            $libelleZone = 'Zone Globale';
            if (!empty($_POST['zone_code'])) {
                try {
                    $stmtZ = $this->model->getCon()->prepare("SELECT libelle_zone FROM zones WHERE code_zone = ?");
                    $stmtZ->execute([$_POST['zone_code']]);
                    $libelleZone = $stmtZ->fetchColumn() ?: $_POST['zone_code'];
                } catch (Exception $e) {}
            }

            // Envoi de l'email d'activation avec coordonnées
            if (!empty($email)) {
                MailerService::sendTemplate(
                    $email,
                    "GEICG Olive Service - Activation de votre compte & Coordonnées d'accès",
                    "welcome_credentials",
                    [
                        'userNom' => trim($nom . ' ' . $prenom),
                        'userFonction' => $libelleFonction,
                        'userZone' => $libelleZone,
                        'userEmail' => $email,
                        'userPassword' => $rawPassword,
                        'loginUrl' => $activationUrl
                    ]
                );
            }

            $idDisplay = $email ?: ($telephone ?: $nom);
            $this->success("Utilisateur créé avec succès en statut inactif ! Un e-mail d'activation avec ses accès et son mot de passe temporaire a été envoyé à <strong>{$idDisplay}</strong>.", ['password' => $rawPassword]);
        } else {
            $this->error('Erreur lors de la création de l\'utilisateur.');
        }
    }

    public function edit()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = (int)$this->post('id_user');
        if (!$id) { $this->error('Identifiant invalide'); return; }

        $user = $this->model->getById($id);
        if (!$user) { $this->error('Utilisateur introuvable'); return; }

        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $telephone = Validator::cleanPhone($_POST['telephone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        // Multi-rôles : supporte tableau roles[] ou role_code simple
        $postedRoles = $_POST['roles'] ?? ($_POST['role_code'] ?? []);
        if (!is_array($postedRoles)) {
            $postedRoles = !empty($postedRoles) ? [$postedRoles] : [];
        }
        $postedRoles = array_values(array_unique(array_filter(array_map('trim', $postedRoles))));

        $fonctionCode = $_POST['fonction_code'] ?? null;
        $statut = ($_POST['actif'] ?? '') === '0' || ($_POST['actif'] ?? '') === 'inactif' ? 'inactif' : 'actif';

        if (empty($nom)) {
            $this->error('Le nom est obligatoire !');
            return;
        }

        if (empty($email)) {
            $this->error('L\'adresse email est obligatoire !');
            return;
        }

        if (!empty($email)) {
            $stmtCheck = $this->model->getCon()->prepare("SELECT id_user FROM users WHERE email_user = ? AND id_user != ?");
            $stmtCheck->execute([$email, $id]);
            if ($stmtCheck->fetch()) {
                $this->error('Cette adresse email est déjà utilisée par un autre compte.');
                return;
            }
        }

        if (!empty($telephone)) {
            $stmtCheck = $this->model->getCon()->prepare("SELECT id_user FROM users WHERE telephone_user = ? AND id_user != ?");
            $stmtCheck->execute([$telephone, $id]);
            if ($stmtCheck->fetch()) {
                $this->error('Ce numéro de téléphone est déjà utilisé par un autre compte.');
                return;
            }
        }

        $data = [
            'id_user' => $id,
            'nom_user' => $nom,
            'prenom_user' => $prenom,
            'telephone_user' => $telephone ?: null,
            'email_user' => $email ?: null,
            'sexe_user' => $_POST['sexe_user'] ?? 'M',
            'fonction_code' => $fonctionCode,
            'zone_user' => !empty($_POST['zone_user']) ? trim($_POST['zone_user']) : null,
            'statut_user' => $statut,
            'updated_at_user' => date('Y-m-d H:i:s')
        ];

        if (!empty($_POST['password'])) {
            $data['password_user'] = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
        }

        if ($this->model->update($data, $id)) {
            if (!empty($postedRoles)) {
                $rolePerms = $_POST['role_perms'] ?? [];
                $rolesData = [];
                foreach ($postedRoles as $r) {
                    if (is_string($r) && !empty($r)) {
                        $rolesData[$r] = [
                            'create' => isset($rolePerms[$r]['create']) ? 1 : 0,
                            'edit'   => isset($rolePerms[$r]['edit']) ? 1 : 0,
                            'show'   => isset($rolePerms[$r]['show']) ? 1 : 0,
                            'delete' => isset($rolePerms[$r]['delete']) ? 1 : 0,
                        ];
                    }
                }

                $this->model->syncUserRoles($user['code_user'], $rolesData);
            }
            $this->success('Utilisateur et permissions par rôle mis à jour avec succès !');
        } else {
            $this->error('Erreur lors de la modification de l\'utilisateur.');
        }
    }

    public function changer()
    {
        $this->requirePost(false);
        $this->requireAuth();
        $id = $this->post('id');

        $userRoles = $_SESSION[USERS_AUTH]['roles'] ?? [];
        if (empty($userRoles)) {
            $singleRole = $_SESSION[USERS_AUTH]['role_code'] ?? ($_SESSION['role_code'] ?? '');
            $userRoles = !empty($singleRole) ? [$singleRole] : [];
        }
        $isSuperAdmin = in_array('ROLE_SUPERADMIN', $userRoles, true);

        $targetUser = $this->model->getById($id);
        if ($targetUser) {
            if (!empty($targetUser['token_user']) && !$isSuperAdmin) {
                $this->error("Action bloquée : Ce compte est en attente d'activation par e-mail via jeton. Seul un Super Admin possède la permission de forcer la modification du statut.");
                return;
            }

            if ($this->model->toggleStatus($id)) {
                $this->success('Statut modifié avec succès!', ['id' => $id, 'reload' => true]);
            } else {
                $this->error('Erreur lors du changement de statut.');
            }
        } else {
            $this->error('Utilisateur introuvable!');
        }
    }

    public function details($details)
    {
        $this->requireAuth();
        try {
            $userId = $this->validator->decrypter($details);
            $userProfile = $this->model->getById($userId);
            if (!$userProfile) {
                header('Location: ' . RACINE . 'user/list');
                exit();
            }
            $userRoles = $this->model->getUserRoles($userProfile['code_user']);
            $primaryRole = !empty($userRoles) ? $userRoles[0] : null;
            $encryptedId = $this->validator->crypter($userId);
            
            $zoneLabel = null;
            if (!empty($userProfile['zone_user'])) {
                $stmtZ = $this->model->getCon()->prepare("SELECT libelle_zone FROM zones WHERE code_zone = ?");
                $stmtZ->execute([$userProfile['zone_user']]);
                $zoneLabel = $stmtZ->fetchColumn();
            }
            $userProfile['libelle_zone'] = $zoneLabel ?: 'Globale';
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'user/list');
            exit();
        }

        $this->loadView('../views/users/details.php', [
            'user' => $userProfile,
            'role' => $primaryRole,
            'userRoles' => $userRoles,
            'encryptedId' => $encryptedId
        ]);
    }

    public function formulaire()
    {
        $this->requireAuth();
        $roles = (new ModelRole())->getAll();
        $fonctions = (new ModelFonction())->getAll();
        $zones = $this->model->getCon()->query("SELECT * FROM zones WHERE statut_zone = 'actif' ORDER BY libelle_zone ASC")->fetchAll(PDO::FETCH_ASSOC);
        $this->loadView('../views/users/edit.php', [
            'user' => [],
            'role' => [],
            'userRoles' => [],
            'userRoleCodes' => [],
            'roles' => $roles,
            'fonctions' => $fonctions,
            'zones' => $zones
        ]);
    }

    public function edition($details)
    {
        $this->requireAuth();
        try {
            $decryptedId = $this->validator->decrypter($details);
            $userProfile = $this->model->getById($decryptedId);

            if (!$userProfile) {
                header('Location: ' . RACINE . 'user/list');
                exit();
            }
            $userRoles = $this->model->getUserRoles($userProfile['code_user']);
            $userRoleCodes = $this->model->getUserRoleCodes($userProfile['code_user']);
            $primaryRole = !empty($userRoles) ? $userRoles[0] : null;
            $roles = (new ModelRole())->getAll();
            $fonctions = (new ModelFonction())->getAll();
            $zones = $this->model->getCon()->query("SELECT * FROM zones WHERE statut_zone = 'actif' ORDER BY libelle_zone ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            header('Location: ' . RACINE . 'user/list');
            exit();
        }

        $this->loadView('../views/users/edit.php', [
            'user' => $userProfile,
            'role' => $primaryRole,
            'userRoles' => $userRoles,
            'userRoleCodes' => $userRoleCodes,
            'roles' => $roles,
            'fonctions' => $fonctions,
            'zones' => $zones
        ]);
    }

    public function profil()
    {
        $this->requireAuth();
        $auth = $_SESSION[USERS_AUTH] ?? null;
        $userId = $auth['id_user'] ?? 0;
        $editId = $this->validator->crypter($userId);
        $userProfile = $this->model->getById((int)$userId);
        $userRoles = $this->model->getUserRoles($userProfile['code_user'] ?? '');
        $primaryRole = !empty($userRoles) ? $userRoles[0] : null;
        $this->loadView('../views/users/profil.php', [
            'user' => $userProfile,
            'role' => $primaryRole,
            'userRoles' => $userRoles,
            'editId' => $editId
        ]);
    }

    /**
     * Action d'activation de compte via le jeton unique reçu par e-mail
     */
    public function activer()
    {
        $token = trim($_GET['token'] ?? '');

        if (empty($token)) {
            $_SESSION['flash_error'] = "Le jeton d'activation est manquant ou invalide.";
            header('Location: ' . RACINE . 'user/connexion');
            exit();
        }

        // Recherche de l'utilisateur par le token d'activation
        $stmt = $this->model->getCon()->prepare("SELECT id_user, nom_user, prenom_user, email_user, statut_user FROM users WHERE token_user = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['flash_error'] = "Ce lien d'activation est invalide ou a déjà été vérifié et utilisé.";
            header('Location: ' . RACINE . 'user/connexion');
            exit();
        }

        // 1. Activer le compte (statut_user = 'actif')
        // 2. SUPPRIMER le token d'activation (token_user = NULL) pour garantir un lien à usage unique !
        $stmtUpdate = $this->model->getCon()->prepare("UPDATE users SET statut_user = 'actif', token_user = NULL, updated_at_user = ? WHERE id_user = ?");
        $stmtUpdate->execute([date('Y-m-d H:i:s'), $user['id_user']]);

        $userName = htmlspecialchars(trim($user['nom_user'] . ' ' . ($user['prenom_user'] ?? '')));
        $_SESSION['flash_success'] = "Félicitations {$userName} ! Votre compte a été activé avec succès. Vous pouvez maintenant vous connecter avec votre mot de passe temporaire et le personnaliser dans votre profil.";
        header('Location: ' . RACINE . 'user/connexion');
        exit();
    }

    /**
     * Traitement de la demande de réinitialisation de mot de passe (Email)
     */
    public function forgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('../views/users/forgot_password.php', [], 'guest');
            return;
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = "Veuillez saisir une adresse e-mail valide.";
            $this->render('../views/users/forgot_password.php', [], 'guest');
            return;
        }

        // Recherche de l'utilisateur par e-mail
        $stmt = $this->model->getCon()->prepare("SELECT id_user, nom_user, prenom_user, email_user FROM users WHERE email_user = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['flash_error'] = "Aucun compte n'est associé à cette adresse e-mail.";
            $this->render('../views/users/forgot_password.php', [], 'guest');
            return;
        }

        // Génération du token de réinitialisation (64 caractères) + Expiration à 30 minutes (1800 sec)
        $resetToken = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 1800);

        // Mise à jour en BDD
        $stmtUpdate = $this->model->getCon()->prepare("UPDATE users SET token_user = ?, reset_token_expires_at = ?, updated_at_user = ? WHERE id_user = ?");
        $stmtUpdate->execute([$resetToken, $expiresAt, date('Y-m-d H:i:s'), $user['id_user']]);

        // URL de réinitialisation
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $resetLink = $protocol . '://' . $host . RACINE . 'user/reset_password?token=' . $resetToken;

        // Envoi de l'e-mail via MailerService
        MailerService::sendTemplate(
            $email,
            "GEICG Olive Service - Demande de réinitialisation de votre mot de passe",
            "reset_password",
            [
                'userNom' => trim($user['nom_user'] . ' ' . ($user['prenom_user'] ?? '')),
                'resetLink' => $resetLink,
                'expirationTime' => '30 minutes'
            ]
        );

        $_SESSION['flash_success'] = "Un lien de réinitialisation valide pendant 30 minutes vient de vous être envoyé à votre adresse e-mail.";
        $this->render('../views/users/forgot_password.php', [], 'guest');
    }

    /**
     * Traitement du lien de réinitialisation et enregistrement du nouveau mot de passe
     */
    public function resetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $token = trim($_GET['token'] ?? '');

            if (empty($token)) {
                $_SESSION['flash_error'] = "Le lien de réinitialisation est manquant ou invalide.";
                header('Location: ' . RACINE . 'user/connexion');
                exit();
            }

            // Vérification du token et de l'expiration (< 30 min)
            $stmt = $this->model->getCon()->prepare("SELECT id_user FROM users WHERE token_user = ? AND reset_token_expires_at >= ?");
            $stmt->execute([$token, date('Y-m-d H:i:s')]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $_SESSION['flash_error'] = "Ce lien de réinitialisation est invalide ou a expiré (durée de validité : 30 minutes). Veuillez faire une nouvelle demande.";
                header('Location: ' . RACINE . 'user/forgot_password');
                exit();
            }

            $this->render('../views/users/reset_password.php', ['token' => $token], 'guest');
            return;
        }

        // Soumission POST du nouveau mot de passe
        $token = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token)) {
            $_SESSION['flash_error'] = "Jeton de réinitialisation manquant.";
            header('Location: ' . RACINE . 'user/connexion');
            exit();
        }

        if (strlen($password) < 6) {
            $_SESSION['flash_error'] = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
            $this->render('../views/users/reset_password.php', ['token' => $token], 'guest');
            return;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['flash_error'] = "Les deux mots de passe ne correspondent pas.";
            $this->render('../views/users/reset_password.php', ['token' => $token], 'guest');
            return;
        }

        // Vérification de la validité du token
        $stmt = $this->model->getCon()->prepare("SELECT id_user FROM users WHERE token_user = ? AND reset_token_expires_at >= ?");
        $stmt->execute([$token, date('Y-m-d H:i:s')]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['flash_error'] = "Ce lien de réinitialisation est invalide ou a expiré.";
            header('Location: ' . RACINE . 'user/forgot_password');
            exit();
        }

        // Mise à jour du mot de passe + suppression du token (usage unique)
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $stmtUpdate = $this->model->getCon()->prepare("UPDATE users SET password_user = ?, token_user = NULL, reset_token_expires_at = NULL, updated_at_user = ? WHERE id_user = ?");
        $stmtUpdate->execute([$newHash, date('Y-m-d H:i:s'), $user['id_user']]);

        $_SESSION['flash_success'] = "Votre mot de passe a été réinitialisé avec succès ! Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.";
        header('Location: ' . RACINE . 'user/connexion');
        exit();
    }

    public function connexion()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('../views/users/connexion.php', [], 'guest');
            return;
        }

        $this->requirePost(false);
        $notEmpty = Validator::validateRequiredFields($_POST);

        if ($notEmpty === true) {
            $loginRaw = trim($this->post('login'));
            $cleanPhone = Validator::cleanPhone($loginRaw);
            $password = $this->post('password');

            // 1. Recherche dans la table USERS par email ou téléphone (brut ou nettoyé)
            $user = $this->validator->getByElement('users', 'email_user', $loginRaw);
            if (!$user && !empty($cleanPhone)) {
                $user = $this->validator->getByElement('users', 'telephone_user', $cleanPhone);
            }
            if (!$user) {
                $user = $this->validator->getByElement('users', 'telephone_user', $loginRaw);
            }

            $passwordMatched = false;
            if (isset($user) && !empty($user)) {
                $hashInDb = $user['password_user'] ?? '';
                if (password_verify($password, $hashInDb)) {
                    $passwordMatched = true;
                } elseif ($hashInDb === $password) {
                    $passwordMatched = true;
                    // Auto-mise à jour du mot de passe avec hash sécurisé
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $this->model->getCon()->prepare("UPDATE users SET password_user = ? WHERE id_user = ?")->execute([$newHash, $user['id_user']]);
                }
            }

            if ($user && $passwordMatched) {
                if ($user['statut_user'] === 'actif') {
                    $userRoles = $this->model->getUserRoles($user['code_user']);
                    $roleCodes = !empty($userRoles) ? array_column($userRoles, 'role_code') : ['ROLE_USER'];
                    $primaryRole = !empty($userRoles) ? $userRoles[0] : null;
                    $roleCode = !empty($roleCodes) ? $roleCodes[0] : 'ROLE_USER';

                    // Permissions cumulées
                    $createPerm = 0; $editPerm = 0; $showPerm = 0; $deletePerm = 0;
                    if (!empty($userRoles)) {
                        foreach ($userRoles as $ur) {
                            if (!empty($ur['create_permission'])) $createPerm = 1;
                            if (!empty($ur['edit_permission']))   $editPerm = 1;
                            if (!empty($ur['show_permission']))   $showPerm = 1;
                            if (!empty($ur['delete_permission'])) $deletePerm = 1;
                        }
                    } else {
                        $createPerm = 1; $editPerm = 1; $showPerm = 1; $deletePerm = 0;
                    }

                    // Récupérer toutes les permissions métier de l'ensemble des rôles
                    $inClause = implode(',', array_fill(0, count($roleCodes), '?'));
                    $stmtPerms = $this->model->getCon()->prepare("
                        SELECT DISTINCT rp.permission_code 
                        FROM role_permissions rp
                        JOIN permissions p ON rp.permission_code = p.code_permission
                        WHERE rp.role_code IN ($inClause) AND p.statut_permission = 'actif'
                    ");
                    $stmtPerms->execute($roleCodes);
                    $allPermissions = $stmtPerms->fetchAll(PDO::FETCH_COLUMN) ?: [];

                    $sessionData = [
                        'id_user' => $user['id_user'],
                        'code_user' => $user['code_user'],
                        'nom' => $user['nom_user'],
                        'prenom' => $user['prenom_user'] ?? '',
                        'email' => $user['email_user'] ?? '',
                        'tel' => $user['telephone_user'] ?? '',
                        'role_code' => $roleCode,
                        'roles' => $roleCodes,
                        'roles_details' => $userRoles,
                        'code_commercial' => $user['code_user'],
                        'permissions' => [
                            'create' => $createPerm,
                            'edit' => $editPerm,
                            'show' => $showPerm,
                            'delete' => $deletePerm
                        ]
                    ];

                    Validator::saveSesion(USERS_AUTH, $sessionData);
                    $_SESSION['permissions'] = $allPermissions;
                    $_SESSION['roles'] = $roleCodes;
                    $_SESSION['etablissement_active_code'] = $user['etablissement_code'] ?? '5454544456';
                    $_SESSION['zone_active_code'] = $user['zone_code'] ?? null;

                    $etabCode = $_SESSION['etablissement_active_code'];
                    $stmtEtab = $this->model->getCon()->prepare("SELECT libelle_etablissement FROM etablissements WHERE code_etablissement = ? LIMIT 1");
                    $stmtEtab->execute([$etabCode]);
                    $_SESSION['etablissement_active_libelle'] = $stmtEtab->fetchColumn() ?: 'Établissement';

                    $zoneCode = $_SESSION['zone_active_code'];
                    if ($zoneCode) {
                        $stmtZone = $this->model->getCon()->prepare("SELECT libelle_zone FROM zones WHERE code_zone = ? LIMIT 1");
                        $stmtZone->execute([$zoneCode]);
                        $_SESSION['zone_active_libelle'] = $stmtZone->fetchColumn() ?: null;
                    } else {
                        $_SESSION['zone_active_libelle'] = null;
                    }

                    $anneeCode = $_SESSION['annee_active_code'] ?? '';
                    if ($anneeCode) {
                        $stmtAnnee = $this->model->getCon()->prepare("SELECT libelle_annee FROM annees WHERE code_annee = ? LIMIT 1");
                        $stmtAnnee->execute([$anneeCode]);
                        $_SESSION['annee_active_libelle'] = $stmtAnnee->fetchColumn() ?: 'Année';
                    }

                    $this->success('Connexion réussie ! Bienvenue sur Olive Service.');
                    return;
                } else {
                    $this->error('Ce compte utilisateur est inactif ou suspendu. Veuillez contacter l\'administrateur.');
                    return;
                }
            }

            // 2. Si pas trouvé dans USERS, recherche directe dans la table ENSEIGNANTS (si elle existe)
            try {
                $stmtEnsLogin = $this->model->getCon()->prepare("
                    SELECT * FROM enseignants 
                    WHERE (email_enseignant = ? OR telephone_enseignant = ?)
                    LIMIT 1
                ");
                $stmtEnsLogin->execute([$loginRaw, $loginRaw]);
                $ens = $stmtEnsLogin->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $ens = null;
            }

            if ($ens && !empty($ens['password_enseignant']) && (password_verify($password, $ens['password_enseignant']) || $ens['password_enseignant'] === $password)) {
                if ($ens['statut_enseignant'] === 'actif') {
                    $sessionData = [
                        'id_user' => $ens['id_enseignant'],
                        'code_user' => $ens['code_enseignant'],
                        'nom' => $ens['nom_enseignant'],
                        'prenom' => $ens['prenom_enseignant'] ?? '',
                        'email' => $ens['email_enseignant'] ?? '',
                        'tel' => $ens['telephone_enseignant'] ?? '',
                        'role_code' => 'ROLE_ENSEIGNANT',
                        'code_commercial' => $ens['code_enseignant'],
                        'permissions' => [
                            'create' => 1,
                            'edit' => 1,
                            'show' => 1,
                            'delete' => 0
                        ]
                    ];

                    Validator::saveSesion(USERS_AUTH, $sessionData);
                    $this->success('Connexion réussie ! Bienvenue sur Olive Service.');
                    return;
                } else {
                    $this->error('Ce compte enseignant est inactif ou suspendu. Veuillez contacter l\'administration.');
                    return;
                }
            }

            $this->error('Identifiants incorrects. Veuillez vérifier votre adresse email / téléphone et mot de passe.');
        } else {
            $this->error('Veuillez renseigner tous les champs !');
        }
    }

    public function decon()
    {
        $this->unsetSession();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            @session_destroy();
        }
        $_SESSION = [];

        header('Location: ' . RACINE . 'user/connexion');
        exit();
    }

    public function logout()
    {
        $this->decon();
    }

    public function editPassword()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->loadView('../views/users/editPassword.php', [
                'user' => $_SESSION[USERS_AUTH] ?? []
            ]);
            return;
        }

        $this->requirePost(false);

        $oldPassword = $this->post('old_password') ?: $this->post('password');
        $newPassword = $this->post('new_password') ?: $this->post('newPassword');
        $confirmPassword = $this->post('confirm_password') ?: $this->post('confirmPassword');

        if (empty($oldPassword) || empty($newPassword)) {
            $this->error('Veuillez renseigner l\'ancien et le nouveau mot de passe !');
            return;
        }

        if (!empty($confirmPassword) && $newPassword !== $confirmPassword) {
            $this->error('La confirmation du nouveau mot de passe ne correspond pas !');
            return;
        }

        if (strlen($newPassword) < 4) {
            $this->error('Le nouveau mot de passe doit contenir au moins 4 caractères !');
            return;
        }

        $userId = $_SESSION[USERS_AUTH]['id_user'] ?? 0;
        $user = $this->model->getById((int)$userId);

        if (!$user) {
            $this->error('Utilisateur introuvable !');
            return;
        }

        if (!password_verify($oldPassword, $user['password_user'] ?? '')) {
            $this->error('Votre mot de passe actuel est incorrect !');
            return;
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        if ($this->model->updatePassword($hash, (int)$userId)) {
            $this->success('Votre mot de passe a été modifié avec succès !');
        } else {
            $this->error('Erreur lors de la mise à jour du mot de passe.');
        }
    }
}