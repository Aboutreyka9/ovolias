<?php
require_once __DIR__ . '/../../public/inc/header.php';

$stats = $stats ?? [];
$missions = $missions ?? [];
$livreurName = $stats['nom_livreur'] ?? ($currentUserName ?? 'Livreur');
$totalMissions = $stats['total_missions'] ?? 0;
$enAttente = $stats['missions_en_attente'] ?? 0;
$enCours = $stats['missions_en_cours'] ?? 0;
$termineesAuj = $stats['missions_terminees_aujourdhui'] ?? 0;
$totalSacs = $stats['total_sacs_transportes'] ?? 0;

// Identifier la prochaine mission prioritaire (première 'en_cours', sinon première 'en_attente')
$nextMission = null;
foreach ($missions as $m) {
    if ($m['statut_mission'] === 'en_cours') {
        $nextMission = $m;
        break;
    }
}
if (!$nextMission) {
    foreach ($missions as $m) {
        if ($m['statut_mission'] === 'en_attente') {
            $nextMission = $m;
            break;
        }
    }
}
?>

<style>
/* === MOBILE PWA UX OPTIMIZATIONS FOR LIVREURS === */
@media (max-width: 768px) {
  .content-wrapper {
    padding: 12px 10px 80px 10px !important;
  }
  .page-header {
    margin-bottom: 16px !important;
    flex-direction: column !important;
    align-items: stretch !important;
  }
  .page-header-actions {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 8px !important;
    width: 100% !important;
  }
  .page-header-actions .btn {
    justify-content: center !important;
    padding: 10px 8px !important;
    font-size: 13px !important;
    height: 44px !important;
  }
  .stats-grid {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 10px !important;
    margin-bottom: 18px !important;
  }
  .stats-card {
    padding: 12px 10px !important;
    gap: 10px !important;
  }
  .stats-card-icon {
    width: 38px !important;
    height: 38px !important;
    font-size: 18px !important;
    flex-shrink: 0 !important;
  }
  .stats-card-val {
    font-size: 20px !important;
  }
  .next-mission-card {
    padding: 16px !important;
    border-radius: 14px !important;
    margin-bottom: 20px !important;
  }
  .next-mission-header-actions {
    width: 100% !important;
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 8px !important;
    margin-top: 10px !important;
  }
  .next-mission-header-actions .btn {
    width: 100% !important;
    justify-content: center !important;
    height: 46px !important;
    font-size: 13px !important;
  }
  .next-mission-actions-bar {
    flex-direction: column !important;
    align-items: stretch !important;
  }
  .next-mission-btn-group {
    width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
  }
  .next-mission-btn-group .btn {
    width: 100% !important;
    height: 48px !important;
    font-size: 14px !important;
    justify-content: center !important;
  }
  .mission-item-card {
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 12px !important;
  }
  .mission-item-actions {
    width: 100% !important;
    display: grid !important;
    grid-template-columns: auto 1fr auto !important;
    gap: 8px !important;
  }
  .mission-item-actions .btn {
    height: 42px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
  }
  
  /* BARRE DE NAVIGATION INFÉRIEURE NATIVE PWA MOBILE */
  .mobile-driver-bottom-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 64px;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-top: 1px solid #E2E8F0;
    display: flex;
    justify-content: space-around;
    align-items: center;
    z-index: 99999;
    box-shadow: 0 -4px 16px rgba(0,0,0,0.06);
  }
  .mobile-driver-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: #64748B;
    font-size: 11px;
    font-weight: 600;
    gap: 3px;
    flex: 1;
    height: 100%;
    transition: all 0.2s ease;
  }
  .mobile-driver-nav-item.active {
    color: #2563EB;
    font-weight: 800;
  }
  .mobile-driver-nav-item.active i {
    transform: scale(1.1);
  }
}
@media (min-width: 769px) {
  .mobile-driver-bottom-bar {
    display: none !important;
  }
}
</style>

