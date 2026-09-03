<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0;">PV Distribution : <code style="font-weight: 800; color: #7E22CE; font-size: 18px; background: #FAF5FF; padding: 3px 10px; border-radius: 6px;"><?= htmlspecialchars($item['code_distribution'] ?? '-') ?></code></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Livraison du <?= !empty($item['date_distribution_effectuee']) ? date('d/m/Y H:i', strtotime($item['date_distribution_effectuee'])) : '-' ?></p>
        </div>
        <a href="<?= RACINE ?>distribution/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
          <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Retour aux distributions
        </a>
      </div>

      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
          <i data-lucide="user-check" style="width: 18px; height: 18px;"></i> Informations Bénéficiaire
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Client</span>
            <div style="font-size: 17px; font-weight: 800; color: #0F172A; margin-top: 4px;"><?= htmlspecialchars(trim(($souscription['nom_client'] ?? '') . ' ' . ($souscription['prenom_client'] ?? ''))) ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Tél : <?= htmlspecialchars($souscription['telephone_client'] ?? '-') ?></div>
          </div>
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Souscription</span>
            <div style="font-size: 17px; font-weight: 800; color: #1E3A5F; margin-top: 4px;"><?= htmlspecialchars($souscription['code_souscription'] ?? '-') ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Pack : <?= htmlspecialchars($souscription['libelle_pack'] ?? '-') ?></div>
          </div>
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Statut Distribution</span>
            <div style="margin-top: 6px;">
              <?php 
                $statut = $item['statut_distribution'] ?? 'En attente';
                $badge = 'bg-warning text-dark';
                if ($statut === 'valide') $badge = 'bg-success';
                elseif ($statut === 'ennule') $badge = 'bg-danger';
              ?>
              <span class="badge <?= $badge ?>" style="padding: 6px 14px; border-radius: 10px; font-weight: 800; font-size: 12px;"><?= htmlspecialchars($statut) ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
          <i data-lucide="truck" style="width: 18px; height: 18px;"></i> Détails Logistiques
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Zone de Livraison</span>
            <div style="font-size: 16px; font-weight: 700; color: #0F172A; margin-top: 4px;"><?= htmlspecialchars($item['libelle_zone'] ?? ($item['zone_code'] ?? '-')) ?></div>
          </div>
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Agent Livreur</span>
            <div style="font-size: 16px; font-weight: 700; color: #0F172A; margin-top: 4px;"><?= htmlspecialchars(trim(($livreur['nom_user'] ?? '') . ' ' . ($livreur['prenom_user'] ?? ''))) ?></div>
          </div>
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Date de Remise</span>
            <div style="font-size: 16px; font-weight: 700; color: #0F172A; margin-top: 4px;"><?= !empty($item['date_distribution_effectuee']) ? date('d/m/Y H:i', strtotime($item['date_distribution_effectuee'])) : '-' ?></div>
          </div>
        </div>

        <?php if (!empty($item['observation_distribution'])): ?>
        <div style="margin-top: 20px; padding: 14px; background: #F8FAFC; border-radius: 8px; border: 1px solid #E2E8F0;">
          <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Observation</span>
          <p style="margin: 6px 0 0 0; color: #334155; font-size: 14px;"><?= nl2br(htmlspecialchars($item['observation_distribution'])) ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($item['pv_reception_photo'])): ?>
        <div style="margin-top: 20px;">
          <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">PV de Réception</span>
          <div style="margin-top: 8px;">
            <img src="<?= RACINE . htmlspecialchars($item['pv_reception_photo']) ?>" style="max-height: 200px; border-radius: 8px; border: 1px solid #E2E8F0;">
          </div>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </main>
</div>
<script>$(document).ready(function() { if (window.lucide) lucide.createIcons(); });</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
