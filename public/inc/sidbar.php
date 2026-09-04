<?php
  if (!isset($globalEtablissementLogo)) {
      try {
          $db = (new Database())->getCon();
          $stmt = $db->query("SELECT logo_etablissement, libelle_etablissement FROM etablissements ORDER BY id_etablissement ASC LIMIT 1");
          $etabRow = $stmt->fetch(PDO::FETCH_ASSOC);
          $globalEtablissementLogo = $etabRow['logo_etablissement'] ?? '';
          $globalEtablissementNom = $etabRow['libelle_etablissement'] ?? 'OLIVE SERVICE';
      } catch (Exception $e) {
          $globalEtablissementLogo = '';
          $globalEtablissementNom = 'OLIVE SERVICE';
      }
  }
  $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';

  // --- SYSTÈME D'AUTORISATIONS & RBAC DU SIDEBAR (OLIVE SERVICE 4 PROFILS) ---
  $userRoles = $_SESSION[USERS_AUTH]['roles'] ?? [];
  if (empty($userRoles)) {
      $singleRole = $_SESSION[USERS_AUTH]['role_code'] ?? ($_SESSION['role_code'] ?? 'ROLE_COMMERCIAL');
      $userRoles = !empty($singleRole) ? [$singleRole] : ['ROLE_COMMERCIAL'];
  }
  if (is_string($userRoles)) {
      $userRoles = [$userRoles];
  }
  $userRoleCode = $userRoles[0] ?? 'ROLE_COMMERCIAL';
  $isSuperAdmin = !empty(array_intersect($userRoles, ['ROLE_SUPERADMIN', 'ROLE_ADMIN', 'ROLE_DIR_GENERAL']));

  // Récupérer les permissions cumulées de tous les rôles de l'utilisateur
  $userPermissions = $_SESSION['permissions'] ?? [];
  if (!$isSuperAdmin && empty($userPermissions)) {
      try {
          $dbConn = (new Database())->getCon();
          $inClause = implode(',', array_fill(0, count($userRoles), '?'));
          $stmtP = $dbConn->prepare("
              SELECT DISTINCT rp.permission_code 
              FROM role_permissions rp
              JOIN permissions p ON rp.permission_code = p.code_permission
              WHERE rp.role_code IN ($inClause) AND p.statut_permission = 'actif'
          ");
          $stmtP->execute($userRoles);
          $userPermissions = $stmtP->fetchAll(PDO::FETCH_COLUMN) ?: [];
          $_SESSION['permissions'] = $userPermissions;
      } catch (Exception $e) {
          $userPermissions = [];
      }
  }

  $canAccess = function(array $requiredPerms = [], array $allowedRoles = []) use ($isSuperAdmin, $userRoles, $userPermissions) {
      if ($isSuperAdmin) return true;
      if (!empty($allowedRoles) && !empty(array_intersect($userRoles, $allowedRoles))) return true;
      if (in_array('*', $userPermissions, true)) return true;
      foreach ($requiredPerms as $perm) {
          if (in_array($perm, $userPermissions, true)) return true;
      }
      return false;
  };
?>
<style>
  /* --- BASE SIDEBAR STYLES --- */
  .sidebar {
    transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .sidebar-accordion-toggle {
    cursor: pointer;
    user-select: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    margin: 4px 0;
    border-radius: 8px;
    font-weight: 700;
    color: #475569;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.2s ease;
  }
  .sidebar-accordion-toggle:hover {
    background: rgba(30, 58, 95, 0.05);
    color: var(--primary-color);
  }
  .sidebar-accordion-toggle .chevron-icon {
    width: 14px;
    height: 14px;
    transition: transform 0.25s ease;
  }
  .sidebar-accordion-toggle[aria-expanded="true"] .chevron-icon {
    transform: rotate(180deg);
  }
  .sidebar-accordion-toggle[aria-expanded="true"] {
    color: var(--primary-color);
  }
  .sidebar-nav .nav-section-items {
    padding-left: 6px;
    display: none;
  }
  .sidebar-nav .nav-section-items.show {
    display: block;
  }
  .sidebar-nav .nav-item.sub {
    font-size: 13px;
    padding: 8px 12px 8px 16px;
    border-left: 2px solid transparent;
    margin: 2px 0;
    transition: all 0.2s ease;
  }
  .sidebar-nav .nav-item.sub.active,
  .sidebar-nav .nav-item.sub:hover {
    border-left-color: var(--primary-color);
    background: rgba(30, 58, 95, 0.06);
    color: var(--primary-color);
    font-weight: 700;
  }

  .sidebar-academic-badge .mini-badge {
    display: none;
  }

  /* --- COMPACT MINI SIDEBAR (COLLAPSED STATE) --- */
  .sidebar.collapsed {
    width: 76px !important;
    min-width: 76px !important;
    max-width: 76px !important;
    overflow-x: hidden;
  }
  .sidebar.collapsed .sidebar-header {
    padding: 12px 6px !important;
    justify-content: center !important;
    min-height: 64px !important;
  }
  .sidebar.collapsed .logo {
    display: none !important;
  }
  .sidebar.collapsed .sidebar-toggle {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 38px !important;
    height: 38px !important;
    border-radius: 8px !important;
    background: #EFF6FF !important;
    color: var(--primary-color) !important;
    border: 1.5px solid #BFDBFE !important;
    margin: 0 auto !important;
    cursor: pointer !important;
  }
  .sidebar.collapsed .sidebar-toggle:hover {
    background: #DBEAFE !important;
  }
  
  .sidebar.collapsed .sidebar-academic-badge {
    padding: 6px 4px !important;
    margin: 6px 6px !important;
  }
  .sidebar.collapsed .sidebar-academic-badge .full-badge {
    display: none !important;
  }
  .sidebar.collapsed .sidebar-academic-badge .mini-badge {
    display: block !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    color: var(--primary-color) !important;
  }

  .sidebar.collapsed .sidebar-accordion-toggle {
    display: none !important;
  }
  .sidebar.collapsed .nav-section-items {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    padding: 0 !important;
  }
  .sidebar.collapsed .nav-section {
    padding: 6px 0 !important;
    margin: 4px 0 !important;
    border-top: 1px solid #E2E8F0 !important;
    width: 100% !important;
  }
  .sidebar.collapsed .nav-item {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 44px !important;
    height: 42px !important;
    margin: 3px auto !important;
    padding: 0 !important;
    border-radius: 8px !important;
    position: relative !important;
    border-left: none !important;
  }
  .sidebar.collapsed .nav-item span {
    display: none !important;
  }
  .sidebar.collapsed .nav-item i,
  .sidebar.collapsed .nav-item [data-lucide] {
    width: 20px !important;
    height: 20px !important;
    margin: 0 !important;
  }
  .sidebar.collapsed .nav-item.active {
    background: var(--primary-color) !important;
    color: #FFFFFF !important;
  }
  .sidebar.collapsed .nav-item.active i,
  .sidebar.collapsed .nav-item.active [data-lucide] {
    color: #FFFFFF !important;
  }

  /* Tooltip flottant au survol en mode réduit */
  .sidebar.collapsed .nav-item:hover::after {
    content: attr(data-title);
    position: fixed;
    left: 86px;
    background: #0F172A;
    color: #FFFFFF;
    font-size: 12px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 6px;
    white-space: nowrap;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.3);
    z-index: 99999;
    pointer-events: none;
    line-height: 1.4;
  }

  .main-content.expanded {
    margin-left: 76px !important;
    width: calc(100% - 76px) !important;
    max-width: calc(100vw - 76px) !important;
  }
  .footer.expanded {
    margin-left: 76px !important;
    width: calc(100% - 76px) !important;
  }
</style>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo" style="display: flex; align-items: center; justify-content: center; max-height: 48px;">
            <?php if (!empty($globalEtablissementLogo)): ?>
                <?php $logoUrl = (strpos($globalEtablissementLogo, 'http') === 0) ? $globalEtablissementLogo : RACINE . ltrim($globalEtablissementLogo, '/'); ?>
                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo Olive Service" style="max-height: 42px; max-width: 140px; object-fit: contain;">
            <?php else: ?>
                <span style="letter-spacing: 1px; color: #059669; font-size: 20px; font-weight: 800;">
                    OLIVE SERVICE
                </span>
            <?php endif; ?>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle" title="Réduire / Déployer le menu">
            <i data-lucide="menu"></i>
        </button>
    </div>

    <!-- Badges Session / Année Active -->
    <div class="sidebar-academic-badge p-2 mx-2 my-2 rounded bg-light border text-center">
        <div class="full-badge">
            <div class="text-uppercase text-muted" style="font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">Profil : <?= htmlspecialchars($userRoleCode) ?></div>
            <div class="fw-bold text-success" style="font-size: 13px;">
                <?= htmlspecialchars($_SESSION['annee_active_libelle'] ?? 'Session Active') ?>
            </div>
        </div>
        <div class="mini-badge" title="Session Active">
            OLIVE
        </div>
    </div>

    <nav class="sidebar-nav">
        <!-- ACCUEIL (Visible pour tous) -->
        <a href="<?= RACINE ?>" class="nav-item <?= ($currentUri === RACINE || $currentUri === RACINE . 'public/' || $currentUri === '/geicg/' || $currentUri === '/geicg/public/') ? 'active' : '' ?>" data-title="Tableau de bord">
            <i data-lucide="layout-dashboard"></i> <span>Tableau de bord</span>
        </a>

        <!-- === MODULE COMMERCIAL (NOUVELLE FONCTIONNALITÉ OVOLIA) === -->
        <?php if ($canAccess(['COMMERCIAL_VIEW_OWN_CLIENTS', 'COMMERCIAL_ADD_CLIENT', 'COMMERCIAL_RECORD_VENTE_AVICOLE'], ['ROLE_COMMERCIAL', 'ROLE_ADMIN'])): ?>
        <div class="nav-section">
            <div class="sidebar-accordion-toggle" data-bs-target="#sec-commercial" aria-expanded="false">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="briefcase" style="width: 16px; height: 16px; color: #059669;"></i> <span>Espace Commercial</span>
                </div>
                <i data-lucide="chevron-down" class="chevron-icon"></i>
            </div>
            <div class="nav-section-items" id="sec-commercial">
                <a href="<?= RACINE ?>aviculture/clients" class="nav-item sub <?= strpos($currentUri, '/aviculture/clients') !== false ? 'active' : '' ?>" data-title="Clients Avicoles">
                    <i data-lucide="users"></i> <span>Clients Avicoles</span>
                </a>
                <a href="<?= RACINE ?>aviculture/ventes" class="nav-item sub <?= strpos($currentUri, '/aviculture/ventes') !== false ? 'active' : '' ?>" data-title="Ventes Avicoles">
                    <i data-lucide="shopping-cart"></i> <span>Ventes Avicoles</span>
                </a>
                <a href="<?= RACINE ?>aviculture/categories_poids" class="nav-item sub <?= strpos($currentUri, '/aviculture/categories_poids') !== false ? 'active' : '' ?>" data-title="Grille Poids & Tarifs">
                    <i data-lucide="scale"></i> <span>Grille Poids & Tarifs</span>
                </a>
            </div>
        </div>

        <!-- MODULE MA CAISSE & VERSEMENTS (NOUVELLE FONCTIONNALITÉ OVOLIA) -->
        <div class="nav-section">
            <div class="sidebar-accordion-toggle" data-bs-target="#sec-decharger" aria-expanded="false">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="wallet" style="width: 16px; height: 16px; color: #047857;"></i> <span>Ma Caisse & Verser</span>
                </div>
                <i data-lucide="chevron-down" class="chevron-icon"></i>
            </div>
            <div class="nav-section-items" id="sec-decharger">
                <a href="<?= RACINE ?>aviculture/ventes" class="nav-item sub <?= strpos($currentUri, '/aviculture/ventes') !== false ? 'active' : '' ?>" data-title="Encaissements Ventes">
                    <i data-lucide="shopping-cart"></i> <span>Encaissements Ventes</span>
                </a>
                <a href="<?= RACINE ?>caisse_commercial/formulaire" class="nav-item sub <?= strpos($currentUri, '/caisse_commercial/') !== false ? 'active' : '' ?>" data-title="Ma Caisse Journalière">
                    <i data-lucide="lock"></i> <span>Ma Caisse Journalière</span>
                </a>
                <a href="<?= RACINE ?>versement/formulaire" class="nav-item sub <?= strpos($currentUri, '/versement/formulaire') !== false ? 'active' : '' ?>" data-title="Faire un Versement">
                    <i data-lucide="send"></i> <span>Faire un Versement</span>
                </a>
                <a href="<?= RACINE ?>versement/list" class="nav-item sub <?= strpos($currentUri, '/versement/list') !== false ? 'active' : '' ?>" data-title="Mes Versements">
                    <i data-lucide="history"></i> <span>Mes Versements</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- === MODULE CATALOGUE DES PRODUITS (NOUVELLE FONCTIONNALITÉ OVOLIA) === -->
        <?php if ($canAccess(['GESTIONNAIRE_MANAGE_PACKS', 'GESTIONNAIRE_MANAGE_ARTICLES', 'GESTIONNAIRE_MANAGE_CATEGORIES_POIDS'], ['ROLE_GESTIONNAIRE', 'ROLE_ADMIN'])): ?>
        <div class="nav-section">
            <div class="sidebar-accordion-toggle" data-bs-target="#sec-catalogue" aria-expanded="false">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="package" style="width: 16px; height: 16px; color: #2563EB;"></i> <span>Catalogue des Produits</span>
                </div>
                <i data-lucide="chevron-down" class="chevron-icon"></i>
            </div>
            <div class="nav-section-items" id="sec-catalogue">
                <a href="<?= RACINE ?>aviculture/produits" class="nav-item sub <?= strpos($currentUri, '/aviculture/produits') !== false ? 'active' : '' ?>" data-title="Produits Avicoles">
                    <i data-lucide="shopping-bag"></i> <span>Produits Avicoles</span>
                </a>
                <a href="<?= RACINE ?>aviculture/categories_poids" class="nav-item sub <?= strpos($currentUri, '/aviculture/categories_poids') !== false ? 'active' : '' ?>" data-title="Grille Poids & Tarifs">
                    <i data-lucide="scale"></i> <span>Grille Poids & Tarifs</span>
                </a>
                <a href="<?= RACINE ?>aviculture/pesees" class="nav-item sub <?= strpos($currentUri, '/aviculture/pesees') !== false ? 'active' : '' ?>" data-title="Pesées & Étiquettes">
                    <i data-lucide="qr-code"></i> <span>Pesées & Étiquettes</span>
                </a>
            </div>
        </div>

        <!-- MODULE GESTIONNAIRE : LOGISTIQUE & APPROVISIONNEMENTS -->
        <div class="nav-section">
            <div class="sidebar-accordion-toggle" data-bs-target="#sec-distribution" aria-expanded="false">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="truck" style="width: 16px; height: 16px; color: #D97706;"></i> <span>Logistique & Stock</span>
                </div>
                <i data-lucide="chevron-down" class="chevron-icon"></i>
            </div>
            <div class="nav-section-items" id="sec-distribution">
                <a href="<?= RACINE ?>aviculture/stock" target="_blank" class="nav-item sub <?= strpos($currentUri, '/aviculture/stock') !== false ? 'active' : '' ?>" data-title="État des Stocks">
                    <i data-lucide="package"></i> <span>État des Stocks</span>
                </a>
                <a href="<?= RACINE ?>aviculture/mouvements_stock" target="_blank" class="nav-item sub <?= strpos($currentUri, '/aviculture/mouvements_stock') !== false ? 'active' : '' ?>" data-title="Mouvements de Stock">
                    <i data-lucide="history"></i> <span>Mouvements de Stock</span>
                </a>
                <a href="<?= RACINE ?>aviculture/pesees" class="nav-item sub <?= strpos($currentUri, '/aviculture/pesees') !== false ? 'active' : '' ?>" data-title="Pesées & Étiquettes">
                    <i data-lucide="qr-code"></i> <span>Pesées & Étiquettes</span>
                </a>
                <a href="<?= RACINE ?>aviculture/achats" class="nav-item sub <?= strpos($currentUri, '/aviculture/achats') !== false ? 'active' : '' ?>" data-title="Achats Produits Finis">
                    <i data-lucide="shopping-bag"></i> <span>Achats &amp; Entrées</span>
                </a>
                <a href="<?= RACINE ?>aviculture/fournisseurs" class="nav-item sub <?= strpos($currentUri, '/aviculture/fournisseurs') !== false ? 'active' : '' ?>" data-title="Fournisseurs Avicoles">
                    <i data-lucide="building"></i> <span>Fournisseurs Avicoles</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- === MODULE FINANCE : FINANCES & TRÉSORERIE (NOUVELLE FONCTIONNALITÉ OVOLIA) === -->
        <?php if ($canAccess(['FINANCE_VIEW_ALL_COTISATIONS', 'FINANCE_VALIDATE_VERSEMENT', 'FINANCE_VIEW_RAPPORTS_AVICULTURE'], ['ROLE_FINANCE', 'ROLE_ADMIN'])): ?>
        <div class="nav-section">
            <div class="sidebar-accordion-toggle" data-bs-target="#sec-finance" aria-expanded="false">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="credit-card" style="width: 16px; height: 16px; color: #DC2626;"></i> <span>Finances & Trésorerie</span>
                </div>
                <i data-lucide="chevron-down" class="chevron-icon"></i>
            </div>
            <div class="nav-section-items" id="sec-finance">
                <a href="<?= RACINE ?>aviculture/ventes" class="nav-item sub <?= strpos($currentUri, '/aviculture/ventes') !== false ? 'active' : '' ?>" data-title="Ventes & Encaissements">
                    <i data-lucide="shopping-cart"></i> <span>Ventes & Encaissements</span>
                </a>
                <a href="<?= RACINE ?>aviculture/achats" class="nav-item sub <?= strpos($currentUri, '/aviculture/achats') !== false ? 'active' : '' ?>" data-title="Achats & Règlements">
                    <i data-lucide="shopping-bag"></i> <span>Achats & Règlements</span>
                </a>
                <a href="<?= RACINE ?>depense/list" class="nav-item sub <?= strpos($currentUri, '/depense/') !== false ? 'active' : '' ?>" data-title="Dépenses d'Exploitation">
                    <i data-lucide="arrow-up-right"></i> <span>Dépenses Exploitation</span>
                </a>
                <a href="<?= RACINE ?>caisse_commercial/list" class="nav-item sub <?= strpos($currentUri, '/caisse_commercial/list') !== false ? 'active' : '' ?>" data-title="Journal des Caisses">
                    <i data-lucide="archive"></i> <span>Journal des Caisses</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- === MODULE ADMINISTRATION & RBAC (ROLE_ADMIN) === -->
        <?php if ($canAccess(['ADMIN_MANAGE_USERS', 'ADMIN_MANAGE_ROLES'], ['ROLE_ADMIN'])): ?>
        <div class="nav-section">
            <div class="sidebar-accordion-toggle" data-bs-target="#sec-admin" aria-expanded="false">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="shield-check" style="width: 16px; height: 16px; color: #7C3AED;"></i> <span>Administration & Accès</span>
                </div>
                <i data-lucide="chevron-down" class="chevron-icon"></i>
            </div>
            <div class="nav-section-items" id="sec-admin">
                <a href="<?= RACINE ?>user/list" class="nav-item sub <?= strpos($currentUri, '/user/') !== false ? 'active' : '' ?>" data-title="Utilisateurs Système">
                    <i data-lucide="users"></i> <span>Utilisateurs Système</span>
                </a>
                <a href="<?= RACINE ?>fonction/list" class="nav-item sub <?= strpos($currentUri, '/fonction/') !== false ? 'active' : '' ?>" data-title="Fonctions Utilisateurs">
                    <i data-lucide="briefcase"></i> <span>Fonctions Utilisateurs</span>
                </a>
                <a href="<?= RACINE ?>role/list" class="nav-item sub <?= strpos($currentUri, '/role/') !== false ? 'active' : '' ?>" data-title="Rôles & Groupes">
                    <i data-lucide="shield"></i> <span>Rôles & Groupes RBAC</span>
                </a>
                <a href="<?= RACINE ?>permission/list" class="nav-item sub <?= strpos($currentUri, '/permission/') !== false ? 'active' : '' ?>" data-title="Permissions Granulaires">
                    <i data-lucide="key"></i> <span>Permissions Granulaires</span>
                </a>
            </div>
        </div>

        <div class="nav-section">
            <div class="sidebar-accordion-toggle" data-bs-target="#sec-config" aria-expanded="false">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="settings" style="width: 16px; height: 16px; color: #64748B;"></i> <span>Configuration Système</span>
                </div>
                <i data-lucide="chevron-down" class="chevron-icon"></i>
            </div>
            <div class="nav-section-items" id="sec-config">
                <a href="<?= RACINE ?>annee/list" class="nav-item sub <?= strpos($currentUri, '/annee/') !== false ? 'active' : '' ?>" data-title="Année d'Activité">
                    <i data-lucide="calendar"></i> <span>Années</span>
                </a>
                <a href="<?= RACINE ?>session/list" class="nav-item sub <?= strpos($currentUri, '/session/') !== false ? 'active' : '' ?>" data-title="Sessions de Cotisation">
                    <i data-lucide="clock"></i> <span>Sessions</span>
                </a>
                <a href="<?= RACINE ?>zone/list" class="nav-item sub <?= strpos($currentUri, '/zone/') !== false && strpos($currentUri, '/zone_commercial/') === false ? 'active' : '' ?>" data-title="Zones Géographiques">
                    <i data-lucide="map-pin"></i> <span>Zones Géographiques</span>
                </a>
                <a href="<?= RACINE ?>etablissement/config" class="nav-item sub <?= strpos($currentUri, '/etablissement/') !== false ? 'active' : '' ?>" data-title="Établissements">
                    <i data-lucide="landmark"></i> <span>Établissements</span>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </nav>
</aside>

<script>
$(document).ready(function() {
  // Accordéons du sidebar
  $(document).on('click', '.sidebar-accordion-toggle', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var $toggle = $(this);
    var targetId = $toggle.attr('data-bs-target');
    var $target = $(targetId);

    if ($target.length) {
      var isExpanded = $toggle.attr('aria-expanded') === 'true';
      if (isExpanded) {
        $target.slideUp(200, function() {
          $target.removeClass('show');
        });
        $toggle.attr('aria-expanded', 'false');
      } else {
        $target.slideDown(200, function() {
          $target.addClass('show');
        });
        $toggle.attr('aria-expanded', 'true');
      }
    }
  });

  // Déplier automatiquement la section contenant le lien actif
  var $activeLink = $('.sidebar-nav .nav-item.sub.active');
  if ($activeLink.length) {
    var $parentItems = $activeLink.closest('.nav-section-items');
    if ($parentItems.length) {
      $parentItems.addClass('show').show();
      var $parentToggle = $parentItems.siblings('.sidebar-accordion-toggle');
      if ($parentToggle.length) {
        $parentToggle.attr('aria-expanded', 'true');
      }
    }
  } else {
    var $firstSection = $('.sidebar-nav .nav-section-items').first();
    if ($firstSection.length) {
      $firstSection.addClass('show').show();
      var $firstToggle = $firstSection.siblings('.sidebar-accordion-toggle');
      if ($firstToggle.length) {
        $firstToggle.attr('aria-expanded', 'true');
      }
    }
  }

  if (window.lucide) {
    lucide.createIcons();
  }
});
</script>
