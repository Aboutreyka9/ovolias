<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px;">
      <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <div>
          <h1 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0;">Zone : <?= htmlspecialchars($item['libelle_zone'] ?? '') ?></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Code Zone : <code><?= htmlspecialchars($item['code_zone'] ?? '') ?></code></p>
        </div>
        <a href="<?= RACINE ?>zone/list" class="btn btn-outline-secondary">
          <i data-lucide="arrow-left"></i> Retour aux zones
        </a>
      </div>
      <div class="card" style="background:#FFF; border-radius:12px; padding:24px; border:1px solid #E2E8F0; max-width:600px;">
        <div class="row mb-3">
          <div class="col-6"><strong>Libellé Succursale :</strong></div>
          <div class="col-6"><?= htmlspecialchars($item['libelle_zone'] ?? '-') ?></div>
        </div>
        <div class="row mb-3">
          <div class="col-6"><strong>Localisation :</strong></div>
          <div class="col-6"><?= htmlspecialchars($item['localisation_zone'] ?? '-') ?></div>
        </div>
        <div class="row mb-3">
          <div class="col-6"><strong>N° Téléphone :</strong></div>
          <div class="col-6"><?= htmlspecialchars($item['telephone_zone'] ?? '-') ?></div>
        </div>
        <div class="row mb-3">
          <div class="col-6"><strong>Contact / Responsable :</strong></div>
          <div class="col-6"><?= htmlspecialchars($item['contact_zone'] ?? '-') ?></div>
        </div>
        <div class="row mb-3">
          <div class="col-6"><strong>Date Création :</strong></div>
          <div class="col-6"><?= htmlspecialchars($item['created_at_zone'] ?? '-') ?></div>
        </div>
        <div class="row mb-3">
          <div class="col-6"><strong>Statut :</strong></div>
          <div class="col-6">
            <span class="badge <?= ($item['statut_zone'] ?? '') === 'actif' ? 'bg-success' : 'bg-secondary' ?>">
              <?= ucfirst($item['statut_zone'] ?? 'inactif') ?>
            </span>
          </div>
        </div>
        <div style="margin-top:24px; text-align:right;">
          <a href="<?= RACINE ?>zone/edition/<?= $encryptedId ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F;">Éditer la zone</a>
        </div>
      </div>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
