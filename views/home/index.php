<?php
require_once __DIR__ . '/../../public/inc/header.php';

$stats = $stats ?? [];
$auth = $auth ?? ($_SESSION[USERS_AUTH] ?? []);
$roleCode = $roleCode ?? ($auth['role_code'] ?? '');
$userCode = $userCode ?? ($auth['code_user'] ?? '');

$recentVentesAvicoles = $recentVentesAvicoles ?? [];
$recentPeseesAvicoles = $recentPeseesAvicoles ?? [];
$recentAchatsAvicoles = $recentAchatsAvicoles ?? [];
$recentDepenses = $recentDepenses ?? [];

// Récupération des permissions en session pour le filtrage d'accès
$userPermissions = $_SESSION['user_permissions'] ?? ($_SESSION['permissions'] ?? []);
$userRole = $_SESSION[USERS_AUTH]['role_code'] ?? ($auth['role_code'] ?? '');

$canAccess = function(array $perms = [], array $roles = []) use ($userPermissions, $userRole): bool {
    if (in_array($userRole, ['ROLE_SUPERADMIN', 'ROLE_ADMIN'], true)) {
        return true;
    }
    if (!empty($roles) && in_array($userRole, $roles, true)) {
        return true;
    }
    if (!empty($perms)) {
        foreach ($perms as $p) {
            if (in_array($p, $userPermissions, true)) {
                return true;
            }
        }
    }
    return false;
};
?>

<style>
/* ==========================================================================
   DESIGN SYSTEM & ANIMATIONS ULTRA-PREMIUM - OVOLIA AVICULTURE DASHBOARD
   ========================================================================== */

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.dashboard-content-wrapper {
  padding: 28px;
  width: 100%;
  box-sizing: border-box;
  animation: fadeInUp 0.4s ease-out;
}

/* Banner Header Ultra Modern */
.dashboard-header-card {
  background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #0F2942 100%);
  border-radius: 16px;
  padding: 24px 28px;
  color: #FFFFFF;
  margin-bottom: 28px;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
  position: relative;
  overflow: hidden;
}

.dashboard-header-card::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -10%;
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, rgba(5, 150, 105, 0.2) 0%, rgba(255, 255, 255, 0) 70%);
  border-radius: 50%;
  pointer-events: none;
}

.header-title-box {
  display: flex;
  align-items: center;
  gap: 16px;
}

.header-icon-badge {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #10B981;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.header-actions-group {
  display: flex;
  gap: 12px;
  align-items: center;
  flex-wrap: wrap;
}

.action-btn-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-weight: 700;
  font-size: 13px;
  padding: 10px 18px;
  border-radius: 10px;
  text-decoration: none;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.action-btn-pill:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}

.btn-pill-primary {
  background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
  color: #FFFFFF;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-pill-success {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  color: #FFFFFF;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-pill-warning {
  background: linear-gradient(135deg, #D97706 0%, #B45309 100%);
  color: #FFFFFF;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-pill-danger {
  background: rgba(255, 255, 255, 0.15);
  color: #FCA5A5;
  border: 1px solid rgba(252, 165, 165, 0.3);
  backdrop-filter: blur(8px);
}
.btn-pill-danger:hover {
  background: rgba(220, 38, 38, 0.9);
  color: #FFFFFF;
}

/* Grid KPI Premium */
.kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 20px;
  margin-bottom: 32px;
}

.kpi-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 22px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.kpi-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--kpi-accent, #059669);
  border-radius: 16px 16px 0 0;
  transition: height 0.25s ease;
}

.kpi-card:hover {
  transform: translateY(-5px) scale(1.01);
  box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.08);
  border-color: rgba(5, 150, 105, 0.3);
}

.kpi-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 14px;
}

