<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$isEdit = !empty($item['id_zone_commercial']);
$title = $isEdit ? 'Éditer l\'Affectation Zone' : 'Nouvelle Affectation Commercial / Zone';
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px;">
      <div class="page-header" style="margin-bottom: 24px;">
        <h1 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0;"><?= $title ?></h1>
        <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Associez un commercial à une zone géographique spécifique</p>
      </div>
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; max-width: 600px;">
        <form id="form-zc" action="<?= RACINE ?>zone_commercial/<?= $isEdit ? 'edit' : 'add' ?>" method="POST">
          <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
          <?php if ($isEdit): ?>
            <input type="hidden" name="id_zone_commercial" value="<?= $item['id_zone_commercial'] ?>">
          <?php endif; ?>
          
          <div class="mb-3">
            <label class="form-label" style="font-weight:600; color:#334155;">Agent Commercial *</label>
            <select name="commercial_code" class="form-select" required>
              <option value="">-- Sélectionner un commercial --</option>
              <?php foreach ($commerciaux as $c): ?>
                <option value="<?= $c['code_user'] ?>" <?= ($item['commercial_code'] ?? '') === $c['code_user'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars(trim($c['nom_user'] . ' ' . $c['prenom_user'])) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-weight:600; color:#334155;">Zone Commerciale *</label>
            <select name="zone_code" class="form-select" required>
              <option value="">-- Sélectionner une zone --</option>
              <?php foreach ($zones as $z): ?>
                <option value="<?= $z['code_zone'] ?>" <?= ($item['zone_code'] ?? '') === $z['code_zone'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($z['libelle_zone']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-weight:600; color:#334155;">Statut</label>
            <select name="statut_zone_commercial" class="form-select">
              <option value="actif" <?= ($item['statut_zone_commercial'] ?? 'actif') === 'actif' ? 'selected' : '' ?>>Actif</option>
              <option value="inactif" <?= ($item['statut_zone_commercial'] ?? '') === 'inactif' ? 'selected' : '' ?>>Inactif</option>
            </select>
          </div>

          <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px;">
            <a href="<?= RACINE ?>zone_commercial/list" class="btn btn-light" style="border:1px solid #CBD5E1;">Annuler</a>
            <button type="submit" class="btn btn-success" style="background:#15803D; border-color:#15803D; font-weight:700;">
              <?= $isEdit ? 'Enregistrer les modifications' : 'Affecter le commercial' ?>
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>
<script>
$(document).ready(function() {
  $('#form-zc').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(res) {
        if (res.status === 1 || res.success) {
          if (window.toastr) toastr.success(res.message || 'Opération réussie');
          setTimeout(function() { window.location.href = '<?= RACINE ?>zone_commercial/list'; }, 1000);
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
