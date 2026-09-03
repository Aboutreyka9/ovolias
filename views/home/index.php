<?php
require_once __DIR__ . '/../../public/inc/header.php';

$stats = $stats ?? [];
$auth = $auth ?? ($_SESSION[USERS_AUTH] ?? []);
$recentCotisations = $recentCotisations ?? [];
$recentVersements = $recentVersements ?? [];
$recentDepenses = $recentDepenses ?? [];
?>

<style>
/* ==========================================================================
   DESIGN SYSTEM & ANIMATIONS ULTRA-PREMIUM - OLIVE SERVICE DASHBOARD
   ========================================================================== */

/* Keyframe Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulseGlow {
  0% {
    box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.2);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(37, 99, 235, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(37, 99, 235, 0);
  }
}

@keyframes floatIcon {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-3px); }
}

/* Base Layout & Wrapper */
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
  background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
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
  color: #38BDF8;
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

/* Dynamic Animated KPI Card */
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

/* Stagger Animation Load */
.kpi-card:nth-child(1) { animation: fadeInUp 0.3s ease-out 0.05s backwards; }
.kpi-card:nth-child(2) { animation: fadeInUp 0.3s ease-out 0.10s backwards; }
.kpi-card:nth-child(3) { animation: fadeInUp 0.3s ease-out 0.15s backwards; }
.kpi-card:nth-child(4) { animation: fadeInUp 0.3s ease-out 0.20s backwards; }
.kpi-card:nth-child(5) { animation: fadeInUp 0.3s ease-out 0.25s backwards; }
.kpi-card:nth-child(6) { animation: fadeInUp 0.3s ease-out 0.30s backwards; }
.kpi-card:nth-child(7) { animation: fadeInUp 0.3s ease-out 0.35s backwards; }
.kpi-card:nth-child(8) { animation: fadeInUp 0.3s ease-out 0.40s backwards; }

.kpi-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--kpi-accent, #2563EB);
  border-radius: 16px 16px 0 0;
  transition: height 0.25s ease;
}

.kpi-card:hover {
  transform: translateY(-6px) scale(1.015);
  box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.03);
  border-color: rgba(37, 99, 235, 0.3);
}

.kpi-card:hover::before {
  height: 6px;
}

.kpi-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 14px;
}

