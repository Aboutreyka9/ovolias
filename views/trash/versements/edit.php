<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$isEdit = !empty($item['id_versement']);
$title = $isEdit ? 'Éditer le Versement' : 'Nouveau Versement de Fonds Commercial';
$commerciaux = $commerciaux ?? [];
$zones = $zones ?? [];
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">
      
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="arrow-down-left" style="color: #1E3A5F; width: 26px; height: 26px;"></i>
            <span><?= $title ?></span>
          </h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Dépôt en caisse centrale des fonds collectés par le commercial sur le terrain</p>
        </div>
        <a href="<?= RACINE ?>versement/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
          <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Retour aux versements
        </a>
      </div>

      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <form id="form-versement" action="<?= RACINE ?>versement/<?= $isEdit ? 'edit' : 'add' ?>" method="POST" enctype="multipart/form-data" style="width: 100%;">
          <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
          <?php if ($isEdit): ?>
            <input type="hidden" name="id_versement" value="<?= $item['id_versement'] ?>">
          <?php endif; ?>

          <div style="margin-bottom: 24px;">
            <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
              <i data-lucide="user" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Étape 1 : Agent Commercial & Fonds à Verser
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Agent Commercial <span style="color: #EF4444;">*</span></label>
                <select name="commercial_code" class="form-control select2" style="width: 100%; box-sizing: border-box;" required>
                  <option value="">-- Sélectionner l'agent commercial --</option>
                  <?php foreach ($commerciaux as $c): ?>
                    <option value="<?= $c['code_user'] ?>" <?= ($item['commercial_code'] ?? '') === $c['code_user'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars(trim(($c['nom_user'] ?? '') . ' ' . ($c['prenom_user'] ?? ''))) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Montant Versé (FCFA) <span style="color: #EF4444;">*</span></label>
                <input type="number" name="montant_versement" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-weight: 800; color: #15803D; outline: none;" value="<?= htmlspecialchars($item['montant_versement'] ?? '') ?>" required placeholder="Ex: 50000">
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Zone d'Activité</label>
                <select name="zone_code" class="form-control select2" style="width: 100%; box-sizing: border-box;">
                  <option value="">-- Sélectionner la zone --</option>
                  <?php foreach ($zones as $z): ?>
                    <option value="<?= $z['code_zone'] ?>" <?= ($item['zone_code'] ?? '') === $z['code_zone'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($z['libelle_zone']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <div style="margin-bottom: 24px;">
            <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
              <i data-lucide="clock" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Étape 2 : Période & Référence
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Date Début Période</label>
                <input type="date" name="periode_versement_debut" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" value="<?= htmlspecialchars($item['periode_versement_debut'] ?? date('Y-m-d')) ?>">
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Date Fin Période</label>
                <input type="date" name="periode_versement_fin" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" value="<?= htmlspecialchars($item['periode_versement_fin'] ?? date('Y-m-d')) ?>">
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Référence Versement</label>
                <input type="text" name="reference_versement" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" value="<?= htmlspecialchars($item['reference_versement'] ?? '') ?>" placeholder="Ex: VRS-2026-001">
              </div>
            </div>
          </div>

          <div style="display: flex; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #E2E8F0; width: 100%;">
            <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 8px; padding: 10px 24px; display: inline-flex; align-items: center; gap: 8px;">
              <i data-lucide="check" style="width: 18px; height: 18px;"></i> <?= $isEdit ? 'Enregistrer les modifications' : 'Valider le Versement' ?>
            </button>
            <a href="<?= RACINE ?>versement/list" class="btn btn-secondary" style="font-weight: 600; border-radius: 8px; padding: 10px 24px; text-decoration: none;">Annuler</a>
          </div>
        </form>
      </div>

    </div>
  </main>
</div>

<script>
$(document).ready(function() {
  if (window.lucide) lucide.createIcons();
  if ($.fn.select2) {
    $('.select2').select2({ width: '100%' });
  }

  $('#form-versement').on('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(res) {
        if (res.status === 1 || res.success) {
          if (window.toastr) toastr.success(res.message || 'Opération réussie');
          setTimeout(function() { window.location.href = '<?= RACINE ?>versement/list'; }, 1000);
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
