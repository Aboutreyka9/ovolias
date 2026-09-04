<?php

abstract class BaseController
{
    protected $validator;
    protected $model;

    public function __construct()
    {
        $this->validator = new Validator();
        $this->model = $this->resolveModel();
    }

    abstract protected function resolveModel();

    protected function requirePost(bool $checkCsrf = true): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($this->isAjax()) {
                $this->json(['status' => 0, 'message' => 'Méthode non autorisée (POST requis)'], 405);
            } else {
                $this->renderForbidden('Cette page accepte uniquement les requêtes POST.');
            }
        }

        if ($checkCsrf && !Validator::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            if ($this->isAjax()) {
                $this->json(['status' => 0, 'message' => 'Session ou jeton de sécurité expiré. Veuillez réactualiser la page.'], 419);
            } else {
                $this->renderError('Jeton de sécurité CSRF expiré ou invalide. Veuillez rafraîchir la page et réessayer.', 419);
            }
        }
    }

    protected function requireAuth(): void
    {
        if (!isset($_SESSION[USERS_AUTH]['id_user']) && !isset($_SESSION[USERS_AUTH]['code_user'])) {
            if ($this->isAjax()) {
                $this->json(['status' => 0, 'message' => 'Authentification requise pour cette opération'], 401);
            } else {
                header('Location: ' . RACINE . 'user/connexion');
                exit();
            }
        }
    }

    /**
     * Affiche la page complète d'erreur 403 (Accès Refusé / Privilèges Insuffisants)
     */
    public function renderForbidden(string $message = "Vous ne disposez pas des autorisations nécessaires pour accéder à cette page ou exécuter cette action.", string $permissionCode = ''): void
    {
        if ($this->isAjax()) {
            $this->json(['status' => 0, 'message' => $message, 'required_permission' => $permissionCode], 403);
        } else {
            if (!headers_sent()) {
                http_response_code(403);
            }
            require __DIR__ . '/../views/errors/403.php';
            exit();
        }
    }

    /**
     * Affiche la page complète d'erreur 404 (Ressource Introuvable)
     */
    public function renderNotFound(string $message = "La page, l'enregistrement ou la ressource demandée n'existe pas."): void
    {
        if ($this->isAjax()) {
            $this->json(['status' => 0, 'message' => $message], 404);
        } else {
            if (!headers_sent()) {
                http_response_code(404);
            }
            require __DIR__ . '/../views/errors/404.php';
            exit();
        }
    }

    /**
     * Affiche la page complète d'erreur 500 (Incident Système)
     */
    public function renderError(string $message = "Une anomalie est survenue lors de l'exécution de la requête.", int $code = 500): void
    {
        if ($this->isAjax()) {
            $this->json(['status' => 0, 'message' => $message], $code);
        } else {
            if (!headers_sent()) {
                http_response_code($code);
            }
            require __DIR__ . '/../views/errors/500.php';
            exit();
        }
    }

    protected function unsetSession(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            @session_destroy();
        }
    }

    protected function csrfField(): string
    {
        return Validator::csrfField();
    }

    protected function json(array $data, int $code = 200): void
    {
        if (!headers_sent()) {
            http_response_code($code);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($data);
        exit;
    }

    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function success(string $message, array $extra = []): void
    {
        if ($this->isAjax()) {
            $this->json(array_merge(['status' => 1, 'message' => $message], $extra));
        } else {
            $_SESSION['flash_success'] = $message;
            $referer = $_SERVER['HTTP_REFERER'] ?? RACINE;
            header('Location: ' . $referer);
            exit;
        }
    }

    protected function error(string $message, int $code = 200): void
    {
        if ($this->isAjax()) {
            $this->json(['status' => 0, 'message' => $message], $code);
        } else {
            $_SESSION['flash_error'] = $message;
            $referer = $_SERVER['HTTP_REFERER'] ?? RACINE;
            header('Location: ' . $referer);
            exit;
        }
    }

    protected function checkUnique(string $table, string $field, $value, string $label, ?string $idField = null, $idVal = null): bool
    {
        $val = trim((string)$value);
        if ($val === '') return true;

        $exists = false;
        if ($idField && !empty($idVal)) {
            $exists = $this->validator->_verif($table, $field, $val, $idField, $idVal);
        } else {
            $exists = $this->validator->verif($table, $field, $val);
        }

        if ($exists) {
            $this->error("Ce $label ($val) est déjà utilisé dans le système !");
            return false;
        }
        return true;
    }

    protected function checkUniquePair(string $table, array $conditions, string $label, ?string $idField = null, $idVal = null): bool
    {
        if (empty($conditions)) return true;

        $pdo = ($this->model && method_exists($this->model, 'getCon')) ? $this->model->getCon() : (new Database())->getCon();
        $where = [];
        $params = [];

        foreach ($conditions as $col => $val) {
            $where[] = "`$col` = ?";
            $params[] = trim((string)$val);
        }

        $sql = "SELECT COUNT(*) FROM `$table` WHERE " . implode(' AND ', $where);
        if ($idField && !empty($idVal)) {
            $sql .= " AND `$idField` != ?";
            $params[] = $idVal;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $count = (int)$stmt->fetchColumn();

        if ($count > 0) {
            $this->error("Ce $label existe déjà pour cette association !");
            return false;
        }
        return true;
    }

    protected function generateCode(string $table, string $field, string $prefix, int $len): string
    {
        return $this->validator->generateCode($table, $field, $prefix, $len);
    }

    /**
     * Rendu d'une vue avec gestion dual-layout :
     * - 'main' (par défaut) : Layout application d'administration (avec Header, Sidebar, Nav, Footer)
     * - 'guest' : Layout visiteur/auth (sans Sidebar ni Nav, uniquement Header et Footer)
     * - false : Aucun layout (rendu brut)
     * 
     * @param string $viewPath Chemin relatif ou absolu de la vue
     * @param array $data Données transmises à la vue
     * @param string|bool $layout 'main', 'guest', ou false
     */
    protected function render(string $viewPath, array $data = [], $layout = 'main'): void
    {
        $data['isSuperAdmin'] = $this->isSuperAdmin();
        $data['currentUserName'] = $data['currentUserName'] ?? ($_SESSION[USERS_AUTH]['nom'] ?? ($_SESSION[USERS_AUTH]['nom_user'] ?? 'Utilisateur'));
        $data['currentUserEmail'] = $data['currentUserEmail'] ?? ($_SESSION[USERS_AUTH]['email'] ?? ($_SESSION[USERS_AUTH]['email_user'] ?? ''));
        $data['currentUserRole'] = $_SESSION[USERS_AUTH]['role_code'] ?? 'ROLE_USER';

        $file = $viewPath;
        if (!file_exists($file)) {
            if (strpos($viewPath, 'views/') === false && strpos($viewPath, '../views/') === false) {
                $file = __DIR__ . '/../views/' . ltrim($viewPath, '/\\');
            } else {
                $file = __DIR__ . '/../' . ltrim(str_replace('../', '', $viewPath), '/\\');
            }
            if (substr($file, -4) !== '.php') {
                $file .= '.php';
            }
        }

        if (!file_exists($file)) {
            $this->renderNotFound("La vue [$viewPath] est introuvable.");
            return;
        }

        // Vérifier si la vue source inclut déjà manuellement header.php
        $rawSource = file_get_contents($file);
        $hasManualHeader = (strpos($rawSource, 'header.php') !== false);

        extract($data);

        // Si $layout === false ou si la vue gère déjà son propre header
        if ($layout === false || $hasManualHeader) {
            require $file;
            return;
        }

        // Layout 1: 'guest' (Connexion, etc. - SANS Sidebar ni Nav)
        if ($layout === 'guest' || $layout === 'auth') {
            require_once __DIR__ . '/../public/inc/header.php';
            require $file;
            require_once __DIR__ . '/../public/inc/footer-link.php';
            return;
        }

        // Layout 2: 'main' (Pages d'administration - AVEC Sidebar et Nav)
        require_once __DIR__ . '/../public/inc/header.php';
        echo '<div class="app-layout">';
        require_once __DIR__ . '/../public/inc/sidbar.php';
        echo '<main class="main-content">';
        require_once __DIR__ . '/../public/inc/nav.php';
        echo '<div class="content-wrapper" style="padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">';
        require $file;
        echo '</div>';
        echo '</main>';
        echo '</div>';
        require_once __DIR__ . '/../public/inc/footer-link.php';
    }

    protected function loadView(string $path, array $data = []): void
    {
        $this->render($path, $data, 'main');
    }

    protected function get(string $key, $default = '')
    {
        return $_GET[$key] ?? $default;
    }

    protected function post(string $key, $default = '')
    {
        return $_POST[$key] ?? $default;
    }

    protected function validateRequired(array $fields): array
    {
        $result = [];
        foreach ($fields as $field => $label) {
            $value = trim($this->post($field));
            if ($value === '') {
                $result[$field] = "$label est requis";
            }
        }
        return $result;
    }

    /**
     * Nettoie automatiquement les champs téléphoniques pour retirer +225 / 225
     */
    protected function cleanPhoneFields(array &$data): void
    {
        $phoneKeys = [
            'telephone', 'telephone_user', 'telephone_etudiant', 'telephone_enseignant',
            'telephone_pere', 'telephone_mere', 'telephone_tuteur', 'telephone_etablissement',
            'telephone_etablissement2', 'telephone_client', 'telephone_livreur', 'contact', 'contact_urgence'
        ];

        foreach ($phoneKeys as $key) {
            if (isset($data[$key]) && is_string($data[$key])) {
                $data[$key] = Validator::cleanPhone($data[$key]);
            }
        }
    }

    protected function redirect(string $url): void
    {
        if (!headers_sent()) {
            header('Location: ' . $url);
        } else {
            echo "<script>window.location.href=" . json_encode($url) . ";</script>";
        }
        exit;
    }

    protected function getCurrentUserCode(): ?string
    {
        return $_SESSION[USERS_AUTH]['code_user'] ?? null;
    }

    protected function contextAnneeCode(): string
    {
        return Context::annee();
    }

    protected function contextEtablissementCode(): string
    {
        return Context::etablissement();
    }

    protected function contextZoneCode(): ?string
    {
        return Context::zone();
    }

    protected function injectContext(array &$data, array $fields = ['annee_code', 'etablissement_code', 'zone_code', 'user_code']): void
    {
        Context::applyTo($data, $fields);
    }

    protected function getCurrentUserRoles(): array
    {
        $roles = $_SESSION[USERS_AUTH]['roles'] ?? [];
        if (empty($roles)) {
            $roleCode = $_SESSION[USERS_AUTH]['role_code'] ?? ($_SESSION['role_code'] ?? '');
            if ($roleCode !== '') {
                $roles = [$roleCode];
            }
        }
        if (is_string($roles)) {
            $roles = [$roles];
        }
        return array_values(array_unique(array_filter($roles)));
    }

    protected function hasRole(string $roleCode): bool
    {
        return in_array($roleCode, $this->getCurrentUserRoles(), true);
    }

    protected function hasAnyRole(array $roleCodes): bool
    {
        return !empty(array_intersect($this->getCurrentUserRoles(), $roleCodes));
    }

    protected function isSuperAdmin(): bool
    {
        return $this->hasAnyRole(['ROLE_SUPERADMIN', 'ROLE_DIR_GENERAL']);
    }

    /**
     * Exige que l'utilisateur connecté possède le rôle Super Admin ou Direction Générale
     */
    protected function requireSuperAdmin(string $customMessage = ''): void
    {
        $this->requireAuth();
        if (!$this->isSuperAdmin()) {
            $msg = !empty($customMessage) ? $customMessage : "Cette action est réservée exclusivement à la Direction Générale et à l'Administration Système.";
            $this->renderForbidden($msg, 'ROLE_SUPERADMIN');
        }
    }

    /**
     * Récupère la liste des permissions attribuées au rôle de l'utilisateur connecté
     */
    protected function getUserPermissions(): array
    {
        if ($this->isSuperAdmin()) {
            return ['*'];
        }

        $roles = $this->getCurrentUserRoles();
        if (empty($roles)) {
            return [];
        }

        try {
            $pdo = ($this->model && method_exists($this->model, 'getCon')) ? $this->model->getCon() : (new Database())->getCon();
            $inClause = implode(',', array_fill(0, count($roles), '?'));
            $sql = "
                SELECT DISTINCT rp.permission_code 
                FROM role_permissions rp
                JOIN permissions p ON rp.permission_code = p.code_permission
                WHERE rp.role_code IN ($inClause)
                  AND p.statut_permission = 'actif'
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($roles);
            return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        } catch (Exception $e) {
            error_log("Error fetching user permissions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Vérifie si l'utilisateur possède une permission métier donnée
     */
    protected function hasPermission(string $permissionCode): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $perms = $this->getUserPermissions();
        return in_array('*', $perms, true) || in_array($permissionCode, $perms, true);
    }

    /**
     * Vérifie les droits d'action unitaire CRUD (create, edit, show, delete)
     */
    protected function hasActionPermission(string $action): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        $perms = $_SESSION[USERS_AUTH]['permissions'] ?? [];
        return !empty($perms[$action]);
    }

    /**
     * Bloque la requête avec une page complète 403 si l'utilisateur ne possède pas la permission requise
     */
    protected function requirePermission(string $permissionCode, string $customMessage = ''): void
    {
        $this->requireAuth();

        if (!$this->hasPermission($permissionCode)) {
            $msg = !empty($customMessage) ? $customMessage : "Accès refusé : vous ne possédez pas le privilège [{$permissionCode}] requis pour accéder à cette section.";
            $this->renderForbidden($msg, $permissionCode);
        }
    }

    /**
     * Bloque la requête avec une page complète 403 si l'action CRUD n'est pas autorisée
     */
    protected function requireAction(string $action, string $customMessage = ''): void
    {
        $this->requireAuth();

        if (!$this->hasActionPermission($action)) {
            $actionLabels = [
                'create' => 'la création de nouveaux enregistrements',
                'edit' => 'la modification d\'enregistrements',
                'show' => 'la consultation de ces données',
                'delete' => 'la suppression d\'enregistrements'
            ];
            $lbl = $actionLabels[$action] ?? $action;
            $msg = !empty($customMessage) ? $customMessage : "Votre compte ne vous autorise pas {$lbl}.";
            $this->renderForbidden($msg, strtoupper($action) . '_PRIVILEGE');
        }
    }
}
