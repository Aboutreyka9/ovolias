<?php
require_once __DIR__ . '/../../public/inc/header.php';
$item = isset($item) ? $item : (isset($permission) ? $permission : []);
$modules = [];
try {
    $modules = (new ModelModuleMetier())->getByStatus('actif');
} catch (Exception $e) {
    $modules = [];
}

if (empty($modules)) {
    $modules = [
        ['code_module' => 'COMMERCIAL', 'libelle_module' => 'Espace Commercial & Souscriptions'],
        ['code_module' => 'DISTRIBUTION', 'libelle_module' => 'Logistique & Distributions'],
        ['code_module' => 'VERSEMENT', 'libelle_module' => 'Versements Commerciaux & Caisse'],
        ['code_module' => 'FINANCE', 'libelle_module' => 'Dépenses & Gestion Financière'],
        ['code_module' => 'ADMINISTRATION', 'libelle_module' => 'Administration & Sécurité'],
    ];
}
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px;">
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;"><?= !empty($item['id_permission']) ? 'Éditer ' : 'Ajouter ' ?> une Permission Granulaire</h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Configuration des autorisations unitaires et des privilèges système</p>
        </div>
        <a href="<?= RACINE ?>permission/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
          <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour à la liste
        </a>
      </div>

      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <form action="<?= RACINE ?>permission/<?= !empty($item['id_permission']) ? 'edit' : 'add' ?>" method="POST" style="width: 100%;">
          <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
          <?php if (!empty($item['id_permission'])): ?>
            <input type="hidden" name="id_permission" value="<?= $item['id_permission'] ?>">
          <?php endif; ?>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; width: 100%;">
            
            <div class="form-group" style="width: 100%; box-sizing: border-box;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Libellé de la Permission <span style="color: #EF4444;">*</span></label>
              <input type="text" class="form-control" name="libelle_permission" value="<?= htmlspecialchars($item['libelle_permission'] ?? '') ?>" placeholder="Ex: Saisie et Édition des Notes" required style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-weight: 600;">
            </div>

            <div class="form-group" style="width: 100%; box-sizing: border-box;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Code Système <span style="color: #EF4444;">*</span></label>
              <input type="text" class="form-control" name="code_permission" value="<?= htmlspecialchars($item['code_permission'] ?? '') ?>" placeholder="Ex: MANAGE_GRADES" <?= !empty($item['id_permission']) ? 'readonly' : 'required' ?> style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #CBD5E1; background: <?= !empty($item['id_permission']) ? '#F1F5F9' : '#FFF' ?>; font-family: monospace; font-weight: 700;">
            </div>

            <div class="form-group" style="width: 100%; box-sizing: border-box;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Module Métier Associé <span style="color: #EF4444;">*</span></label>
              <div style="display: flex; gap: 8px; align-items: flex-start;">
                <select class="form-control select2" id="select-module-permission" name="module_permission" style="width: 100%;" required>
                  <option value="">-- Sélectionner un module --</option>
                  <?php foreach ($modules as $m): ?>
                    <option value="<?= htmlspecialchars($m['code_module']) ?>" <?= (($item['module_permission'] ?? '') === $m['code_module']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($m['libelle_module']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <button type="button" id="btn-add-module" class="btn" style="height: 42px; min-width: 42px; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; border: 2px dashed #1E3A5F; color: #1E3A5F; background: #FFFFFF;" title="Ajouter un nouveau module métier">
                  <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                </button>
              </div>
            </div>

            <?php if (!empty($item['id_permission'])): ?>
            <div class="form-group" style="width: 100%; box-sizing: border-box;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Statut</label>
              <select class="form-control" name="statut_permission" style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #CBD5E1;">
                <option value="actif" <?= (($item['statut_permission'] ?? 'actif') === 'actif') ? 'selected' : '' ?>>Actif</option>
                <option value="inactif" <?= (($item['statut_permission'] ?? '') === 'inactif') ? 'selected' : '' ?>>Inactif</option>
              </select>
            </div>
            <?php endif; ?>

          </div>

          <div style="display: flex; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #E2E8F0; width: 100%;">
            <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 8px; padding: 10px 24px;">Enregistrer la Permission</button>
            <a href="<?= RACINE ?>permission/list" class="btn btn-secondary" style="font-weight: 600; border-radius: 8px; padding: 10px 24px;">Annuler</a>
          </div>
        </form>
      </div>

      <!-- Modal Ajout Module Métier -->
      <div class="modal-overlay" id="modalAddModule">
        <div class="modal" style="max-width: 420px;">
          <div class="modal-header">
            <h3 class="modal-title">Nouveau Module Métier</h3>
            <button class="modal-close" id="modalAddModuleClose"><i data-lucide="x"></i></button>
          </div>
          <div class="modal-body">
            <form id="form-add-module">
              <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Libellé du Module <span style="color: #EF4444;">*</span></label>
                <input type="text" id="input-libelle-module" class="form-control" placeholder="Ex: Logistique & Distributions" required style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #CBD5E1;">
                <small style="color: #64748B; font-size: 11px; margin-top: 4px; display: block;">Le code système sera généré automatiquement.</small>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button class="btn-secondary" id="modalAddModuleCancel">Annuler</button>
            <button class="btn-primary" id="modalAddModuleSave" style="background: #1E3A5F; border-color: #1E3A5F;">Créer le Module</button>
          </div>
        </div>
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

  var $modalModule = $('#modalAddModule');
  var $selectModule = $('#select-module-permission');

  $('#btn-add-module').on('click', function() {
    $modalModule.addClass('active');
    $('#input-libelle-module').val('').focus();
  });

  $('#modalAddModuleClose, #modalAddModuleCancel').on('click', function() {
    $modalModule.removeClass('active');
    $('#input-libelle-module').val('');
  });

  $('#modalAddModuleSave').on('click', function() {
    var libelle = $('#input-libelle-module').val().trim();
    if (!libelle) {
      if (window.toastr) toastr.error('Veuillez saisir le libellé du module');
      return;
    }

    $.ajax({
      url: '<?= RACINE ?>permission/addModule',
      type: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      data: {
        libelle_module: libelle,
        csrf_token: '<?= Validator::generateCsrfToken() ?>'
      },
      dataType: 'json',
      success: function(res) {
        console.log('AJAX addModule response:', res);
        if (res.status === 1 || res.success) {
          if (window.toastr) toastr.success(res.message || 'Module créé avec succès');
          var newOption = new Option(res.libelle_module, res.code_module, true, true);
          $selectModule.append(newOption).trigger('change');
          $modalModule.removeClass('active');
          $('#input-libelle-module').val('');
        } else {
          if (window.toastr) toastr.error(res.message || 'Erreur lors de la création du module');
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX addModule error:', status, error, xhr.responseText);
        if (window.toastr) toastr.error('Erreur réseau');
      }
    });
  });
});
</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
