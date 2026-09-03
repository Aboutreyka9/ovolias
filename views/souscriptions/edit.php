<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$isEdit = !empty($item['id_souscription']);
$title = $isEdit ? 'Éditer la Souscription' : 'Nouvelle Souscription Client';
$clients = $clients ?? [];
$packs = $packs ?? [];
$sessions = $sessions ?? [];
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
            <i data-lucide="file-text" style="color: #1E3A5F; width: 26px; height: 26px;"></i>
            <span><?= $title ?></span>
          </h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Création ou modification d'un contrat de souscription de pack d'articles</p>
        </div>
        <a href="<?= RACINE ?>souscription/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
          <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour aux souscriptions
        </a>
      </div>

      <!-- CARTE FORMULAIRE PRINCIPALE -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <form id="form-souscription" action="<?= RACINE ?>souscription/<?= $isEdit ? 'edit' : 'add' ?>" method="POST" style="width: 100%;">
          <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
          <?php if ($isEdit): ?>
            <input type="hidden" name="id_souscription" value="<?= $item['id_souscription'] ?>">
          <?php endif; ?>

          <!-- BLOC 1 : SÉLECTION DU CLIENT & DU PACK -->
          <div style="margin-bottom: 24px;">
            <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
              <i data-lucide="user-check" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Étape 1 : Choix du Client & du Pack Produit
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Client Souscripteur <span style="color: #EF4444;">*</span></label>
                <select name="client_code" class="form-control select2" style="width: 100%; box-sizing: border-box;" required <?= $isEdit ? 'disabled' : '' ?>>
                  <option value="">-- Rechercher un client par nom ou tel --</option>
                  <?php foreach ($clients as $c): ?>
                    <option value="<?= $c['code_client'] ?>" <?= ($item['client_code'] ?? '') === $c['code_client'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars(trim($c['nom_client'])) ?> (<?= htmlspecialchars($c['telephone_client']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Pack d'Articles Choisis <span style="color: #EF4444;">*</span></label>
                <select name="pack_code" id="select-pack" class="form-control select2" style="width: 100%; box-sizing: border-box;" required <?= $isEdit ? 'disabled' : '' ?>>
                  <option value="">-- Sélectionner un pack --</option>
                  <?php foreach ($packs as $p): ?>
                    <option value="<?= $p['code_pack'] ?>" 
                            data-prix="<?= $p['prix_cotisation_pack'] ?>" 
                            data-jours="<?= $p['nombre_jour_pack'] ?>"
                            <?= ($item['pack_code'] ?? '') === $p['code_pack'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($p['libelle_pack']) ?> (<?= number_format($p['prix_cotisation_pack'], 0, ',', ' ') ?> FCFA/j - <?= $p['nombre_jour_pack'] ?> jours)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <!-- BLOC 2 : MODALITÉS DE COTISATION -->
          <div style="margin-bottom: 24px;">
            <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
              <i data-lucide="calculator" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Étape 2 : Échéancier & Cotisation Journalière
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Cotisation Journalière (FCFA) <span style="color: #EF4444;">*</span></label>
                <input type="number" name="montant_cotisation_journaliere" id="input-cotis" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-weight: 700; color: #047857; outline: none;" value="<?= htmlspecialchars($item['montant_cotisation_journaliere'] ?? '') ?>" required placeholder="Ex: 1000">
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Durée Totale (en Nombre de Jours) <span style="color: #EF4444;">*</span></label>
                <input type="number" name="nombre_jour_total" id="input-jours" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-weight: 700; color: #1E3A5F; outline: none;" value="<?= htmlspecialchars($item['nombre_jour_total'] ?? '170') ?>" required placeholder="Ex: 170">
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Date de Démarrage <span style="color: #EF4444;">*</span></label>
                <input type="date" name="date_debut_souscription" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" value="<?= htmlspecialchars($item['date_debut_souscription'] ?? date('Y-m-d')) ?>" required>
              </div>

              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Session d'Activité</label>
                <select name="session_code" class="form-control select2" style="width: 100%; box-sizing: border-box;">
                  <option value="">-- Session par défaut --</option>
                  <?php foreach ($sessions as $s): ?>
                    <option value="<?= $s['code_session'] ?>" <?= ($item['session_code'] ?? '') === $s['code_session'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($s['libelle_session']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <!-- BLOC 3 : STATUT ET VALIDATION -->
          <div style="margin-bottom: 24px;">
            <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
              <i data-lucide="check-circle-2" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Étape 3 : Statut du Contrat
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
              <div class="form-group">
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Statut de la Souscription <span style="color: #EF4444;">*</span></label>
                 <select name="statut_souscription" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;">
                   <option value="valide" <?= ($item['statut_souscription'] ?? 'valide') === 'valide' ? 'selected' : '' ?>>Validée (En cours)</option>
                   <option value="solde" <?= ($item['statut_souscription'] ?? '') === 'solde' ? 'selected' : '' ?>>Soldée</option>
                   <option value="annule" <?= ($item['statut_souscription'] ?? '') === 'annule' ? 'selected' : '' ?>>Annulée</option>
                   <option value="reconduite" <?= ($item['statut_souscription'] ?? '') === 'reconduite' ? 'selected' : '' ?>>Reconduite</option>
                 </select>
              </div>
            </div>
          </div>

          <!-- BOUTONS D'ACTION -->
          <div style="display: flex; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #E2E8F0; width: 100%;">
            <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 8px; padding: 10px 24px; display: inline-flex; align-items: center; gap: 8px;">
              <i data-lucide="check" style="width: 18px; height: 18px;"></i> <?= $isEdit ? 'Enregistrer les modifications' : 'Valider la Souscription' ?>
            </button>
            <a href="<?= RACINE ?>souscription/list" class="btn btn-secondary" style="font-weight: 600; border-radius: 8px; padding: 10px 24px; text-decoration: none;">Annuler</a>
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

  $('#select-pack').on('change select2:select', function() {
    var selectedOpt = $(this).find('option:selected');
    var prix = selectedOpt.data('prix');
    var jours = selectedOpt.data('jours');
    if (prix) $('#input-cotis').val(prix);
    if (jours) $('#input-jours').val(jours);
  });

  $('#form-souscription').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(res) {
        if (res.status === 1 || res.success) {
          if (window.toastr) toastr.success(res.message || 'Opération réussie');
          setTimeout(function() { window.location.href = '<?= RACINE ?>souscription/list'; }, 1000);
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
