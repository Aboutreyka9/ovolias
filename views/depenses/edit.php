<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$item = $item ?? [];
$isEdit = !empty($item['id_depense']);
$title = $isEdit ? 'Éditer la Dépense' : 'Saisie d\'une Dépense d\'Exploitation';
$typeDepenses = $typeDepenses ?? [];
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">
      
      <!-- EN-TÊTE DE PAGE -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="arrow-up-right" style="color: #DC2626; width: 26px; height: 26px;"></i>
            <span><?= $title ?></span>
          </h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Enregistrement des frais de fonctionnement et sorties de caisse Olive Service</p>
        </div>
        <a href="<?= RACINE ?>depense/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
          <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour aux dépenses
        </a>
      </div>

      <!-- CARTE FORMULAIRE PRINCIPALE -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <form id="form-depense" action="<?= RACINE ?>depense/<?= $isEdit ? 'edit' : 'add' ?>" method="POST" enctype="multipart/form-data" style="width: 100%;">
          <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
          <?php if ($isEdit): ?>
            <input type="hidden" name="id_depense" value="<?= $item['id_depense'] ?>">
          <?php endif; ?>

          <!-- BLOC 1 : TYPE ET MONTANT -->
          <div style="margin-bottom: 24px;">
            <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
              <i data-lucide="tags" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Étape 1 : Catégorie & Montant Engagé
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Catégorie de Dépense <span style="color: #EF4444;">*</span></label>
                <select name="type_depense_code" class="form-control select2" style="width: 100%; box-sizing: border-box;" required>
                  <option value="">-- Sélectionner le type de dépense --</option>
                  <?php foreach ($typeDepenses as $td): ?>
                    <option value="<?= $td['code_type_depense'] ?>" <?= ($item['type_depense_code'] ?? '') === $td['code_type_depense'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($td['libelle_type_depense']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Montant Total (FCFA) <span style="color: #EF4444;">*</span></label>
                <input type="number" name="montant_depense" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-weight: 800; color: #DC2626; outline: none;" value="<?= htmlspecialchars($item['montant_depense'] ?? '') ?>" required placeholder="Ex: 15000">
              </div>
            </div>
          </div>

          <!-- BLOC 2 : DATE & DESCRIPTION -->
          <div style="margin-bottom: 24px;">
            <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
              <i data-lucide="file-text" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Étape 2 : Date & Description du Frais
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Date de la Dépense <span style="color: #EF4444;">*</span></label>
                <input type="date" name="date_depense" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" value="<?= htmlspecialchars($item['date_depense'] ?? date('Y-m-d')) ?>" required>
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Mode de Règlement</label>
                <select name="mode_reglement" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;">
                  <option value="espece" <?= ($item['mode_reglement'] ?? 'espece') === 'espece' ? 'selected' : '' ?>>Espèce (Caisse)</option>
                  <option value="mobile_money" <?= ($item['mode_reglement'] ?? '') === 'mobile_money' ? 'selected' : '' ?>>Mobile Money</option>
                  <option value="virement" <?= ($item['mode_reglement'] ?? '') === 'virement' ? 'selected' : '' ?>>Virement / Chèque</option>
                </select>
              </div>

              <div class="form-group" style="grid-column: 1 / -1;">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Motif / Description Détaillée <span style="color: #EF4444;">*</span></label>
                <textarea name="description_depense" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" rows="3" required placeholder="Description explicative du besoin ou de la facture..."><?= htmlspecialchars($item['description_depense'] ?? ($item['motif_depense'] ?? '')) ?></textarea>
              </div>

              <div class="form-group" style="grid-column: 1 / -1;">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Pièce Justificative (Facture, Reçu)</label>
                <input type="file" name="piece_justificative" class="form-control" style="width: 100%; box-sizing: border-box; padding: 9px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" accept="image/*,.pdf">
              </div>
            </div>
          </div>

          <!-- BOUTONS D'ACTION -->
          <div style="display: flex; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #E2E8F0; width: 100%;">
            <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 8px; padding: 10px 24px; display: inline-flex; align-items: center; gap: 8px;">
              <i data-lucide="check" style="width: 18px; height: 18px;"></i> <?= $isEdit ? 'Enregistrer les modifications' : 'Valider la Dépense' ?>
            </button>
            <a href="<?= RACINE ?>depense/list" class="btn btn-secondary" style="font-weight: 600; border-radius: 8px; padding: 10px 24px; text-decoration: none;">Annuler</a>
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

  $('#form-depense').on('submit', function(e) {
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
          setTimeout(function() { window.location.href = '<?= RACINE ?>depense/list'; }, 1000);
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