.kpi-title {
  font-size: 12px;
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
  background: var(--kpi-bg-icon, #EFF6FF);
  color: var(--kpi-color-icon, #2563EB);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.kpi-card:hover .kpi-icon-wrapper {
  transform: scale(1.15) rotate(6deg);
}

.kpi-value {
  font-size: 26px;
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
      <!-- BANNER D'EN-TÊTE ULTRA-MODERNE ANIMÉE                                      -->
      <!-- ========================================================================= -->
      <div class="dashboard-header-card">
        <div class="header-title-box">
          <div class="header-icon-badge">
            <i data-lucide="layout-dashboard" style="width: 28px; height: 28px;"></i>
          </div>
          <div>
            <h1 style="font-size: 24px; font-weight: 800; margin: 0; letter-spacing: -0.5px;">
              Tableau de Bord &bull; Olive Service
            </h1>
            <p style="margin: 4px 0 0 0; font-size: 13px; color: #94A3B8; font-weight: 500;">
              Vue synthétique et pilotage financier en temps réel. Bienvenue, <strong style="color: #FFFFFF;"><?= htmlspecialchars($auth['nom_user'] ?? 'Utilisateur') ?></strong>
            </p>
          </div>
        </div>

        <!-- ACTIONS RAPIDES BOUTONS PILULE -->
        <div class="header-actions-group">
          <a href="<?= RACINE ?>souscription/formulaire" class="action-btn-pill btn-pill-primary">
            <i data-lucide="plus-circle" style="width: 17px; height: 17px;"></i> Nouvelle Souscription
          </a>
          <a href="<?= RACINE ?>versement/formulaire" class="action-btn-pill btn-pill-success">
            <i data-lucide="arrow-down-left" style="width: 17px; height: 17px;"></i> Versement Commercial
          </a>
          <a href="<?= RACINE ?>depense/formulaire" class="action-btn-pill btn-pill-danger">
            <i data-lucide="arrow-up-right" style="width: 17px; height: 17px;"></i> Saisir Dépense
          </a>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- SECTION 1 : GRILLE KPI ANIMÉE AVEC ACCENTS COLORÉS DYNAMIQUES              -->
      <!-- ========================================================================= -->
      
      <div class="kpi-grid">
        
        <!-- KPI 1 : Clients -->
        <div class="kpi-card" style="--kpi-accent: linear-gradient(90deg, #3B82F6, #1D4ED8); --kpi-bg-icon: #EFF6FF; --kpi-color-icon: #2563EB;">
          <div class="kpi-header">
            <span class="kpi-title">Clients Enregistrés</span>
            <div class="kpi-icon-wrapper">
              <i data-lucide="users" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          <div class="kpi-value">
            <?= number_format($stats['total_clients'] ?? 0, 0, ',', ' ') ?>
          </div>
          <div class="kpi-footer">
            <span>Portfolio active</span>
            <span class="kpi-tag" style="background: #EFF6FF; color: #1D4ED8;">
              <i data-lucide="file-check" style="width: 12px; height: 12px;"></i> <?= (int)($stats['total_souscriptions'] ?? 0) ?> souscr.
            </span>
          </div>
        </div>

        <!-- KPI 2 : Packs & Articles -->
        <div class="kpi-card" style="--kpi-accent: linear-gradient(90deg, #8B5CF6, #6D28D9); --kpi-bg-icon: #FAF5FF; --kpi-color-icon: #7E22CE;">
          <div class="kpi-header">
            <span class="kpi-title">Packs & Offres</span>
            <div class="kpi-icon-wrapper">
              <i data-lucide="package" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          <div class="kpi-value" style="color: #7E22CE;">
            <?= number_format($stats['total_packs'] ?? 0, 0, ',', ' ') ?> <span style="font-size: 14px; font-weight: 600;">Packs</span>
          </div>
          <div class="kpi-footer">
            <span>Articles catalogue</span>
            <span class="kpi-tag" style="background: #FAF5FF; color: #7E22CE;">
              <i data-lucide="boxes" style="width: 12px; height: 12px;"></i> <?= (int)($stats['total_articles'] ?? 0) ?> références
            </span>
          </div>
        </div>

        <!-- KPI 3 : Cotisations Encaissées -->
        <div class="kpi-card" style="--kpi-accent: linear-gradient(90deg, #10B981, #047857); --kpi-bg-icon: #ECFDF5; --kpi-color-icon: #047857;">
          <div class="kpi-header">
            <span class="kpi-title">Cotisations Terrain</span>
            <div class="kpi-icon-wrapper">
              <i data-lucide="coins" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          <div class="kpi-value" style="color: #047857;">
            <?= number_format($stats['total_cotisations'] ?? 0, 0, ',', ' ') ?> <span style="font-size: 13px; font-weight: 600;">FCFA</span>
          </div>
          <div class="kpi-footer">
            <span>Encaissement clients</span>
            <span class="kpi-tag" style="background: #ECFDF5; color: #047857;">
              <i data-lucide="trending-up" style="width: 12px; height: 12px;"></i> En direct
            </span>
          </div>
        </div>

        <!-- KPI 4 : Versements Validés -->
        <div class="kpi-card" style="--kpi-accent: linear-gradient(90deg, #059669, #065F46); --kpi-bg-icon: #F0FDF4; --kpi-color-icon: #16A34A;">
          <div class="kpi-header">
            <span class="kpi-title">Versements Validés</span>
            <div class="kpi-icon-wrapper">
              <i data-lucide="check-circle-2" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          <div class="kpi-value" style="color: #16A34A;">
            <?= number_format($stats['total_versements'] ?? 0, 0, ',', ' ') ?> <span style="font-size: 13px; font-weight: 600;">FCFA</span>
          </div>
          <div class="kpi-footer">
            <?php if (!empty($stats['total_versements_en_attente'])): ?>
              <span style="color: #D97706; font-weight: 700;">
                <i data-lucide="clock" style="width: 12px; height: 12px;"></i> <?= number_format($stats['total_versements_en_attente'], 0, ',', ' ') ?> en attente
              </span>
            <?php else: ?>
              <span>Versements caisse</span>
              <span class="kpi-tag" style="background: #F0FDF4; color: #16A34A;">100% à jour</span>
            <?php endif; ?>
          </div>
        </div>

        <!-- KPI 5 : Souscriptions Soldées -->
        <div class="kpi-card" style="--kpi-accent: linear-gradient(90deg, #14B8A6, #0F766E); --kpi-bg-icon: #F0FDFA; --kpi-color-icon: #0D9488;">
          <div class="kpi-header">
            <span class="kpi-title">Contrats Soldés</span>
            <div class="kpi-icon-wrapper">
              <i data-lucide="circle-check-big" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          <div class="kpi-value" style="color: #0D9488;">
            <?= number_format($stats['total_souscriptions_soldees'] ?? 0, 0, ',', ' ') ?> <span style="font-size: 14px; color: #64748B; font-weight: 600;">/ <?= number_format($stats['total_souscriptions'] ?? 0, 0, ',', ' ') ?></span>
          </div>
          <div class="kpi-footer">
            <span>Prêts pour distribution</span>
            <span class="kpi-tag" style="background: #F0FDFA; color: #0D9488;">
              <i data-lucide="sparkles" style="width: 12px; height: 12px;"></i> Finalisés
            </span>
          </div>
        </div>

        <!-- KPI 6 : Distributions -->
        <div class="kpi-card" style="--kpi-accent: linear-gradient(90deg, #EC4899, #BE185D); --kpi-bg-icon: #FDF2F8; --kpi-color-icon: #DB2777;">
          <div class="kpi-header">
            <span class="kpi-title">Distributions Packs</span>
            <div class="kpi-icon-wrapper">
              <i data-lucide="package-check" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          <div class="kpi-value" style="color: #DB2777;">
            <?= number_format($stats['total_distributions_validees'] ?? 0, 0, ',', ' ') ?> <span style="font-size: 14px; color: #64748B; font-weight: 600;">/ <?= number_format($stats['total_distributions'] ?? 0, 0, ',', ' ') ?></span>
          </div>
          <div class="kpi-footer">
            <span>Livraisons effectuées</span>
            <span class="kpi-tag" style="background: #FDF2F8; color: #DB2777;">
              <i data-lucide="truck" style="width: 12px; height: 12px;"></i> Remises
            </span>
          </div>
        </div>

        <!-- KPI 7 : Dépenses Engagées -->
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
            <span>Charges d'exploitation</span>
            <span class="kpi-tag" style="background: #FEF2F2; color: #DC2626;">
              <i data-lucide="trending-down" style="width: 12px; height: 12px;"></i> Sorties
            </span>
          </div>
        </div>

        <!-- KPI 8 : Solde Net -->
        <div class="kpi-card" style="--kpi-accent: linear-gradient(90deg, #6366F1, #4338CA); --kpi-bg-icon: #EEF2FF; --kpi-color-icon: #4F46E5;">
          <div class="kpi-header">
            <span class="kpi-title">Solde Nette Trésorerie</span>
            <div class="kpi-icon-wrapper">
              <i data-lucide="scale" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          <div class="kpi-value" style="color: <?= ($stats['solde_net'] ?? 0) >= 0 ? '#15803D' : '#DC2626' ?>;">
            <?= number_format($stats['solde_net'] ?? 0, 0, ',', ' ') ?> <span style="font-size: 13px; font-weight: 600;">FCFA</span>
          </div>
          <div class="kpi-footer">
            <span>Recettes &minus; Décaissements</span>
            <span class="kpi-tag" style="background: #EEF2FF; color: #4F46E5;">
              <i data-lucide="wallet" style="width: 12px; height: 12px;"></i> Bilan
            </span>
          </div>
        </div>

      </div>

      <!-- ========================================================================= -->
      <!-- SECTION 2 : RACCOURCIS D'ACCÈS RAPIDES INTERACTIFS                         -->
      <!-- ========================================================================= -->
      <div style="margin-bottom: 32px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0 0 16px 0; display: flex; align-items: center; gap: 10px;">
          <i data-lucide="zap" style="color: #2563EB; width: 20px; height: 20px;"></i> Raccourcis d'Accès Rapide
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
          
          <a href="<?= RACINE ?>client/list" class="quick-action-card">
            <div class="quick-action-content">
              <div class="quick-action-icon" style="background: #EFF6FF; color: #2563EB;">
                <i data-lucide="users" style="width: 22px; height: 22px;"></i>
              </div>
              <div>
                <strong style="color: #0F172A; font-size: 14px; display: block; font-weight: 700;">Gestion Clients</strong>
                <small style="color: #64748B;">Répertoire & souscriptions</small>
              </div>
            </div>
            <i data-lucide="chevron-right" class="quick-action-arrow" style="width: 18px; height: 18px;"></i>
          </a>

          <a href="<?= RACINE ?>souscription/list" class="quick-action-card">
            <div class="quick-action-content">
              <div class="quick-action-icon" style="background: #ECFDF5; color: #047857;">
                <i data-lucide="file-text" style="width: 22px; height: 22px;"></i>
              </div>
              <div>
                <strong style="color: #0F172A; font-size: 14px; display: block; font-weight: 700;">Souscriptions</strong>
                <small style="color: #64748B;">Suivi des engagements</small>
              </div>
            </div>
            <i data-lucide="chevron-right" class="quick-action-arrow" style="width: 18px; height: 18px;"></i>
          </a>

          <a href="<?= RACINE ?>cotisation/list" class="quick-action-card">
            <div class="quick-action-content">
              <div class="quick-action-icon" style="background: #FFFBEB; color: #D97706;">
                <i data-lucide="coins" style="width: 22px; height: 22px;"></i>
              </div>
              <div>
                <strong style="color: #0F172A; font-size: 14px; display: block; font-weight: 700;">Cotisations Terrain</strong>
                <small style="color: #64748B;">Paiements quotidiens</small>
              </div>
            </div>
            <i data-lucide="chevron-right" class="quick-action-arrow" style="width: 18px; height: 18px;"></i>
          </a>

          <a href="<?= RACINE ?>versement/list" class="quick-action-card">
            <div class="quick-action-content">
              <div class="quick-action-icon" style="background: #F0FDF4; color: #16A34A;">
                <i data-lucide="arrow-down-left" style="width: 22px; height: 22px;"></i>
              </div>
              <div>
                <strong style="color: #0F172A; font-size: 14px; display: block; font-weight: 700;">Versements</strong>
                <small style="color: #64748B;">Validations de caisse</small>
              </div>
            </div>
            <i data-lucide="chevron-right" class="quick-action-arrow" style="width: 18px; height: 18px;"></i>
          </a>

          <a href="<?= RACINE ?>depense/list" class="quick-action-card">
            <div class="quick-action-content">
              <div class="quick-action-icon" style="background: #FEF2F2; color: #DC2626;">
                <i data-lucide="arrow-up-right" style="width: 22px; height: 22px;"></i>
              </div>
              <div>
                <strong style="color: #0F172A; font-size: 14px; display: block; font-weight: 700;">Dépenses</strong>
                <small style="color: #64748B;">Charges & décaissements</small>
              </div>
            </div>
            <i data-lucide="chevron-right" class="quick-action-arrow" style="width: 18px; height: 18px;"></i>
          </a>

          <a href="<?= RACINE ?>distribution/list" class="quick-action-card">
            <div class="quick-action-content">
              <div class="quick-action-icon" style="background: #FAF5FF; color: #7E22CE;">
                <i data-lucide="truck" style="width: 22px; height: 22px;"></i>
              </div>
              <div>
                <strong style="color: #0F172A; font-size: 14px; display: block; font-weight: 700;">Distributions</strong>
                <small style="color: #64748B;">Remises des packs clients</small>
              </div>
            </div>
            <i data-lucide="chevron-right" class="quick-action-arrow" style="width: 18px; height: 18px;"></i>
          </a>

        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- SECTION 3 : TABLEAUX RÉCAPITULATIFS RÉCENTS                               -->
      <!-- ========================================================================= -->
      
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 24px;">
        
        <!-- TABLEAU 1 : Dernières Cotisations Clients -->
        <div class="table-card">
          <div class="table-card-header">
            <h3 class="table-card-title">
              <i data-lucide="coins" style="width: 20px; height: 20px; color: #D97706;"></i> Dernières Cotisations Clients
            </h3>
            <a href="<?= RACINE ?>cotisation/list" style="font-size: 12px; font-weight: 700; color: #D97706; text-decoration: none;">Voir tout &rarr;</a>
          </div>

          <div style="overflow-x: auto;">
            <table class="custom-table">
              <thead>
                <tr>
                  <th>Code Cotisation</th>
                  <th>Client</th>
                  <th style="text-align: right;">Montant</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($recentCotisations)): ?>
                  <tr>
                    <td colspan="3" style="padding: 20px; text-align: center; color: #94A3B8;">Aucune cotisation récente enregistrée</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($recentCotisations as $c): ?>
                    <tr>
                      <td style="font-weight: 700; color: #D97706; font-family: monospace;">
                        <?= htmlspecialchars($c['code_cautisation_client'] ?? '-') ?>
                      </td>
                      <td style="font-weight: 600; color: #0F172A;"><?= htmlspecialchars($c['nom_client'] ?? '-') ?></td>
                      <td style="text-align: right; font-weight: 800; color: #059669;"><?= number_format((float)($c['montant_cautisation_client'] ?? 0), 0, ',', ' ') ?> FCFA</td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- TABLEAU 2 : Derniers Versements Commerciaux -->
        <div class="table-card">
          <div class="table-card-header">
            <h3 class="table-card-title">
              <i data-lucide="arrow-down-left" style="width: 20px; height: 20px; color: #059669;"></i> Derniers Versements Commerciaux
            </h3>
            <a href="<?= RACINE ?>versement/list" style="font-size: 12px; font-weight: 700; color: #059669; text-decoration: none;">Voir tout &rarr;</a>
          </div>

          <div style="overflow-x: auto;">
            <table class="custom-table">
              <thead>
                <tr>
                  <th>Commercial</th>
                  <th>Zone</th>
                  <th style="text-align: right;">Montant</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($recentVersements)): ?>
                  <tr>
                    <td colspan="3" style="padding: 20px; text-align: center; color: #94A3B8;">Aucun versement commercial enregistré</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($recentVersements as $v): ?>
                    <tr>
                      <td style="font-weight: 600; color: #0F172A;">
                        <?= htmlspecialchars(trim(($v['nom_commercial'] ?? '') . ' ' . ($v['prenom_commercial'] ?? ''))) ?>
                      </td>
                      <td style="color: #64748B;"><?= htmlspecialchars($v['libelle_zone'] ?? 'Non spécifiée') ?></td>
                      <td style="text-align: right; font-weight: 800; color: #047857;"><?= number_format((float)($v['montant_versement'] ?? 0), 0, ',', ' ') ?> FCFA</td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- TABLEAU 3 : Dernières Dépenses Engagées -->
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
                  <th>Type / Motif</th>
                  <th style="text-align: right;">Montant</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($recentDepenses)): ?>
                  <tr>
                    <td colspan="3" style="padding: 20px; text-align: center; color: #94A3B8;">Aucune dépense enregistrée</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($recentDepenses as $dep): ?>
                    <tr>
                      <td style="font-weight: 700; color: #DC2626; font-family: monospace;">
                        <?= htmlspecialchars($dep['code_depense'] ?? '-') ?>
                      </td>
                      <td style="font-weight: 600; color: #0F172A;"><?= htmlspecialchars($dep['libelle_type_depense'] ?? ($dep['description_depense'] ?? 'Charge générale')) ?></td>
                      <td style="text-align: right; font-weight: 800; color: #DC2626;"><?= number_format((float)($dep['montant_depense'] ?? 0), 0, ',', ' ') ?> FCFA</td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

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
