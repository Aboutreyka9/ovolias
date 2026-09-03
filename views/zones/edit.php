<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$isEdit = !empty($item['id_zone']);
$title = $isEdit ? 'Éditer la Zone' : 'Nouvelle Zone Commerciale';
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px;">
      <div class="page-header" style="margin-bottom: 24px;">
        <h1 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0;"><?= $title ?></h1>
        <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Définissez les paramètres de la zone commerciale</p>
      </div>
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; max-width: 600px;">
        <form id="form-zone" action="<?= RACINE ?>zone/<?= $isEdit ? 'edit' : 'add' ?>" method="POST">
          <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
          <?php if ($isEdit): ?>
            <input type="hidden" name="id_zone" value="<?= $item['id_zone'] ?>">
          <?php endif; ?>
          
          <div class="mb-3">
            <label class="form-label" style="font-weight:600; color:#334155;">Nom / Libellé de la Zone *</label>
            <input type="text" name="libelle_zone" class="form-control" value="<?= htmlspecialchars($item['libelle_zone'] ?? '') ?>" required placeholder="ex: ZONE A - Centre Ville">
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-weight:600; color:#334155;">Statut</label>
            <select name="statut_zone" class="form-select">
              <option value="actif" <?= ($item['statut_zone'] ?? 'actif') === 'actif' ? 'selected' : '' ?>>Actif</option>
              <option value="inactif" <?= ($item['statut_zone'] ?? '') === 'inactif' ? 'selected' : '' ?>>Inactif</option>
            </select>
          </div>

          <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px;">
            <a href="<?= RACINE ?>zone/list" class="btn btn-light" style="border:1px solid #CBD5E1;">Annuler</a>
            <button type="submit" class="btn btn-success" style="background:#15803D; border-color:#15803D; font-weight:700;">
              <?= $isEdit ? 'Enregistrer les modifications' : 'Créer la zone' ?>
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>
<script>
$(document).ready(function() {
  $('#form-zone').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(res) {
        if (res.status === 1 || res.success) {
          if (window.toastr) toastr.success(res.message || 'Opération réussie');
          setTimeout(function() { window.location.href = '<?= RACINE ?>zone/list'; }, 1000);
        } else {
          if (window.toastr) toastr.error(res.message || 'Erreur lors de l\'enregistrement');
        }
      },
      error: function() {
        if (window.toastr) toastr.error('Erreur réseau');
      }
    });
  });
});
</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
