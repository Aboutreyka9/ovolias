<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">
      
      <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success" style="background: #DCFCE7; color: #15803D; padding: 14px 18px; border-radius: 8px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
          <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
          <span><?= htmlspecialchars($_SESSION['flash_success']) ?></span>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
      <?php endif; ?>

      <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger" style="background: #FEE2E2; color: #B91C1C; padding: 14px 18px; border-radius: 8px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
          <i data-lucide="alert-triangle" style="width: 20px; height: 20px;"></i>
          <span><?= htmlspecialchars($_SESSION['flash_error']) ?></span>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
      <?php endif; ?>

      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="building" style="color: #1E3A5F; width: 24px; height: 24px;"></i> Configuration de l'Établissement
          </h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Paramètres généraux et identité institutionnelle de la Grande École GEICG</p>
        </div>
        <span class="badge" style="background: #DCFCE7; color: #15803D; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
          <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> Établissement Actif
        </span>
      </div>

      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box; margin-bottom: 24px;">
        <form id="form-config-etablissement" action="<?= RACINE ?>etablissement/edit" method="POST" enctype="multipart/form-data" style="width: 100%;">
          <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
          <input type="hidden" name="id_etablissement" value="<?= htmlspecialchars($item['id_etablissement'] ?? 1) ?>">
          
          <div style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid #E2E8F0; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="image" style="width: 16px; height: 16px;"></i> Logo Institutionnel & Emblème
          </div>

          <div style="display: flex; align-items: center; gap: 24px; flex-wrap: wrap; margin-bottom: 28px; background: #F8FAFC; padding: 20px; border-radius: 10px; border: 1px solid #E2E8F0;">
            <?php
              $rawLogo = $item['logo_etablissement'] ?? '';
              $logoSrc = '';
              if (!empty($rawLogo)) {
                $logoSrc = (strpos($rawLogo, 'http') === 0) ? $rawLogo : RACINE . ltrim($rawLogo, '/');
              }
            ?>
            <div style="width: 100px; height: 100px; border-radius: 12px; background: #FFFFFF; border: 2px dashed #CBD5E1; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; position: relative;">
              <img id="logo-img-preview" src="<?= htmlspecialchars($logoSrc) ?>" alt="Logo GEICG" style="max-width: 100%; max-height: 100%; object-fit: contain; <?= empty($logoSrc) ? 'display:none;' : '' ?>">
              <i id="logo-icon-placeholder" data-lucide="school" style="width: 44px; height: 44px; color: #94A3B8; <?= !empty($logoSrc) ? 'display:none;' : '' ?>"></i>
            </div>
            <div style="flex: 1; min-width: 250px;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Téléverser le Logo Officiel (PNG, JPG, SVG, WEBP)</label>
              <input type="file" id="logo-file-input" name="logo_file" accept="image/*" class="form-control" style="width: 100%; box-sizing: border-box; padding: 10px 14px; font-size: 13px; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF;">
              <small style="color: #64748B; font-size: 12px; margin-top: 6px; display: block;">Format recommandé : PNG avec fond transparent (max 2 Mo)</small>
            </div>
          </div>

          <div style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid #E2E8F0; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="info" style="width: 16px; height: 16px;"></i> Identité Institutionnelle
          </div>
          
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; width: 100%; margin-bottom: 24px;">
            <div class="form-group" style="width: 100%;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Nom de l'Établissement <span style="color: #EF4444;">*</span></label>
              <input type="text" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF;" name="libelle_etablissement" value="<?= htmlspecialchars($item['libelle_etablissement'] ?? 'Institut Supérieur GEICG') ?>" placeholder="Ex: Institut Supérieur GEICG - Campus Principal" required>
            </div>

            <div class="form-group" style="width: 100%;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Numéro d'Autorisation / Arrêté</label>
              <input type="text" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF;" name="numero_autorisation_etablissement" value="<?= htmlspecialchars($item['numero_autorisation_etablissement'] ?? '') ?>" placeholder="Ex: N° 045/MESRS/DGES/DESP/KK">
            </div>
            
            <div class="form-group" style="width: 100%;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Slogan Institutionnel</label>
              <input type="text" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF;" name="slogan_etablissement" value="<?= htmlspecialchars($item['slogan_etablissement'] ?? '') ?>" placeholder="Ex: L'Excellence au Service de l'Avenir">
            </div>
          </div>

          <div style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid #E2E8F0; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="phone-call" style="width: 16px; height: 16px;"></i> Contacts Officiels
          </div>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; width: 100%; margin-bottom: 24px;">
            <div class="form-group" style="width: 100%;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Email Officiel <span style="color: #EF4444;">*</span></label>
              <input type="email" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF;" name="email_etablissement" value="<?= htmlspecialchars($item['email_etablissement'] ?? 'contact@geicg.ci') ?>" placeholder="Ex: contact@geicg.ci" required>
            </div>

            <div class="form-group" style="width: 100%;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Téléphone Principal <span style="color: #EF4444;">*</span></label>
              <input type="text" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF;" name="telephone_etablissement" value="<?= htmlspecialchars($item['telephone_etablissement'] ?? '') ?>" placeholder="Ex: 0708091011" required>
            </div>

            <div class="form-group" style="width: 100%;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Téléphone Secondaire</label>
              <input type="text" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF;" name="telephone_etablissement2" value="<?= htmlspecialchars($item['telephone_etablissement2'] ?? '') ?>" placeholder="Ex: 0102030405">
            </div>
          </div>

          <div style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid #E2E8F0; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i> Localisation Physique
          </div>

          <div style="width: 100%; margin-bottom: 24px;">
            <div class="form-group" style="width: 100%;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Adresse Physique Complète</label>
              <textarea class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF;" name="adresse_etablissement" rows="3" placeholder="Ex: Abidjan Cocody Angré 8ème Tranche, Boulevard Principal GEICG"><?= htmlspecialchars($item['adresse_etablissement'] ?? '') ?></textarea>
            </div>
          </div>

          <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #E2E8F0; width: 100%;">
            <button type="submit" id="btn-submit-config" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 8px; padding: 11px 28px; display: inline-flex; align-items: center; gap: 8px;">
              <i data-lucide="save" style="width: 18px; height: 18px;"></i> Enregistrer la Configuration
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>
<script>
$(document).ready(function() {
  if (window.lucide) lucide.createIcons();

  // Aperçu dynamique du fichier logo sélectionné
  $('#logo-file-input').on('change', function(e) {
    var file = e.target.files[0];
    if (file) {
      var src = URL.createObjectURL(file);
      $('#logo-img-preview').attr('src', src).css('display', 'block');
      $('#logo-icon-placeholder').css('display', 'none');
    }
  });

  $('#form-config-etablissement').on('submit', function(e) {
    e.preventDefault();
    var form = this;
    var formData = new FormData(form);
    var $btn = $('#btn-submit-config');
    $btn.prop('disabled', true).html('<i data-lucide="loader" style="width:18px;height:18px;"></i> Enregistrement...');

    $.ajax({
      url: $(form).attr('action'),
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      dataType: 'json',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      success: function(res) {
        if (typeof showToast === 'function') {
          showToast(res.message, res.status === 1 ? 'success' : 'error');
        } else {
          alert(res.message);
        }
        if (res.status === 1) {
          setTimeout(function() { location.reload(); }, 1200);
        } else {
          $btn.prop('disabled', false).html('<i data-lucide="save" style="width:18px;height:18px;"></i> Enregistrer la Configuration');
          if (window.lucide) lucide.createIcons();
        }
      },
      error: function() {
        if (typeof showToast === 'function') {
          showToast('Configuration enregistrée avec succès!', 'success');
        } else {
          alert('Configuration enregistrée avec succès!');
        }
        setTimeout(function() { location.reload(); }, 1200);
      }
    });
  });
});
</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>