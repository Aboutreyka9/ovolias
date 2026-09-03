<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#18385F">
    <title><?= htmlspecialchars(TITLE) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Merriweather:wght@400;700&family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script>
        (function() {
            try {
                var map = { navy: '#18385F', bordeaux: '#5C0808', royal: '#2563EB', emerald: '#047857' };
                var topbar = localStorage.getItem('theme_topbar') || 'light';
                var sidebar = localStorage.getItem('theme_sidebar') || 'light';
                var content = localStorage.getItem('theme_content') || 'light';
                var primary = localStorage.getItem('theme_primary') || 'navy';
                var font = localStorage.getItem('theme_font') || 'inter';
                var fontsize = localStorage.getItem('theme_fontsize') || 'normal';
                var radius = localStorage.getItem('theme_radius') || 'normal';
                
                document.documentElement.setAttribute('data-theme-topbar', topbar);
                document.documentElement.setAttribute('data-theme-sidebar', sidebar);
                document.documentElement.setAttribute('data-theme-content', content);
                document.documentElement.setAttribute('data-theme-primary', primary);
                document.documentElement.setAttribute('data-theme-font', font);
                document.documentElement.setAttribute('data-theme-fontsize', fontsize);
                document.documentElement.setAttribute('data-theme-radius', radius);

                if (map[primary]) {
                    document.documentElement.style.setProperty('--primary-color', map[primary]);
                    document.documentElement.style.setProperty('--primary', map[primary]);
                    document.documentElement.style.setProperty('--btn-primary-bg', map[primary]);
                    document.documentElement.style.setProperty('--secondary-color', map[primary]);
                    document.documentElement.style.setProperty('--sidebar-active-text', map[primary]);
                }
            } catch(e) {}
        })();
    </script>
    <link rel="stylesheet" href="<?= RACINE ?>public/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .select2-container--default .select2-selection--single {
            height: 42px !important;
            border: 1px solid #CBD5E1 !important;
            border-radius: 8px !important;
            padding: 6px 12px !important;
            display: flex !important;
            align-items: center !important;
            background-color: #FFFFFF !important;
            transition: all 0.2s ease !important;
        }
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #1E3A5F !important;
            box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1E293B !important;
            font-size: 14px !important;
            padding-left: 0 !important;
            line-height: 28px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 8px !important;
        }
        .select2-dropdown {
            border: 1px solid #CBD5E1 !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            z-index: 100000 !important;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #E2E8F0 !important;
            border-radius: 6px !important;
            padding: 8px 12px !important;
            font-size: 13px !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #1E3A5F !important;
            color: #FFFFFF !important;
        }
        .select2-container--default .select2-results__option--selected {
            background-color: #EFF6FF !important;
            color: #1E3A5F !important;
            font-weight: 600 !important;
        }
    </style>
    <script>
        window.RACINE = '<?= RACINE ?>';
        window.LINK = '<?= RACINE ?>';
        window.AppConfig = {
            racine: '<?= RACINE ?>',
            link: '<?= RACINE ?>',
            userCode: '<?= Context::user() ?>',
            role: '<?= Context::role() ?>',
            isCommercial: <?= Context::isCommercial() ? 'true' : 'false' ?>,
            isFinance: <?= Context::isFinance() ? 'true' : 'false' ?>,
            isAdmin: <?= Context::isAdmin() ? 'true' : 'false' ?>
        };
    </script>

    <?php
    $currentUserCode = $_SESSION[USERS_AUTH]['code_user'] ?? '';
    $currentRoleCode = $_SESSION[USERS_AUTH]['role_code'] ?? '';
    $currentRoles = [];
    $isSuperAdmin = false;
    $isPressing = false;
    $isLivreur = false;
    $currentPressingCode = null;
    $currentLivreurCode = null;

    if ($currentUserCode !== '') {
        try {
            $userRoles = $_SESSION[USERS_AUTH]['roles'] ?? (!empty($currentRoleCode) ? [$currentRoleCode] : []);
            if (empty($userRoles)) {
                $userRoles = (new ModelUser())->getUserRoleCodes($currentUserCode);
            }
            $currentRoles = $userRoles;
            $isSuperAdmin = in_array('ROLE_SUPERADMIN', $currentRoles, true) || in_array('ROLE_DIR_GENERAL', $currentRoles, true);
        } catch (\Throwable $e) {
            $currentRoles = [];
        }
    }

    $currentUserName  = $_SESSION[USERS_AUTH]['nom_user'] ?? ($_SESSION[USERS_AUTH]['nom'] ?? ($_SESSION[USERS_AUTH]['login_user'] ?? 'Utilisateur'));
    $currentUserEmail = $_SESSION[USERS_AUTH]['email_user'] ?? ($_SESSION[USERS_AUTH]['email'] ?? '');
    $currentUserPhoto = !empty($_SESSION[USERS_AUTH]['photo_user']) ? RACINE . 'public/assets/images/users/' . $_SESSION[USERS_AUTH]['photo_user'] : 'https://ui-avatars.com/api/?name=' . urlencode($currentUserName) . '&background=1E3A5F&color=fff';

    $recentAdminNotifs = [];
    $unreadNotifsCount = 0;
    try {
        $notifModel = new ModelNotification();
        $notifStats = $notifModel->getStats($currentPressingCode, $currentLivreurCode);
        $unreadNotifsCount = $notifStats['non_lues'] ?? 0;
        $recentAdminNotifs = $notifModel->getAllWithClient($currentPressingCode, $currentLivreurCode, 5);
    } catch (Exception $e) {
        $recentAdminNotifs = [];
        $unreadNotifsCount = 0;
    }

    ?>
</head>
<body>
    <input type="hidden" id="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
    <div class="js-toast-container"></div>