<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>

    <div class="content-wrapper">
      
      <!-- === EN-TÊTE BIENVENUE LIVREUR === -->
      <div class="page-header" style="margin-bottom: 22px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        <div>
          <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 42px; height: 42px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
              <i class="fa fa-motorcycle"></i>
            </div>
            <div>
              <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #1E293B;">
                Bonjour, <?= htmlspecialchars($livreurName) ?> !
              </h1>
              <p class="page-subtitle" style="margin: 2px 0 0; color: #64748B; font-size: 13px;">
                Espace Logistique & Tournées • <?= date('d/m/Y') ?>
              </p>
            </div>
          </div>
        </div>

        <div class="page-header-actions" style="display: flex; gap: 10px;">
          <a href="<?= RACINE ?>mission/carte" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 10px;">
            <i data-lucide="map" style="width: 16px; height: 16px;"></i> Carte GPS
          </a>
          <a href="<?= RACINE ?>mission/list" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 10px;">
            <i data-lucide="clipboard-list" style="width: 16px; height: 16px;"></i> Mes missions
          </a>
        </div>
      </div>

      <!-- === 4 CARTES KPI DU LIVREUR === -->
      <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
        
        <!-- 1. Missions en attente -->
        <div class="card stats-card" style="padding: 18px; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF; display: flex; align-items: center; gap: 16px;">
          <div class="stats-card-icon" style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa fa-clock"></i>
          </div>
          <div>
            <span style="font-size: 12px; font-weight: 600; color: #64748B; display: block; text-transform: uppercase;">À démarrer</span>
            <strong class="stats-card-val" style="font-size: 24px; font-weight: 800; color: #D97706; line-height: 1.2;"><?= $enAttente ?></strong>
            <small style="color: #94A3B8; font-size: 11px; display: block;">Missions en attente</small>
          </div>
        </div>

        <!-- 2. Courses en cours -->
        <div class="card stats-card" style="padding: 18px; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF; display: flex; align-items: center; gap: 16px;">
          <div class="stats-card-icon" style="width: 48px; height: 48px; border-radius: 12px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa fa-route"></i>
          </div>
          <div>
            <span style="font-size: 12px; font-weight: 600; color: #64748B; display: block; text-transform: uppercase;">En cours</span>
            <strong class="stats-card-val" style="font-size: 24px; font-weight: 800; color: #2563EB; line-height: 1.2;"><?= $enCours ?></strong>
            <small style="color: #94A3B8; font-size: 11px; display: block;">Sur la route</small>
          </div>
        </div>

        <!-- 3. Terminées aujourd'hui -->
        <div class="card stats-card" style="padding: 18px; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF; display: flex; align-items: center; gap: 16px;">
          <div class="stats-card-icon" style="width: 48px; height: 48px; border-radius: 12px; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa fa-check-circle"></i>
          </div>
          <div>
            <span style="font-size: 12px; font-weight: 600; color: #64748B; display: block; text-transform: uppercase;">Livrées aujourd'hui</span>
            <strong class="stats-card-val" style="font-size: 24px; font-weight: 800; color: #059669; line-height: 1.2;"><?= $termineesAuj ?></strong>
            <small style="color: #94A3B8; font-size: 11px; display: block;">Succès du jour</small>
          </div>
        </div>

        <!-- 4. Sacs transportés -->
        <div class="card stats-card" style="padding: 18px; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF; display: flex; align-items: center; gap: 16px;">
          <div class="stats-card-icon" style="width: 48px; height: 48px; border-radius: 12px; background: #F3E8FF; color: #7C3AED; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa fa-box-open"></i>
          </div>
          <div>
            <span style="font-size: 12px; font-weight: 600; color: #64748B; display: block; text-transform: uppercase;">Colis / Sacs</span>
            <strong class="stats-card-val" style="font-size: 24px; font-weight: 800; color: #7C3AED; line-height: 1.2;"><?= $totalSacs ?></strong>
            <small style="color: #94A3B8; font-size: 11px; display: block;">Volume confié</small>
          </div>
        </div>

      </div>

      <!-- === CARTE MISE EN AVANT : PROCHAINE COURSE PRIORITAIRE === -->
      <?php if ($nextMission): 
        $isNextColis = ($nextMission['type_mission'] === 'collecte');
        $targetAdresse = !empty($nextMission['adresse_mission']) ? $nextMission['adresse_mission'] : ($nextMission['adresse_client'] ?? 'Abidjan');
        $gpsLat = !empty($nextMission['latitude_mission']) ? $nextMission['latitude_mission'] : ($nextMission['latitude_client'] ?? '');
        $gpsLng = !empty($nextMission['longitude_mission']) ? $nextMission['longitude_mission'] : ($nextMission['longitude_client'] ?? '');
        
        $mapsUrl = ($gpsLat && $gpsLng) 
            ? "https://www.google.com/maps/dir/?api=1&destination={$gpsLat},{$gpsLng}"
            : "https://www.google.com/maps/search/?api=1&query=" . urlencode($targetAdresse . ' Abidjan');
      ?>
      <div class="card next-mission-card" style="border: 1px solid <?= $isNextColis ? '#F59E0B' : '#2563EB' ?>; border-left: 6px solid <?= $isNextColis ? '#D97706' : '#2563EB' ?>; border-radius: 16px; padding: 22px; margin-bottom: 26px; background: #FFFFFF; box-shadow: 0 4px 14px rgba(0,0,0,0.04);">
        
        <!-- En-tête de carte -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 18px; border-bottom: 1px solid #F1F5F9; padding-bottom: 14px;">
          <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <span style="background: <?= $isNextColis ? '#FEF3C7' : '#EFF6FF' ?>; color: <?= $isNextColis ? '#92400E' : '#1E40AF' ?>; padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
              <i data-lucide="<?= $isNextColis ? 'package' : 'truck' ?>" style="width: 15px; height: 15px;"></i>
              <?= $isNextColis ? 'Collecte de linge' : 'Livraison au client' ?>
            </span>
            <span style="background: #F1F5F9; color: #475569; padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
              <i data-lucide="tag" style="width: 14px; height: 14px;"></i> Mission #<?= htmlspecialchars($nextMission['code_mission']) ?>
            </span>
            <span style="color: #64748B; font-size: 13px; font-weight: 600;">
              Commande #<?= htmlspecialchars($nextMission['commande_code']) ?>
            </span>
          </div>

          <div class="next-mission-header-actions" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <?php if (!empty($nextMission['telephone_client'])): ?>
              <a href="tel:<?= htmlspecialchars($nextMission['telephone_client']) ?>" class="btn btn-sm btn-secondary" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700; border-radius: 8px; padding: 10px 14px; color: #059669; border-color: #A7F3D0; background: #ECFDF5;">
                <i data-lucide="phone" style="width: 16px; height: 16px;"></i> Appeler client
              </a>
            <?php endif; ?>
            <a href="<?= RACINE ?>mission/carte?mission=<?= urlencode($nextMission['code_mission']) ?>" class="btn btn-sm btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 6px; font-weight: 700; border-radius: 8px; padding: 10px 14px;">
              <i data-lucide="navigation" style="width: 16px; height: 16px;"></i> Trajet & GPS Live
            </a>
          </div>
        </div>

        <!-- Informations Logistiques séparées proprement -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 20px;">
          
          <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 14px; border-radius: 12px;">
            <span style="font-size: 11px; font-weight: 700; color: #64748B; display: flex; align-items: center; gap: 6px; text-transform: uppercase; margin-bottom: 6px;">
              <i data-lucide="user" style="width: 14px; height: 14px; color: #2563EB;"></i> Client & Contact
            </span>
            <strong style="font-size: 15px; color: #1E293B; display: block; margin-bottom: 2px;"><?= htmlspecialchars($nextMission['nom_client'] ?? 'Client') ?></strong>
            <span style="color: #475569; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
              <i data-lucide="phone-call" style="width: 13px; height: 13px; color: #059669;"></i> <?= htmlspecialchars($nextMission['telephone_client'] ?? 'Non renseigné') ?>
            </span>
          </div>

          <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 14px; border-radius: 12px;">
            <span style="font-size: 11px; font-weight: 700; color: #64748B; display: flex; align-items: center; gap: 6px; text-transform: uppercase; margin-bottom: 6px;">
              <i data-lucide="map-pin" style="width: 14px; height: 14px; color: #DC2626;"></i> Destination / Adresse
            </span>
            <strong style="font-size: 14px; color: #1E293B; display: block; margin-bottom: 2px;"><?= htmlspecialchars($targetAdresse) ?></strong>
            <span style="color: #64748B; font-size: 12px;">Quartier : <strong><?= htmlspecialchars($nextMission['quartier_client'] ?? 'Abidjan') ?></strong></span>
          </div>

          <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 14px; border-radius: 12px;">
            <span style="font-size: 11px; font-weight: 700; color: #64748B; display: flex; align-items: center; gap: 6px; text-transform: uppercase; margin-bottom: 6px;">
              <i data-lucide="store" style="width: 14px; height: 14px; color: #D97706;"></i> Pressing Partenaire
            </span>
            <strong style="font-size: 14px; color: #1E293B; display: block; margin-bottom: 2px;"><?= htmlspecialchars($nextMission['libelle_pressing'] ?? 'Pressing') ?></strong>
            <span style="color: #64748B; font-size: 12px;"><?= htmlspecialchars($nextMission['adresse_pressing'] ?? 'Abidjan') ?></span>
          </div>

        </div>

        <!-- ACTIONS IMMÉDIATES 1-CLIC POUR LE LIVREUR -->
        <div class="next-mission-actions-bar" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; background: #F1F5F9; padding: 14px 16px; border-radius: 12px;">
          <div style="font-size: 13px; color: #475569; display: flex; align-items: center; gap: 8px;">
            <span style="font-weight: 600;">Statut de la course :</span>
            <span class="badge-status <?= $nextMission['statut_mission'] === 'en_cours' ? 'badge-status-progress' : '' ?>" style="text-transform: uppercase; font-weight: 700; font-size: 11px;">
              <?= str_replace('_', ' ', htmlspecialchars($nextMission['statut_mission'])) ?>
            </span>
          </div>

          <div class="next-mission-btn-group" style="display: flex; gap: 8px; flex-wrap: wrap;">
            <?php if ($isNextColis): ?>
              <?php if ($nextMission['statut_mission'] === 'en_attente'): ?>
                <button type="button" class="btn btn-primary" onclick="triggerLivreurAction('enRouteCollecte', '<?= $nextMission['code_mission'] ?>')" style="background: #D97706; border-color: #D97706; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; border-radius: 10px;">
                  <i data-lucide="play" style="width: 16px; height: 16px;"></i> Démarrer la collecte
                </button>
              <?php elseif ($nextMission['statut_mission'] === 'en_cours'): ?>
                <button type="button" class="btn btn-primary" onclick="triggerLivreurAction('lingeCollecte', '<?= $nextMission['code_mission'] ?>')" style="background: #2563EB; border-color: #2563EB; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; border-radius: 10px;">
                  <i data-lucide="package-check" style="width: 16px; height: 16px;"></i> Confirmer linge collecté
                </button>
                <button type="button" class="btn btn-success" onclick="triggerLivreurAction('deposeAuPressing', '<?= $nextMission['code_mission'] ?>')" style="background: #059669; border-color: #059669; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; border-radius: 10px;">
                  <i data-lucide="store" style="width: 16px; height: 16px;"></i> Déposé au pressing
                </button>
              <?php endif; ?>
            <?php else: ?>
              <?php if ($nextMission['statut_mission'] === 'en_attente'): ?>
                <button type="button" class="btn btn-primary" onclick="triggerLivreurAction('enRouteLivraison', '<?= $nextMission['code_mission'] ?>')" style="background: #2563EB; border-color: #2563EB; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; border-radius: 10px;">
                  <i data-lucide="truck" style="width: 16px; height: 16px;"></i> En route pour livrer
                </button>
              <?php elseif ($nextMission['statut_mission'] === 'en_cours'): ?>
                <button type="button" class="btn btn-success" onclick="triggerLivreurAction('remiseAuClient', '<?= $nextMission['code_mission'] ?>')" style="background: #059669; border-color: #059669; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; border-radius: 10px;">
                  <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i> Confirmer la remise au client
                </button>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- === LISTE DE TOUTES LES MISSIONS DE LA TOURNÉE === -->
      <div class="card" style="border-radius: 16px; border: 1px solid #E2E8F0; padding: 22px; background: #FFFFFF;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 12px;">
          <div>
            <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: #1E293B;">Mes Tournées & Courses</h3>
            <p style="margin: 2px 0 0; font-size: 13px; color: #64748B;">Historique et suivi complet de vos livraisons</p>
          </div>

          <div style="display: flex; gap: 6px; background: #F1F5F9; padding: 4px; border-radius: 10px;">
            <button class="btn btn-sm btn-filter active" data-filter="all" onclick="filterMissionList('all', this)" style="border-radius: 6px; font-weight: 700; font-size: 12px; padding: 6px 12px; background: #FFFFFF; border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">Toutes (<?= count($missions) ?>)</button>
            <button class="btn btn-sm btn-filter" data-filter="en_cours" onclick="filterMissionList('en_cours', this)" style="border-radius: 6px; font-weight: 600; font-size: 12px; padding: 6px 12px; background: transparent; border: none;">En cours</button>
            <button class="btn btn-sm btn-filter" data-filter="en_attente" onclick="filterMissionList('en_attente', this)" style="border-radius: 6px; font-weight: 600; font-size: 12px; padding: 6px 12px; background: transparent; border: none;">À faire</button>
            <button class="btn btn-sm btn-filter" data-filter="terminee" onclick="filterMissionList('terminee', this)" style="border-radius: 6px; font-weight: 600; font-size: 12px; padding: 6px 12px; background: transparent; border: none;">Terminées</button>
          </div>
        </div>

        <?php if (empty($missions)): ?>
          <div style="padding: 40px; text-align: center; color: #94A3B8;">
            <i class="fa fa-clipboard-check" style="font-size: 40px; margin-bottom: 12px; display: block; color: #CBD5E1;"></i>
            <h4 style="margin: 0; font-size: 16px; color: #475569;">Aucune mission assignée pour le moment</h4>
            <p style="margin: 4px 0 0; font-size: 13px;">Dès qu'un pressing vous assigne une course, elle apparaîtra directement ici.</p>
          </div>
        <?php else: ?>
          <div style="display: flex; flex-direction: column; gap: 12px;" id="missionsFeed">
            <?php foreach ($missions as $m): 
              $isCol = ($m['type_mission'] === 'collecte');
              $mLat = !empty($m['latitude_mission']) ? $m['latitude_mission'] : ($m['latitude_client'] ?? '');
              $mLng = !empty($m['longitude_mission']) ? $m['longitude_mission'] : ($m['longitude_client'] ?? '');
              $mAdr = !empty($m['adresse_mission']) ? $m['adresse_mission'] : ($m['adresse_client'] ?? 'Abidjan');
              $mMapsUrl = ($mLat && $mLng)
                  ? "https://www.google.com/maps/dir/?api=1&destination={$mLat},{$mLng}"
                  : "https://www.google.com/maps/search/?api=1&query=" . urlencode($mAdr . ' Abidjan');
            ?>
              <div class="mission-item-card card" data-status="<?= htmlspecialchars($m['statut_mission']) ?>" style="padding: 16px; border-radius: 12px; border: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                  <div style="width: 42px; height: 42px; border-radius: 10px; background: <?= $isCol ? '#FEF3C7' : '#EFF6FF' ?>; color: <?= $isCol ? '#D97706' : '#2563EB' ?>; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa <?= $isCol ? 'fa-box' : 'fa-truck' ?>"></i>
                  </div>
                  <div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <strong style="font-size: 14px; color: #1E293B;">#<?= htmlspecialchars($m['code_mission']) ?></strong>
                      <span style="font-size: 11px; padding: 2px 8px; border-radius: 12px; background: <?= $isCol ? '#FFFBEB' : '#F0FDF4' ?>; color: <?= $isCol ? '#B45309' : '#15803D' ?>; font-weight: 700;">
                        <?= $isCol ? 'Collecte' : 'Livraison' ?>
                      </span>
                      <span class="badge-status <?= $m['statut_mission'] === 'terminee' ? 'delivered' : ($m['statut_mission'] === 'en_cours' ? 'badge-status-progress' : '') ?>" style="font-size: 10px;">
                        <?= htmlspecialchars($m['statut_mission']) ?>
                      </span>
                    </div>
                    <div style="font-size: 13px; color: #475569; margin-top: 3px;">
                      Client : <strong><?= htmlspecialchars($m['nom_client'] ?? 'Client') ?></strong> • <i class="fa fa-map-marker-alt" style="color: #DC2626; font-size: 11px;"></i> <?= htmlspecialchars($mAdr) ?>
                    </div>
                  </div>
                </div>

                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                  <?php if (!empty($m['telephone_client'])): ?>
                    <a href="tel:<?= htmlspecialchars($m['telephone_client']) ?>" class="btn btn-sm btn-secondary" title="Appeler" style="padding: 6px 10px;">
                      <i class="fa fa-phone" style="color: #059669;"></i>
                    </a>
                  <?php endif; ?>
                  
                  <a href="<?= RACINE ?>mission/carte?mission=<?= urlencode($m['code_mission']) ?>" class="btn btn-sm btn-secondary" title="Tracé d'itinéraire et guidage GPS" style="padding: 6px 10px; display: inline-flex; align-items: center; gap: 4px; font-weight: 600;">
                    <i class="fa fa-location-arrow" style="color: #2563EB;"></i> GPS
                  </a>

                  <?php if ($m['statut_mission'] === 'en_attente'): ?>
                    <button type="button" class="btn btn-sm btn-primary" onclick="triggerLivreurAction('<?= $isCol ? 'enRouteCollecte' : 'enRouteLivraison' ?>', '<?= $m['code_mission'] ?>')" style="padding: 6px 12px; font-weight: 700;">
                      Démarrer
                    </button>
                  <?php elseif ($m['statut_mission'] === 'en_cours'): ?>
                    <?php if ($isCol): ?>
                      <button type="button" class="btn btn-sm btn-primary" onclick="triggerLivreurAction('lingeCollecte', '<?= $m['code_mission'] ?>')" style="padding: 6px 10px; font-weight: 700;">
                        Linge pris
                      </button>
                      <button type="button" class="btn btn-sm btn-success" onclick="triggerLivreurAction('deposeAuPressing', '<?= $m['code_mission'] ?>')" style="padding: 6px 10px; font-weight: 700;">
                        Au pressing
                      </button>
                    <?php else: ?>
                      <button type="button" class="btn btn-sm btn-success" onclick="triggerLivreurAction('remiseAuClient', '<?= $m['code_mission'] ?>')" style="padding: 6px 12px; font-weight: 700;">
                        Livré
                      </button>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>
