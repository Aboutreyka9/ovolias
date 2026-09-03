
<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$isEdit = !empty($item['id_cautisation_client']);
$title = $isEdit ? 'Éditer la Cotisation' : 'Saisie d\'une Cotisation Client';
$souscriptions = $souscriptions ?? [];
$commerciaux = $commerciaux ?? [];
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
            <i data-lucide="wallet" style="color: #047857; width: 26px; height: 26px;"></i>
            <span><?= $title ?></span>
          </h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Enregistrement et encaissement d'une cotisation quotidienne client sur le terrain</p>
        </div>
<a href="<?= RACINE ?>cotisation/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
          <i data-lucide="list" style="width: 16px; height: 16px;"></i> Liste des Cotisations
        </a>
      </div>

      <!-- CARTE FORMULAIRE PRINCIPALE -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <form id="form-cotisation" action="<?= RACINE ?>cotisation/<?= $isEdit ? 'edit' : 'add' ?>" method="POST" enctype="multipart/form-data" style="width: 100%;">
          <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
          <?php if ($isEdit): ?>
            <input type="hidden" name="id_cautisation_client" value="<?= $item['id_cautisation_client'] ?>">
          <?php endif; ?>

          <!-- BLOC 1 : IDENTIFICATION DU CONTRAT -->
          <div style="margin-bottom: 24px;">
            <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
              <i data-lucide="file-text" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Étape 1 : Contrat de Souscription Client
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
              <div class="form-group" style="grid-column: 1 / -1;">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Souscription Client Concernée <span style="color: #EF4444;">*</span></label>
                <select name="souscription_code" id="select-souscription" class="form-control select2" style="width: 100%; box-sizing: border-box;" required <?= $isEdit ? 'disabled' : '' ?>>
                  <option value="">-- Rechercher une souscription par client ou pack --</option>
                  <?php foreach ($souscriptions as $s): ?>
                    <option value="<?= $s['code_souscription'] ?>" 
                            data-cotis="<?= $s['montant_cotisation_journaliere'] ?? 1000 ?>"
                            <?= ($item['souscription_code'] ?? '') === $s['code_souscription'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars(trim($s['nom_client'] ?? '')) ?> - Pack <?= htmlspecialchars($s['libelle_pack'] ?? 'Pack') ?> (Réf: <?= $s['code_souscription'] ?> - <?= number_format((float)($s['montant_cotisation_journaliere'] ?? 1000), 0, ',', ' ') ?> F/j)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <!-- BLOC 2 : MONTANTS ET ÉCHÉANCE -->
          <div style="margin-bottom: 24px;">
            <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
              <i data-lucide="coins" style="width: 16px; height: 16px; color: #047857;"></i> Étape 2 : Montant & Nombre de Jours
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Montant Reçu (FCFA) <span style="color: #EF4444;">*</span></label>
                <input type="number" name="montant_cautisation" id="input-montant" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-weight: 800; color: #047857; outline: none;" value="<?= htmlspecialchars($item['montant_cautisation'] ?? '') ?>" required placeholder="Ex: 1000">
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Nombre de Jours Payés</label>
                <input type="number" name="nombre_jour_paye" id="input-nb-jours" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-weight: 700; color: #1E3A5F; outline: none;" value="<?= htmlspecialchars($item['nombre_jour_paye'] ?? '1') ?>" min="1" placeholder="Calculé auto">
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Date de la Cotisation <span style="color: #EF4444;">*</span></label>
                <input type="date" name="date_cautisation" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" value="<?= htmlspecialchars($item['date_cautisation'] ?? date('Y-m-d')) ?>" required>
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Commercial Encaisseur</label>
                <select name="commercial_code" class="form-control select2" style="width: 100%; box-sizing: border-box;">
                  <option value="">-- Sélectionner l'agent / commercial --</option>
                  <?php foreach ($commerciaux as $c): ?>
                    <option value="<?= $c['code_user'] ?>" <?= ($item['commercial_code'] ?? '') === $c['code_user'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars(trim(($c['nom_user'] ?? '') . ' ' . ($c['prenom_user'] ?? ''))) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <!-- BLOC 3 : MODE & PREUVE DE PAIEMENT -->
          <div style="margin-bottom: 24px;">
            <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
              <i data-lucide="receipt" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Étape 3 : Mode & Preuve de Paiement
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Mode de Règlement <span style="color: #EF4444;">*</span></label>
                <select name="mode_paiement" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;">
                  <option value="espece" <?= ($item['mode_paiement'] ?? 'espece') === 'espece' ? 'selected' : '' ?>>Espèce (Comptant)</option>
                  <option value="mobile_money" <?= ($item['mode_paiement'] ?? '') === 'mobile_money' ? 'selected' : '' ?>>Mobile Money (Wave, Orange, MTN)</option>
                  <option value="virement" <?= ($item['mode_paiement'] ?? '') === 'virement' ? 'selected' : '' ?>>Virement Bancaire</option>
                </select>
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">N° Référence Transaction</label>
                <input type="text" name="reference_paiement" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" value="<?= htmlspecialchars($item['reference_paiement'] ?? '') ?>" placeholder="Ex: TXN12345678">
              </div>

              <div class="form-group" style="grid-column: 1 / -1;">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Photo du Reçu / Preuve (Image)</label>
                <input type="file" name="photo_recu" class="form-control" style="width: 100%; box-sizing: border-box; padding: 9px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" accept="image/*">
              </div>
            </div>
          </div>

          <!-- BOUTONS D'ACTION -->
          <div style="display: flex; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #E2E8F0; width: 100%;">
            <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 8px; padding: 10px 24px; display: inline-flex; align-items: center; gap: 8px;">
              <i data-lucide="check" style="width: 18px; height: 18px;"></i> <?= $isEdit ? 'Enregistrer les modifications' : 'Valider la Cotisation' ?>
            </button>
            <a href="<?= RACINE ?>cotisation/list" class="btn btn-secondary" style="font-weight: 600; border-radius: 8px; padding: 10px 24px; text-decoration: none;">Annuler</a>
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

  $('#select-souscription').on('change select2:select', function() {
    var selectedOpt = $(this).find('option:selected');
    var cotisUnit = parseFloat(selectedOpt.data('cotis')) || 1000;
    var montant = parseFloat($('#input-montant').val()) || 0;
    if (montant > 0 && cotisUnit > 0) {
      $('#input-nb-jours').val(Math.max(1, Math.round(montant / cotisUnit)));
    }
  });

  $('#input-montant').on('input', function() {
    var selectedOpt = $('#select-souscription').find('option:selected');
    var cotisUnit = parseFloat(selectedOpt.data('cotis')) || 1000;
    var montant = parseFloat($(this).val()) || 0;
    if (cotisUnit > 0) {
      $('#input-nb-jours').val(Math.max(1, Math.round(montant / cotisUnit)));
    }
  });

  $('#form-cotisation').on('submit', function(e) {
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
          setTimeout(function() { window.location.href = '<?= RACINE ?>cotisation/list'; }, 1000);
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
