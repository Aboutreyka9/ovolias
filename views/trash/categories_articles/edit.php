<?php
require_once __DIR__ . '/../../public/inc/header.php';
$categorie = isset($categorie) ? $categorie : [];
$currentIcon = $categorie['icon_categorie_article'] ?? '';
$hasExistingIcon = !empty($currentIcon);
$iconUrl = $hasExistingIcon ? (strpos($currentIcon, 'http') === 0 ? $currentIcon : RACINE . ltrim($currentIcon, '/')) : '';
?>

<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>

    <div class="content-wrapper">
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
        <div>
          <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #1E293B;">
            <?= isset($categorie['id_categorie_article']) ? 'Modifier la catégorie' : 'Ajouter une catégorie' ?>
          </h1>
          <p class="page-subtitle" style="margin: 4px 0 0; color: #64748B; font-size: 13px;">Gestion des catégories d'articles et de linge</p>
        </div>
        <a href="<?= RACINE ?>categorie/list" class="btn btn-sm btn-outline-secondary" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
          <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
          Retour à la liste
        </a>
      </div>

      <div class="form-card card" style="border-radius: 14px; border: 1px solid #E2E8F0; padding: 24px; background: #FFFFFF;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid #E2E8F0;">
          <div>
            <h2 style="font-size: 16px; font-weight: 800; color: #1E293B; margin: 0;">Informations de la catégorie</h2>
          </div>
          <?php if (isset($categorie['statut_categorie_article'])): ?>
            <span class="badge-status <?= $categorie['statut_categorie_article'] == 'actif' ? 'delivered' : 'cancelled' ?>">
              <?= $categorie['statut_categorie_article'] == 'actif' ? 'Actif' : 'Inactif' ?>
            </span>
          <?php endif; ?>
        </div>

        <div class="card-body">
          <form class="formEditCategorie" enctype="multipart/form-data">
            <?= Validator::csrfField() ?>
            <input type="hidden" id="id_categorie_article" name="id_categorie_article" value="<?= htmlspecialchars($categorie['id_categorie_article'] ?? '') ?>">
            <input type="hidden" id="icon_categorie_article" name="icon_categorie_article" value="<?= htmlspecialchars($currentIcon) ?>">

            <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
              <div class="form-field">
                <label for="libelle_categorie_article" style="font-weight: 700; font-size: 13px; color: #1E293B; margin-bottom: 6px; display: block;">Libellé de la catégorie <span style="color: #DC2626;">*</span></label>
                <div class="input-with-icon">
                  <span class="input-icon"><?= Validator::icon('file-text'); ?></span>
                  <input type="text" class="form-control" id="libelle_categorie_article" name="libelle_categorie_article"
                         placeholder="Ex: Vêtements homme, Linge de maison, Cuir..."
                         value="<?= htmlspecialchars($categorie['libelle_categorie_article'] ?? '') ?>" required>
                </div>
                <div class="error-message" id="libelleError"></div>
              </div>

              <?php if (isset($categorie['statut_categorie_article'])): ?>
              <div class="form-field">
                <label for="actif" style="font-weight: 700; font-size: 13px; color: #1E293B; margin-bottom: 6px; display: block;">Statut de la catégorie</label>
                <div class="input-with-icon">
                  <span class="input-icon"><?= Validator::icon('toggle-left'); ?></span>
                  <select class="form-control" id="actif" name="actif">
                    <option value="1" <?= ($categorie['statut_categorie_article'] ?? '') == 'actif' ? 'selected' : '' ?>>Actif</option>
                    <option value="0" <?= ($categorie['statut_categorie_article'] ?? '') == 'inactif' ? 'selected' : '' ?>>Inactif</option>
                  </select>
                </div>
                <div class="error-message" id="actifError"></div>
              </div>
              <?php endif; ?>

              <div class="form-field" style="grid-column: 1 / -1;">
                <label for="description_categorie_article" style="font-weight: 700; font-size: 13px; color: #1E293B; margin-bottom: 6px; display: block;">Description</label>
                <div class="input-with-icon">
                  <span class="input-icon" style="top: 14px;"><?= Validator::icon('align-left'); ?></span>
                  <textarea class="form-control" id="description_categorie_article" name="description_categorie_article" rows="3" placeholder="Description optionnelle des types d'articles regroupés dans cette catégorie..."><?= htmlspecialchars($categorie['description_categorie_article'] ?? '') ?></textarea>
                </div>
                <div class="error-message" id="descriptionError"></div>
              </div>

              <!-- CHAMP FICHIER : ICÔNE / IMAGE DE LA CATÉGORIE -->
              <div class="form-field" style="grid-column: 1 / -1;">
                <label for="icon_file" style="font-weight: 700; font-size: 13px; color: #1E293B; margin-bottom: 6px; display: block;">
                  Icône / Image de la catégorie (Fichier image)
                </label>
                
                <div style="display: flex; gap: 16px; align-items: flex-start; flex-wrap: wrap;">
                  <!-- Zone d'aperçu d'image -->
                  <div id="iconPreviewWrapper" style="width: 80px; height: 80px; border-radius: 12px; border: 2px dashed #CBD5E1; background: #F8FAFC; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; position: relative;">
                    <?php if ($hasExistingIcon): ?>
                      <img id="iconPreviewImg" src="<?= htmlspecialchars($iconUrl) ?>" alt="Icône actuelle" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                      <img id="iconPreviewImg" src="" alt="Aperçu" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                      <i id="iconPlaceholderIcon" class="fa fa-image" style="font-size: 26px; color: #94A3B8;"></i>
                    <?php endif; ?>
                  </div>

                  <!-- Sélecteur de fichier -->
                  <div style="flex: 1; min-width: 240px;">
                    <div class="input-with-icon">
                      <span class="input-icon"><?= Validator::icon('image'); ?></span>
                      <input type="file" class="form-control" id="icon_file" name="icon_file" accept="image/png,image/jpeg,image/webp,image/svg+xml,image/gif" onchange="previewCategoryIcon(this)">
                    </div>
                    <small style="color: #64748B; font-size: 12px; display: block; margin-top: 6px;">
                      Formats acceptés : <strong>PNG, JPG, WEBP, SVG, GIF</strong> (Taille max : 5 Mo).
                      <?php if ($hasExistingIcon): ?>
                        <span style="color: #059669; display: block; margin-top: 2px;"><i class="fa fa-check-circle"></i> Une icône est déjà configurée. Choisissez un nouveau fichier pour la remplacer.</span>
                      <?php endif; ?>
                    </small>
                  </div>
                </div>
                <div class="error-message" id="iconError"></div>
              </div>

            </div>

            <div class="form-actions" style="display: flex; gap: 10px; margin-top: 26px; padding-top: 18px; border-top: 1px solid #E2E8F0;">
              <button type="submit" class="btn btn-primary btn_actions btnEditCategorie" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; background: #1E3A5F; border-color: #1E3A5F;">
                <span class="btn-text" style="display: inline-flex; align-items: center; gap: 6px;">
                  <i data-lucide="save" style="width: 16px; height: 16px;"></i>
                  Sauvegarder
                </span>
              </button>
              <a href="<?= RACINE ?>categorie/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
                <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                Annuler
              </a>
            </div>
          </form>
        </div>
      </div>

    </div>
  </main>
</div>

<script>
function previewCategoryIcon(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      $('#iconPreviewImg').attr('src', e.target.result).show();
      $('#iconPlaceholderIcon').hide();
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
<script src="<?= RACINE ?>public/json/entities/categories.js?v=5"></script>
<?php require_once __DIR__ . '/../../public/inc/footer.php'; ?>
