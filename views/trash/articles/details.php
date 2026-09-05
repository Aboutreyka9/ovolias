<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px;">
      <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <div>
          <h1 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0;">Fiche Article : <?= htmlspecialchars($item['libelle_article'] ?? '') ?></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Code Article : <code><?= htmlspecialchars($item['code_article'] ?? '') ?></code></p>
        </div>
        <a href="<?= RACINE ?>article/list" class="btn btn-outline-secondary">
          <i data-lucide="arrow-left"></i> Retour au catalogue
        </a>
      </div>
      <div class="card" style="background:#FFF; border-radius:12px; padding:24px; border:1px solid #E2E8F0; max-width:600px;">
        <div class="row mb-3">
          <div class="col-6"><strong>Libellé Article :</strong></div>
          <div class="col-6"><?= htmlspecialchars($item['libelle_article'] ?? '-') ?></div>
        </div>
        <div class="row mb-3">
          <div class="col-6"><strong>Description :</strong></div>
          <div class="col-6"><?= nl2br(htmlspecialchars($item['description_article'] ?? '-')) ?></div>
        </div>
        <div class="row mb-3">
          <div class="col-6"><strong>Statut :</strong></div>
          <div class="col-6">
            <span class="badge <?= ($item['statut_article'] ?? '') === 'actif' ? 'bg-success' : 'bg-secondary' ?>">
              <?= ucfirst($item['statut_article'] ?? 'inactif') ?>
            </span>
          </div>
        </div>
        <div style="margin-top:24px; text-align:right;">
          <a href="<?= RACINE ?>article/edition/<?= $encryptedId ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F;">Éditer l'article</a>
        </div>
      </div>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
