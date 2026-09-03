<?php
require_once __DIR__ . '/../../public/inc/header.php';
$item = isset($item) ? $item : [];
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px;">
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Fiche Permission : <?= htmlspecialchars($item['libelle_permission'] ?? 'Permission') ?></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Consultation des données d'autorisation granulaire</p>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="<?= RACINE ?>permission/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour à la liste
          </a>
          <a href="<?= RACINE ?>permission/edition/<?= $encryptedId ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="edit" style="width: 18px; height: 18px;"></i> Éditer cette permission
          </a>
        </div>
      </div>

      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #F1F5F9;">
          <div style="width: 44px; height: 44px; border-radius: 10px; background: #EFF6FF; color: #1E3A5F; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="key" style="width: 24px; height: 24px;"></i>
          </div>
          <div>
            <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin: 0;">Détail de la Permission</h3>
            <span style="font-size: 12px; color: #64748B;">Réf ID #<?= htmlspecialchars($item['id_permission'] ?? '-') ?></span>
          </div>
          <div style="margin-left: auto;">
            <?php if (($item['statut_permission'] ?? '') === 'actif'): ?>
              <span class="badge" style="background:#DCFCE7; color:#15803D; padding:6px 14px; border-radius:14px; font-weight:700; font-size:13px;">Statut : Actif</span>
            <?php else: ?>
              <span class="badge" style="background:#FEE2E2; color:#B91C1C; padding:6px 14px; border-radius:14px; font-weight:700; font-size:13px;">Statut : Inactif</span>
            <?php endif; ?>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
          <div style="background: #F8FAFC; border-radius: 8px; padding: 16px; border: 1px solid #F1F5F9;">
            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748B; letter-spacing: 0.5px; margin-bottom: 6px;">Libellé de la Permission</div>
            <div style="font-size: 15px; font-weight: 700; color: #0F172A; word-break: break-word;">
              <?= htmlspecialchars($item['libelle_permission'] ?? '-') ?>
            </div>
          </div>
          <div style="background: #F8FAFC; border-radius: 8px; padding: 16px; border: 1px solid #F1F5F9;">
            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748B; letter-spacing: 0.5px; margin-bottom: 6px;">Code Clé Système</div>
            <div style="font-size: 15px; font-weight: 700; color: #1E3A5F; font-family: monospace; word-break: break-word;">
              <?= htmlspecialchars($item['code_permission'] ?? '-') ?>
            </div>
          </div>
          <div style="background: #F8FAFC; border-radius: 8px; padding: 16px; border: 1px solid #F1F5F9;">
            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748B; letter-spacing: 0.5px; margin-bottom: 6px;">Module Fonctionnel</div>
            <div style="font-size: 15px; font-weight: 700; color: #0284C7; word-break: break-word;">
              <?= htmlspecialchars($item['module_permission'] ?? '-') ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
<script>$(document).ready(function() { if (window.lucide) lucide.createIcons(); });</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
