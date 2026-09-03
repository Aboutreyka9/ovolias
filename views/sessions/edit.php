<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$isEdit = !empty($item['id_session']);
$title = $isEdit ? 'Éditer la Session' : 'Nouvelle Session d\'Activité';
$annees = $annees ?? [];
$zones = $zones ?? [];
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
            <i data-lucide="clock" style="color: #1E3A5F; width: 26px; height: 26px;"></i>
            <span><?= $title ?></span>
          </h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Configuration des paramètres de la campagne et session de souscription</p>
        </div>
        <a href="<?= RACINE ?>session/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
          <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour aux sessions
        </a>
      </div>

      <!-- CARTE FORMULAIRE PRINCIPALE -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <form id="form-session" action="<?= RACINE ?>session/<?= $isEdit ? 'edit' : 'add' ?>" method="POST" style="width: 100%;">
          <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
          <?php if ($isEdit): ?>
            <input type="hidden" name="id_session" value="<?= $item['id_session'] ?>">
          <?php endif; ?>

          <!-- BLOC 1 : IDENTIFICATION DE LA SESSION -->
          <div style="margin-bottom: 24px;">
            <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
              <i data-lucide="info" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Identification de la session
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Libellé de la Session <span style="color: #EF4444;">*</span></label>
                <input type="text" name="libelle_session" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-weight: 700; color: #0F172A; outline: none;" value="<?= htmlspecialchars($item['libelle_session'] ?? '') ?>" required placeholder="Ex: Session Noël 2026">
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Année d'activité <span style="color: #EF4444;">*</span></label>
                <select name="annee_code" class="form-control select2" style="width: 100%; box-sizing: border-box;" required>
                  <option value="">-- Sélectionner l'année --</option>
                  <?php foreach ($annees as $a): ?>
                    <option value="<?= $a['code_annee'] ?>" <?= ($item['annee_code'] ?? '') === $a['code_annee'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($a['libelle_annee']) ?> (Code: <?= htmlspecialchars($a['code_annee']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Zone Commerciale / Géographique <span style="color: #64748B; font-weight: 500; font-size: 12px;">(Optionnel)</span></label>
                <select name="zone_code" class="form-control select2" style="width: 100%; box-sizing: border-box;">
                  <option value="">-- Aucune zone (Session Globale / Super Admin) --</option>
                  <?php foreach ($zones as $z): ?>
                    <option value="<?= htmlspecialchars($z['code_zone']) ?>" <?= ($item['zone_code'] ?? '') === $z['code_zone'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($z['libelle_zone']) ?> (Code: <?= htmlspecialchars($z['code_zone']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
                <small style="color: #64748B; font-size: 11px; margin-top: 4px; display: block;">Laissez vide si cette session s'applique à l'ensemble du réseau ou au niveau Super Admin.</small>
              </div>
            </div>
          </div>

          <!-- BLOC 2 : DURÉE & PLANIFICATION -->
          <div style="margin-bottom: 24px;">
            <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
              <i data-lucide="calendar" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Durée & Planification de la session
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Date de Début <span style="color: #EF4444;">*</span></label>
                <input type="date" name="date_debut_session" id="date-debut-session" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" value="<?= htmlspecialchars($item['date_debut_session'] ?? '') ?>" required>
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Date de Fin Prévue <span style="color: #EF4444;">*</span></label>
                <input type="date" name="date_fin_session" id="date-fin-session" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" value="<?= htmlspecialchars($item['date_fin_session'] ?? '') ?>" required>
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Nombre Total de Jours <span style="color: #EF4444;">*</span></label>
                <input type="number" name="nombre_jour_session" id="nombre-jour-session" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-weight: 800; color: #1E3A5F; outline: none;" value="<?= htmlspecialchars($item['nombre_jour_session'] ?? '170') ?>" required min="1" placeholder="Ex: 170">
                <small style="color: #64748B; font-size: 11px; margin-top: 4px; display: block;">Calculé automatiquement selon les dates, modifiable si besoin.</small>
              </div>
            </div>
          </div>

          <!-- BOUTONS D'ACTION -->
          <div style="display: flex; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #E2E8F0; width: 100%;">
            <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 8px; padding: 10px 24px; display: inline-flex; align-items: center; gap: 8px;">
              <i data-lucide="check" style="width: 18px; height: 18px;"></i> <?= $isEdit ? 'Enregistrer les modifications' : 'Créer la Session' ?>
            </button>
            <a href="<?= RACINE ?>session/list" class="btn btn-secondary" style="font-weight: 600; border-radius: 8px; padding: 10px 24px; text-decoration: none;">Annuler</a>
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

  function calculerNombreJours() {
    var debut = $('#date-debut-session').val();
    var fin = $('#date-fin-session').val();
    if (debut && fin) {
      var d1 = new Date(debut);
      var d2 = new Date(fin);
      var diff = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24)) + 1;
      if (diff > 0) {
        $('#nombre-jour-session').val(diff);
      } else {
        $('#nombre-jour-session').val('');
      }
    }
  }

  $('#date-debut-session, #date-fin-session').on('change', calculerNombreJours);
});
</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