</div>

<script>
function filterMissionList(status, btn) {
  $('.btn-filter').removeClass('active').css({ 'background': 'transparent', 'box-shadow': 'none' });
  $(btn).addClass('active').css({ 'background': '#FFFFFF', 'box-shadow': '0 1px 3px rgba(0,0,0,0.1)' });

  if (status === 'all') {
    $('.mission-item-card').show();
  } else {
    $('.mission-item-card').each(function() {
      if ($(this).attr('data-status') === status) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  }
}

function triggerLivreurAction(actionName, codeMission) {
  const baseApi = (typeof LINK !== 'undefined') ? LINK : ((typeof RACINE !== 'undefined') ? RACINE : '/admin-lavex/');
  
  if (typeof showToast === 'function') {
    showToast('Mise à jour de la mission...', 'info');
  }

  $.post(baseApi + 'mission/' + actionName, { code_mission: codeMission }, function(rep) {
    if (rep.status) {
      if (typeof showToast === 'function') showToast(rep.message || 'Action exécutée avec succès !', 'success');
      setTimeout(function() {
        window.location.reload();
      }, 700);
    } else {
      if (typeof showToast === 'function') showToast(rep.message || 'Erreur lors de l\'action', 'error');
    }
  }, 'json').fail(function() {
    if (typeof showToast === 'function') showToast('Erreur de connexion au serveur', 'error');
  });
}
</script>

<script src="<?= RACINE ?>public/json/livreur-gps-tracker.js?v=2"></script>
<script>
$(document).ready(function() {
  const codeLiv = '<?= htmlspecialchars($livreurCode ?? '') ?>';
  const hasActiveMission = <?= ($enCours > 0) ? 'true' : 'false' ?>;
  if (typeof LivreurGpsTracker !== 'undefined') {
    LivreurGpsTracker.init(null, codeLiv, hasActiveMission);
  }
});
</script>

<!-- === BARRE DE NAVIGATION INFÉRIEURE NATIVE PWA MOBILE === -->
<div class="mobile-driver-bottom-bar">
  <a href="<?= RACINE ?>" class="mobile-driver-nav-item active">
    <i data-lucide="layout-dashboard" style="width: 20px; height: 20px;"></i>
    <span>Accueil</span>
  </a>
  <a href="<?= RACINE ?>mission/carte" class="mobile-driver-nav-item">
    <i data-lucide="navigation" style="width: 20px; height: 20px;"></i>
    <span>GPS Live</span>
  </a>
  <a href="<?= RACINE ?>mission/list" class="mobile-driver-nav-item">
    <i data-lucide="clipboard-list" style="width: 20px; height: 20px;"></i>
    <span>Missions</span>
  </a>
  <a href="<?= RACINE ?>user/profil" class="mobile-driver-nav-item">
    <i data-lucide="user" style="width: 20px; height: 20px;"></i>
    <span>Profil</span>
  </a>
</div>

<?php require_once __DIR__ . '/../../public/inc/footer.php'; ?>