.kpi-title {
  font-size: 11.5px;
  font-weight: 700;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.kpi-icon-wrapper {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--kpi-bg-icon, #ECFDF5);
  color: var(--kpi-color-icon, #059669);
  transition: all 0.3s ease;
}

.kpi-card:hover .kpi-icon-wrapper {
  transform: scale(1.12) rotate(4deg);
}

.kpi-value {
  font-size: 24px;
  font-weight: 800;
  color: #0F172A;
  line-height: 1.1;
  letter-spacing: -0.5px;
}

.kpi-footer {
  font-size: 12px;
  color: #64748B;
  margin-top: 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.kpi-tag {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 3px 8px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
}

/* Quick Actions Cards */
.quick-action-card {
  background: #FFFFFF;
  border-radius: 14px;
  padding: 18px;
  border: 1px solid #E2E8F0;
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: all 0.25s ease;
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.quick-action-card:hover {
  transform: translateY(-3px);
  border-color: #1E3A5F;
  box-shadow: 0 10px 15px -3px rgba(30, 58, 95, 0.08);
}

.quick-action-content {
  display: flex;
  align-items: center;
  gap: 14px;
}

.quick-action-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.25s ease;
}

.quick-action-card:hover .quick-action-icon {
  transform: scale(1.1);
}

.quick-action-arrow {
  color: #94A3B8;
  transition: transform 0.25s ease, color 0.25s ease;
}

.quick-action-card:hover .quick-action-arrow {
  transform: translateX(4px);
  color: #1E3A5F;
}

/* Modern Tables Styling */
.table-card {
  background: #FFFFFF;
  border-radius: 16px;
  border: 1px solid #E2E8F0;
  padding: 22px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
  transition: box-shadow 0.3s ease;
}

.table-card:hover {
  box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.06);
}

.table-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 18px;
  border-bottom: 1px solid #F1F5F9;
  padding-bottom: 14px;
}

.table-card-title {
  font-size: 15px;
  font-weight: 800;
  color: #0F172A;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

.custom-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}

.custom-table th {
  background: #F8FAFC;
  color: #475569;
  font-weight: 700;
  text-transform: uppercase;
  font-size: 11px;
  letter-spacing: 0.5px;
  padding: 12px 14px;
  border-bottom: 1px solid #E2E8F0;
}

.custom-table td {
  padding: 12px 14px;
  border-bottom: 1px solid #F1F5F9;
  transition: background 0.2s ease;
}

.custom-table tr:hover td {
  background: #F8FAFC;
}
</style>

<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>

    <div class="dashboard-content-wrapper">
      
      <!-- ========================================================================= -->
      <!-- BANNER D'EN-TÊTE DYNAMIQUE SELON LE RÔLE                                  -->
      <!-- ========================================================================= -->
      <div class="dashboard-header-card">
        <div class="header-title-box">
          <div class="header-icon-badge">
            <i data-lucide="egg" style="width: 28px; height: 28px;"></i>
          </div>
          <div>
            <h1 style="font-size: 24px; font-weight: 800; margin: 0; letter-spacing: -0.5px;">
              OVOLIA Aviculture &bull; Tableau de Bord
            </h1>
            <p style="margin: 4px 0 0 0; font-size: 13px; color: #94A3B8; font-weight: 500;">
              Bienvenue, <strong style="color: #FFFFFF;"><?= htmlspecialchars($auth['nom_user'] ?? 'Utilisateur') ?> <?= htmlspecialchars($auth['prenom_user'] ?? '') ?></strong> 
              <span class="badge" style="background: #059669; margin-left: 8px; vertical-align: middle; padding: 3px 8px; font-size: 11px; border-radius: 6px;">
                <?= htmlspecialchars($roleCode) ?>
              </span>
            </p>
          </div>
        </div>

        <!-- ACTIONS RAPIDES BOUTONS PILULE FILTRÉES PAR PERMISSIONS -->
        <div class="header-actions-group">
          <?php if ($canAccess(['COMMERCIAL_RECORD_VENTE_AVICOLE', 'GESTIONNAIRE_RECORD_VENTE_AVICOLE'], ['ROLE_COMMERCIAL', 'ROLE_CAISSIER'])): ?>
            <a href="<?= RACINE ?>aviculture/ventes" target="_blank" class="action-btn-pill btn-pill-success">
              <i data-lucide="shopping-cart" style="width: 17px; height: 17px;"></i> Caisse Ventes POS
            </a>
          <?php endif; ?>

          <?php if ($canAccess(['COMMERCIAL_ADD_CLIENT', 'GESTIONNAIRE_MANAGE_CLIENTS_AVICOLES'], ['ROLE_COMMERCIAL'])): ?>
            <a href="<?= RACINE ?>aviculture/clients" class="action-btn-pill btn-pill-primary">
              <i data-lucide="user-plus" style="width: 17px; height: 17px;"></i> Nouveau Client
            </a>
          <?php endif; ?>

          <?php if ($canAccess(['GESTIONNAIRE_RECORD_PESEE_ETIQUETTE'], ['ROLE_GESTIONNAIRE'])): ?>
            <a href="<?= RACINE ?>aviculture/pesees" class="action-btn-pill btn-pill-warning">
              <i data-lucide="scale" style="width: 17px; height: 17px;"></i> Saisir Pesée Net
            </a>
          <?php endif; ?>

          <?php if ($canAccess(['FINANCE_MANAGE_DEPENSES'], ['ROLE_FINANCE', 'ROLE_COMPTABLE'])): ?>
            <a href="<?= RACINE ?>depense/formulaire" class="action-btn-pill btn-pill-danger">
              <i data-lucide="arrow-up-right" style="width: 17px; height: 17px;"></i> Saisir Dépense
            </a>
          <?php endif; ?>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- SECTION 1 : GRILLE KPI DYNAMIQUE ET DIVERSIFIÉE PAR PROFIL                 -->
      <!-- ========================================================================= -->
      
      <div class="kpi-grid">
        
        <!-- KPI 1 : Ventes Avicoles (Chiffre d'Affaires) -->
        <?php if ($canAccess(['COMMERCIAL_RECORD_VENTE_AVICOLE', 'FINANCE_VIEW_RAPPORTS_AVICULTURE'], ['ROLE_COMMERCIAL', 'ROLE_CAISSIER', 'ROLE_FINANCE', 'ROLE_DIR_GENERAL'])): ?>
        <div class="kpi-card" style="--kpi-accent: linear-gradient(90deg, #059669, #047857); --kpi-bg-icon: #ECFDF5; --kpi-color-icon: #059669;">
          <div class="kpi-header">
            <span class="kpi-title">Recettes Ventes POS</span>
            <div class="kpi-icon-wrapper">
              <i data-lucide="dollar-sign" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          <div class="kpi-value" style="color: #059669;">
            <?= number_format($stats['ca_ventes'] ?? 0, 0, ',', ' ') ?> <span style="font-size: 13px; font-weight: 600;">FCFA</span>
          </div>
          <div class="kpi-footer">
            <span>Volume de ventes</span>
            <span class="kpi-tag" style="background: #ECFDF5; color: #059669;">
              <i data-lucide="shopping-bag" style="width: 12px; height: 12px;"></i> <?= (int)($stats['total_ventes'] ?? 0) ?> factures
            </span>
          </div>
        </div>
        <?php endif; ?>

        <!-- KPI 2 : Pesées & Poids Net Réel (Kg) -->
        <?php if ($canAccess(['GESTIONNAIRE_RECORD_PESEE_ETIQUETTE', 'FINANCE_VALIDATE_PESEE_STOCK'], ['ROLE_GESTIONNAIRE', 'ROLE_DIR_GENERAL'])): ?>
        <div class="kpi-card" style="--kpi-accent: linear-gradient(90deg, #D97706, #B45309); --kpi-bg-icon: #FFFBEB; --kpi-color-icon: #D97706;">
          <div class="kpi-header">
            <span class="kpi-title">Pesées & Poids Net</span>
            <div class="kpi-icon-wrapper">
              <i data-lucide="scale" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          <div class="kpi-value" style="color: #D97706;">
            <?= number_format($stats['poids_total_pesees'] ?? 0, 2, ',', ' ') ?> <span style="font-size: 13px; font-weight: 600;">Kg</span>
          </div>
          <div class="kpi-footer">
            <span>Lots contrôlés</span>
            <span class="kpi-tag" style="background: #FFFBEB; color: #D97706;">
              <i data-lucide="qr-code" style="width: 12px; height: 12px;"></i> <?= (int)($stats['total_pesees'] ?? 0) ?> étiquettes
            </span>
          </div>
        </div>
        <?php endif; ?>

        <!-- KPI 3 : Achats & Approvisionnements (FCFA) -->
        <?php if ($canAccess(['GESTIONNAIRE_RECORD_ACHAT_AVICOLE', 'LOGISTIQUE_VERIFIER_BON_ACHAT'], ['ROLE_GESTIONNAIRE', 'ROLE_FINANCE', 'ROLE_DIR_GENERAL'])): ?>
        <div class="kpi-card" style="--kpi-accent: linear-gradient(90deg, #2563EB, #1D4ED8); --kpi-bg-icon: #EFF6FF; --kpi-color-icon: #2563EB;">
          <div class="kpi-header">
            <span class="kpi-title">Achats Intrants</span>
            <div class="kpi-icon-wrapper">
              <i data-lucide="truck" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          <div class="kpi-value" style="color: #2563EB;">
            <?= number_format($stats['montant_achats'] ?? 0, 0, ',', ' ') ?> <span style="font-size: 13px; font-weight: 600;">FCFA</span>
          </div>
          <div class="kpi-footer">
            <span>Fournisseurs</span>
            <span class="kpi-tag" style="background: #EFF6FF; color: #2563EB;">
              <i data-lucide="box" style="width: 12px; height: 12px;"></i> <?= (int)($stats['total_achats'] ?? 0) ?> bons d'achat
            </span>
          </div>
        </div>
        <?php endif; ?>

        <!-- KPI 4 : Dépenses d'Exploitation -->
        <?php if ($canAccess(['FINANCE_MANAGE_DEPENSES', 'FINANCE_VIEW_RAPPORTS_AVICULTURE'], ['ROLE_FINANCE', 'ROLE_COMPTABLE', 'ROLE_DIR_GENERAL'])): ?>
        <div class="kpi-card" style="--kpi-accent: linear-gradient(90deg, #EF4444, #B91C1C); --kpi-bg-icon: #FEF2F2; --kpi-color-icon: #DC2626;">
          <div class="kpi-header">
            <span class="kpi-title">Dépenses & Charges</span>
            <div class="kpi-icon-wrapper">
              <i data-lucide="arrow-up-right" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          <div class="kpi-value" style="color: #DC2626;">
            <?= number_format($stats['total_depenses'] ?? 0, 0, ',', ' ') ?> <span style="font-size: 13px; font-weight: 600;">FCFA</span>
          </div>
          <div class="kpi-footer">
            <span>Frais généraux</span>
            <span class="kpi-tag" style="background: #FEF2F2; color: #DC2626;">
              <i data-lucide="trending-down" style="width: 12px; height: 12px;"></i> Charges
            </span>
          </div>
        </div>
        <?php endif; ?>

        <!-- KPI 5 : Répertoire Clients Avicoles -->
        <?php if ($canAccess(['COMMERCIAL_VIEW_OWN_CLIENTS', 'GESTIONNAIRE_MANAGE_CLIENTS_AVICOLES'], ['ROLE_COMMERCIAL', 'ROLE_GESTIONNAIRE', 'ROLE_DIR_GENERAL'])): ?>
        <div class="kpi-card" style="--kpi-accent: linear-gradient(90deg, #7E22CE, #6B21A8); --kpi-bg-icon: #FAF5FF; --kpi-color-icon: #7E22CE;">
          <div class="kpi-header">
            <span class="kpi-title">Clients Avicoles</span>
            <div class="kpi-icon-wrapper">
              <i data-lucide="users" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          <div class="kpi-value" style="color: #7E22CE;">
            <?= number_format($stats['total_clients'] ?? 0, 0, ',', ' ') ?> <span style="font-size: 14px; font-weight: 600;">Fiches</span>
          </div>
          <div class="kpi-footer">
            <span>Portfolio actif</span>
            <span class="kpi-tag" style="background: #FAF5FF; color: #7E22CE;">
              <i data-lucide="user-check" style="width: 12px; height: 12px;"></i> Répertoire
            </span>
          </div>
        </div>
        <?php endif; ?>

        <!-- KPI 6 : Produits au Catalogue -->
        <?php if ($canAccess(['GESTIONNAIRE_MANAGE_PRODUITS_AVICOLES', 'COMMERCIAL_VIEW_AVICULTURE_CATALOGUE'], ['ROLE_GESTIONNAIRE', 'ROLE_COMMERCIAL', 'ROLE_DIR_GENERAL'])): ?>
        <div class="kpi-card" style="--kpi-accent: linear-gradient(90deg, #0D9488, #0F766E); --kpi-bg-icon: #F0FDFA; --kpi-color-icon: #0D9488;">
          <div class="kpi-header">
            <span class="kpi-title">Produits Catalogue</span>
            <div class="kpi-icon-wrapper">
              <i data-lucide="boxes" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          <div class="kpi-value" style="color: #0D9488;">
            <?= number_format($stats['total_produits'] ?? 0, 0, ',', ' ') ?> <span style="font-size: 14px; font-weight: 600;">Articles</span>
          </div>
          <div class="kpi-footer">
            <span>Gamme OVOLIA</span>
            <span class="kpi-tag" style="background: #F0FDFA; color: #0D9488;">
              <i data-lucide="check-circle" style="width: 12px; height: 12px;"></i> Actifs
            </span>
          </div>
        </div>
        <?php endif; ?>

        <!-- KPI 7 : Solde Net Trésorerie -->
        <?php if ($canAccess(['FINANCE_VIEW_RAPPORTS_AVICULTURE', 'ADMIN_VIEW_RAPPORTS_FINANCIERS'], ['ROLE_FINANCE', 'ROLE_COMPTABLE', 'ROLE_DIR_GENERAL', 'ROLE_ADMIN', 'ROLE_SUPERADMIN'])): ?>
        <div class="kpi-card" style="--kpi-accent: linear-gradient(90deg, #4F46E5, #3730A3); --kpi-bg-icon: #EEF2FF; --kpi-color-icon: #4F46E5;">
          <div class="kpi-header">
            <span class="kpi-title">Solde Net Trésorerie</span>
            <div class="kpi-icon-wrapper">
              <i data-lucide="wallet" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          <div class="kpi-value" style="color: <?= ($stats['solde_net'] ?? 0) >= 0 ? '#15803D' : '#DC2626' ?>;">
            <?= number_format($stats['solde_net'] ?? 0, 0, ',', ' ') ?> <span style="font-size: 13px; font-weight: 600;">FCFA</span>
          </div>
          <div class="kpi-footer">
            <span>Recettes Ventes &minus; Dépenses</span>
            <span class="kpi-tag" style="background: #EEF2FF; color: #4F46E5;">Bilan</span>
          </div>
        </div>
        <?php endif; ?>

      </div>

      <!-- ========================================================================= -->
      <!-- SECTION 2 : RACCOURCIS D'ACCÈS RAPIDES SELON L'AUTORISATION                 -->
      <!-- ========================================================================= -->
      <div style="margin-bottom: 32px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0 0 16px 0; display: flex; align-items: center; gap: 10px;">
          <i data-lucide="zap" style="color: #059669; width: 20px; height: 20px;"></i> Raccourcis d'Accès Rapides & Modules Métiers
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
          
          <?php if ($canAccess(['COMMERCIAL_RECORD_VENTE_AVICOLE', 'GESTIONNAIRE_RECORD_VENTE_AVICOLE'], ['ROLE_COMMERCIAL', 'ROLE_CAISSIER'])): ?>
          <a href="<?= RACINE ?>aviculture/ventes" target="_blank" class="quick-action-card">
            <div class="quick-action-content">
              <div class="quick-action-icon" style="background: #ECFDF5; color: #059669;">
                <i data-lucide="shopping-cart" style="width: 22px; height: 22px;"></i>
              </div>
              <div>
                <strong style="color: #0F172A; font-size: 13.5px; display: block; font-weight: 700;">Caisse Ventes POS</strong>
                <small style="color: #64748B;">Comptoir & encaissements</small>
              </div>
            </div>
            <i data-lucide="chevron-right" class="quick-action-arrow" style="width: 18px; height: 18px;"></i>
          </a>
          <?php endif; ?>

          <?php if ($canAccess(['GESTIONNAIRE_RECORD_PESEE_ETIQUETTE'], ['ROLE_GESTIONNAIRE'])): ?>
          <a href="<?= RACINE ?>aviculture/pesees" class="quick-action-card">
            <div class="quick-action-content">
              <div class="quick-action-icon" style="background: #FFFBEB; color: #D97706;">
                <i data-lucide="scale" style="width: 22px; height: 22px;"></i>
              </div>
              <div>
                <strong style="color: #0F172A; font-size: 13.5px; display: block; font-weight: 700;">Pesées & Calibrages</strong>
                <small style="color: #64748B;">Étiquettes poids net</small>
              </div>
            </div>
            <i data-lucide="chevron-right" class="quick-action-arrow" style="width: 18px; height: 18px;"></i>
          </a>
          <?php endif; ?>

          <?php if ($canAccess(['GESTIONNAIRE_MANAGE_STOCK_AVICOLE'], ['ROLE_GESTIONNAIRE'])): ?>
          <a href="<?= RACINE ?>aviculture/stock" class="quick-action-card">
            <div class="quick-action-content">
              <div class="quick-action-icon" style="background: #F0FDFA; color: #0D9488;">
                <i data-lucide="boxes" style="width: 22px; height: 22px;"></i>
              </div>
              <div>
                <strong style="color: #0F172A; font-size: 13.5px; display: block; font-weight: 700;">État du Stock</strong>
                <small style="color: #64748B;">Inventaire volailles/œufs</small>
              </div>
            </div>
            <i data-lucide="chevron-right" class="quick-action-arrow" style="width: 18px; height: 18px;"></i>
          </a>
          <?php endif; ?>

          <?php if ($canAccess(['GESTIONNAIRE_RECORD_ACHAT_AVICOLE'], ['ROLE_GESTIONNAIRE'])): ?>
          <a href="<?= RACINE ?>aviculture/achats" class="quick-action-card">
            <div class="quick-action-content">
              <div class="quick-action-icon" style="background: #EFF6FF; color: #2563EB;">
                <i data-lucide="truck" style="width: 22px; height: 22px;"></i>
              </div>
              <div>
                <strong style="color: #0F172A; font-size: 13.5px; display: block; font-weight: 700;">Achats & Intrants</strong>
                <small style="color: #64748B;">Approvisionnements</small>
              </div>
            </div>
            <i data-lucide="chevron-right" class="quick-action-arrow" style="width: 18px; height: 18px;"></i>
          </a>
          <?php endif; ?>

          <?php if ($canAccess(['COMMERCIAL_ADD_CLIENT', 'COMMERCIAL_VIEW_OWN_CLIENTS'], ['ROLE_COMMERCIAL', 'ROLE_GESTIONNAIRE'])): ?>
          <a href="<?= RACINE ?>aviculture/clients" class="quick-action-card">
            <div class="quick-action-content">
              <div class="quick-action-icon" style="background: #FAF5FF; color: #7E22CE;">
                <i data-lucide="users" style="width: 22px; height: 22px;"></i>
              </div>
              <div>
                <strong style="color: #0F172A; font-size: 13.5px; display: block; font-weight: 700;">Répertoire Clients</strong>
                <small style="color: #64748B;">Fiches & coordonnées</small>
              </div>
            </div>
            <i data-lucide="chevron-right" class="quick-action-arrow" style="width: 18px; height: 18px;"></i>
          </a>
          <?php endif; ?>

          <?php if ($canAccess(['FINANCE_MANAGE_DEPENSES'], ['ROLE_FINANCE', 'ROLE_COMPTABLE'])): ?>
          <a href="<?= RACINE ?>depense/list" class="quick-action-card">
            <div class="quick-action-content">
              <div class="quick-action-icon" style="background: #FEF2F2; color: #DC2626;">
                <i data-lucide="arrow-up-right" style="width: 22px; height: 22px;"></i>
              </div>
              <div>
                <strong style="color: #0F172A; font-size: 13.5px; display: block; font-weight: 700;">Dépenses Exploitation</strong>
                <small style="color: #64748B;">Charges & ordonnancement</small>
              </div>
            </div>
            <i data-lucide="chevron-right" class="quick-action-arrow" style="width: 18px; height: 18px;"></i>
          </a>
          <?php endif; ?>

          <?php if ($canAccess(['ADMIN_MANAGE_USERS'], ['ROLE_ADMIN', 'ROLE_SUPERADMIN'])): ?>
          <a href="<?= RACINE ?>user/list" class="quick-action-card">
            <div class="quick-action-content">
              <div class="quick-action-icon" style="background: #F1F5F9; color: #334155;">
                <i data-lucide="shield-check" style="width: 22px; height: 22px;"></i>
              </div>
              <div>
                <strong style="color: #0F172A; font-size: 13.5px; display: block; font-weight: 700;">Gestion RBAC & Users</strong>
                <small style="color: #64748B;">Comptes & permissions</small>
              </div>
            </div>
            <i data-lucide="chevron-right" class="quick-action-arrow" style="width: 18px; height: 18px;"></i>
          </a>
          <?php endif; ?>

        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- SECTION 3 : TABLEAUX RÉCAPITULATIFS RÉCENTS AUTORISÉS                     -->
      <!-- ========================================================================= -->
      
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 24px;">
        
        <!-- TABLEAU 1 : Dernières Ventes Avicoles POS -->
        <?php if ($canAccess(['COMMERCIAL_RECORD_VENTE_AVICOLE', 'FINANCE_VIEW_RAPPORTS_AVICULTURE'], ['ROLE_COMMERCIAL', 'ROLE_CAISSIER', 'ROLE_FINANCE', 'ROLE_DIR_GENERAL'])): ?>
        <div class="table-card">
          <div class="table-card-header">
            <h3 class="table-card-title">
              <i data-lucide="shopping-cart" style="width: 20px; height: 20px; color: #059669;"></i> Dernières Ventes Avicoles POS
            </h3>
            <a href="<?= RACINE ?>aviculture/ventes" style="font-size: 12px; font-weight: 700; color: #059669; text-decoration: none;">Voir tout &rarr;</a>
          </div>

          <div style="overflow-x: auto;">
            <table class="custom-table">
              <thead>
                <tr>
                  <th>Code Vente</th>
                  <th>Client / Type</th>
                  <th style="text-align: right;">Montant Net</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($recentVentesAvicoles)): ?>
                  <tr>
                    <td colspan="3" style="padding: 20px; text-align: center; color: #94A3B8;">Aucune vente enregistrée récemment</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($recentVentesAvicoles as $v): ?>
                    <tr>
                      <td style="font-weight: 700; color: #059669; font-family: monospace;">
                        <?= htmlspecialchars($v['code_vente'] ?? '-') ?>
                      </td>
                      <td style="font-weight: 600; color: #0F172A;">
                        <?= htmlspecialchars($v['client_nom'] ?? ($v['type_vente'] === 'commande_livraison' ? 'Commande Pro' : 'Client Comptoir')) ?>
                      </td>
                      <td style="text-align: right; font-weight: 800; color: #059669;">
                        <?= number_format((float)($v['montant_total_net'] ?? 0), 0, ',', ' ') ?> FCFA
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php endif; ?>

        <!-- TABLEAU 2 : Dernières Pesées & Étiquettes Net -->
        <?php if ($canAccess(['GESTIONNAIRE_RECORD_PESEE_ETIQUETTE', 'FINANCE_VALIDATE_PESEE_STOCK'], ['ROLE_GESTIONNAIRE', 'ROLE_DIR_GENERAL'])): ?>
        <div class="table-card">
          <div class="table-card-header">
            <h3 class="table-card-title">
              <i data-lucide="scale" style="width: 20px; height: 20px; color: #D97706;"></i> Dernières Pesées & Calibrages
            </h3>
            <a href="<?= RACINE ?>aviculture/pesees" style="font-size: 12px; font-weight: 700; color: #D97706; text-decoration: none;">Voir tout &rarr;</a>
          </div>

          <div style="overflow-x: auto;">
            <table class="custom-table">
              <thead>
                <tr>
                  <th>Code Pesée</th>
                  <th>Produit / Tranche</th>
                  <th style="text-align: right;">Poids Net</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($recentPeseesAvicoles)): ?>
                  <tr>
                    <td colspan="3" style="padding: 20px; text-align: center; color: #94A3B8;">Aucune pesée enregistrée récemment</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($recentPeseesAvicoles as $p): ?>
                    <tr>
                      <td style="font-weight: 700; color: #D97706; font-family: monospace;">
                        <?= htmlspecialchars($p['code_pesee'] ?? '-') ?>
                      </td>
                      <td style="font-weight: 600; color: #0F172A;">
                        <?= htmlspecialchars($p['designation_produit'] ?? 'Produit') ?> 
                        <small style="color: #64748B;">(<?= htmlspecialchars($p['libelle_categorie'] ?? '-') ?>)</small>
                      </td>
                      <td style="text-align: right; font-weight: 800; color: #D97706;">
                        <?= number_format((float)($p['poids_net'] ?? 0), 2, ',', ' ') ?> Kg
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php endif; ?>

        <!-- TABLEAU 3 : Derniers Achats & Approvisionnements Intrants -->
        <?php if ($canAccess(['GESTIONNAIRE_RECORD_ACHAT_AVICOLE', 'LOGISTIQUE_VERIFIER_BON_ACHAT'], ['ROLE_GESTIONNAIRE', 'ROLE_FINANCE', 'ROLE_DIR_GENERAL'])): ?>
        <div class="table-card">
          <div class="table-card-header">
            <h3 class="table-card-title">
              <i data-lucide="truck" style="width: 20px; height: 20px; color: #2563EB;"></i> Derniers Achats Intrants
            </h3>
            <a href="<?= RACINE ?>aviculture/achats" style="font-size: 12px; font-weight: 700; color: #2563EB; text-decoration: none;">Voir tout &rarr;</a>
          </div>

          <div style="overflow-x: auto;">
            <table class="custom-table">
              <thead>
                <tr>
                  <th>Code Achat</th>
                  <th>Fournisseur</th>
                  <th style="text-align: right;">Montant Total</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($recentAchatsAvicoles)): ?>
                  <tr>
                    <td colspan="3" style="padding: 20px; text-align: center; color: #94A3B8;">Aucun achat enregistré récemment</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($recentAchatsAvicoles as $a): ?>
                    <tr>
                      <td style="font-weight: 700; color: #2563EB; font-family: monospace;">
                        <?= htmlspecialchars($a['code_achat'] ?? '-') ?>
                      </td>
                      <td style="font-weight: 600; color: #0F172A;">
                        <?= htmlspecialchars($a['nom_fournisseur'] ?? 'Fournisseur') ?>
                      </td>
                      <td style="text-align: right; font-weight: 800; color: #2563EB;">
                        <?= number_format((float)($a['montant_total'] ?? 0), 0, ',', ' ') ?> FCFA
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php endif; ?>

        <!-- TABLEAU 4 : Dernières Dépenses d'Exploitation -->
        <?php if ($canAccess(['FINANCE_MANAGE_DEPENSES'], ['ROLE_FINANCE', 'ROLE_COMPTABLE', 'ROLE_DIR_GENERAL'])): ?>
        <div class="table-card">
          <div class="table-card-header">
            <h3 class="table-card-title">
              <i data-lucide="arrow-up-right" style="width: 20px; height: 20px; color: #DC2626;"></i> Dernières Dépenses Engagées
            </h3>
            <a href="<?= RACINE ?>depense/list" style="font-size: 12px; font-weight: 700; color: #DC2626; text-decoration: none;">Voir tout &rarr;</a>
          </div>

          <div style="overflow-x: auto;">
            <table class="custom-table">
              <thead>
                <tr>
                  <th>Code Dépense</th>
                  <th>Type / Libellé</th>
                  <th style="text-align: right;">Montant</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($recentDepenses)): ?>
                  <tr>
                    <td colspan="3" style="padding: 20px; text-align: center; color: #94A3B8;">Aucune dépense enregistrée récemment</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($recentDepenses as $dep): ?>
                    <tr>
                      <td style="font-weight: 700; color: #DC2626; font-family: monospace;">
                        <?= htmlspecialchars($dep['code_depense'] ?? '-') ?>
                      </td>
                      <td style="font-weight: 600; color: #0F172A;">
                        <?= htmlspecialchars($dep['libelle_type_depense'] ?? ($dep['description_depense'] ?? 'Charge générale')) ?>
                      </td>
                      <td style="text-align: right; font-weight: 800; color: #DC2626;">
                        <?= number_format((float)($dep['montant_depense'] ?? 0), 0, ',', ' ') ?> FCFA
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php endif; ?>

      </div>

    </div>
  </main>
</div>

<script>
$(document).ready(function() {
  if (window.lucide) {
    lucide.createIcons();
  }
});
</script>

<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
