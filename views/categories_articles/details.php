<?php
require_once __DIR__ . '/../../public/inc/header.php';
$categorie = isset($categorie) ? $categorie : [];
$icon = $categorie['icon_categorie_article'] ?? '';
$iconUrl = !empty($icon) ? (strpos($icon, 'http') === 0 ? $icon : RACINE . ltrim($icon, '/')) : '';
?>

<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>

    <div class="content-wrapper">
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
        <div>
          <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #1E293B;">Détails de la catégorie</h1>
          <p class="page-subtitle" style="margin: 4px 0 0; color: #64748B; font-size: 13px;">Informations complètes</p>
        </div>
        <div style="display: flex; gap: 8px;">
          <a href="<?= RACINE ?>categorie/edition/<?= $encryptedId ?? '' ?>" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700;">
            <i data-lucide="edit" style="width: 16px; height: 16px;"></i> Modifier
          </a>
          <a href="<?= RACINE ?>categorie/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Retour
          </a>
        </div>
      </div>

      <div class="detail-card card" style="border-radius: 14px; border: 1px solid #E2E8F0; padding: 24px; background: #FFFFFF;">
        <div class="detail-card-header" style="margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center;">
          <h2 style="margin: 0; font-size: 16px; font-weight: 800; color: #1E293B;">Fiche Catégorie</h2>
          <span class="badge-status <?= ($categorie['statut_categorie_article'] ?? '') == 'actif' ? 'delivered' : 'cancelled' ?>">
            <?= htmlspecialchars($categorie['statut_categorie_article'] ?? '') ?>
          </span>
        </div>
        <div class="detail-card-body">
          <div class="info-list" style="display: flex; flex-direction: column; gap: 16px;">
            <div class="info-item" style="display: flex; align-items: center; gap: 16px;">
              <span class="info-label" style="width: 140px; font-weight: 700; color: #64748B; font-size: 13px;">Icône / Image</span>
              <span class="info-value">
                <?php if (!empty($iconUrl)): ?>
                  <img src="<?= htmlspecialchars($iconUrl) ?>" alt="Icône catégorie" style="width: 60px; height: 60px; border-radius: 10px; object-fit: cover; border: 1px solid #CBD5E1; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                <?php else: ?>
                  <span style="color: #94A3B8; font-size: 13px; font-style: italic;">Aucune icône définie</span>
                <?php endif; ?>
              </span>
            </div>
            <div class="info-item" style="display: flex; align-items: center; gap: 16px;">
              <span class="info-label" style="width: 140px; font-weight: 700; color: #64748B; font-size: 13px;">Code</span>
              <span class="info-value code-badge" style="background: #EFF6FF; color: #1D4ED8; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-family: monospace;"><?= htmlspecialchars($categorie['code_categorie_article'] ?? '') ?></span>
            </div>
            <div class="info-item" style="display: flex; align-items: center; gap: 16px;">
              <span class="info-label" style="width: 140px; font-weight: 700; color: #64748B; font-size: 13px;">Libellé</span>
              <strong class="info-value" style="font-size: 15px; color: #1E293B;"><?= htmlspecialchars($categorie['libelle_categorie_article'] ?? '-') ?></strong>
            </div>
            <div class="info-item" style="display: flex; align-items: flex-start; gap: 16px;">
              <span class="info-label" style="width: 140px; font-weight: 700; color: #64748B; font-size: 13px;">Description</span>
              <span class="info-value" style="color: #475569; font-size: 13px;"><?= nl2br(htmlspecialchars($categorie['description_categorie_article'] ?? '-')) ?></span>
            </div>
            <div class="info-item" style="display: flex; align-items: center; gap: 16px;">
              <span class="info-label" style="width: 140px; font-weight: 700; color: #64748B; font-size: 13px;">Date de création</span>
              <span class="info-value" style="color: #64748B; font-size: 13px;"><?= htmlspecialchars($categorie['created_at_categorie_article'] ?? '-') ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../../public/inc/footer.php'; ?>
