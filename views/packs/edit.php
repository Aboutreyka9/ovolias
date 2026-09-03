<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$isEdit = !empty($item['id_pack']);
$title = $isEdit ? 'Éditer le Pack' : 'Nouveau Pack Produit';
$categories = $categories ?? [];
$sessions = $sessions ?? [];
$zones = $zones ?? [];
$articles = $articles ?? [];
$packArticles = $packArticles ?? [];
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
            <i data-lucide="boxes" style="color: #1E3A5F; width: 26px; height: 26px;"></i>
            <span><?= $title ?></span>
          </h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Configuration du pack en 3 étapes</p>
        </div>
        <a href="<?= RACINE ?>pack/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
          <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour aux packs
        </a>
      </div>

      <!-- INDICATEUR D'ÉTAPES -->
      <div style="display: flex; gap: 8px; margin-bottom: 24px; align-items: center;">
        <div id="step-indicator-1" class="step-indicator active" style="flex: 1; padding: 12px; text-align: center; background: #1E3A5F; color: #FFF; border-radius: 8px; font-weight: 700; font-size: 13px;">Étape 1 : Désignation & Montant</div>
        <div style="color: #94A3B8; font-size: 18px;">→</div>
        <div id="step-indicator-2" class="step-indicator" style="flex: 1; padding: 12px; text-align: center; background: #F1F5F9; color: #64748B; border-radius: 8px; font-weight: 700; font-size: 13px;">Étape 2 : Composants du Pack</div>
        <div style="color: #94A3B8; font-size: 18px;">→</div>
        <div id="step-indicator-3" class="step-indicator" style="flex: 1; padding: 12px; text-align: center; background: #F1F5F9; color: #64748B; border-radius: 8px; font-weight: 700; font-size: 13px;">Étape 3 : Articles du Pack <span id="article-count" style="font-weight: 700; font-size: 12px;">(0)</span></div>
      </div>

      <!-- CARTE FORMULAIRE PRINCIPALE -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <div id="form-messages" style="display: none; margin-bottom: 20px; padding: 12px 16px; border-radius: 8px; font-weight: 600; font-size: 14px;"></div>
        <form id="form-pack" action="<?= RACINE ?>pack/<?= $isEdit ? 'edit' : 'add' ?>" method="POST" enctype="multipart/form-data" style="width: 100%;">
          <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
          <?php if ($isEdit): ?>
            <input type="hidden" name="id_pack" value="<?= $item['id_pack'] ?>">
          <?php endif; ?>

          <!-- ÉTAPE 1 : DESIGNATION & MONTANT -->
          <div id="step-1" class="form-step">
            <div style="margin-bottom: 24px;">
              <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
                <i data-lucide="tag" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Étape 1 : Désignation & Montant
              </h3>
              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group" style="grid-column: span 2;">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Désignation / Nom du Pack <span style="color: #EF4444;">*</span></label>
                  <input type="text" name="libelle_pack" id="libelle_pack" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" value="<?= htmlspecialchars($item['libelle_pack'] ?? '') ?>" required placeholder="Ex: Pack Noël Famille Prestige">
                </div>

                <div class="form-group">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Cotisation / Jour (FCFA) <span style="color: #EF4444;">*</span></label>
                  <input type="number" name="prix_cotisation_pack" id="prix_cotisation_pack" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-weight: 800; color: #047857; outline: none;" value="<?= htmlspecialchars($item['prix_cotisation_pack'] ?? '0') ?>" required placeholder="Ex: 1000" min="0">
                </div>

                <div class="form-group">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Image / Visuel du Pack</label>
                  <input type="file" name="image_pack" id="image_pack" class="form-control" style="width: 100%; box-sizing: border-box; padding: 9px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" accept="image/*">
                  
                  <div id="preview-pack-wrapper" style="margin-top: 12px; <?= !empty($item['image_pack'] ?? '') ? '' : 'display: none;' ?>">
                    <div style="position: relative; display: inline-block; padding: 6px; background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 10px;">
                      <img id="preview-pack-image" src="<?= !empty($item['image_pack'] ?? '') ? RACINE . 'public/assets/images/packs/' . htmlspecialchars($item['image_pack']) : '' ?>" style="max-height: 110px; max-width: 100%; border-radius: 8px; object-fit: cover; display: block;">
                      <span id="preview-pack-badge" style="position: absolute; top: 12px; left: 12px; font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 6px; background: #1E3A5F; color: #FFFFFF;">
                        <?= !empty($item['image_pack'] ?? '') ? 'Visuel actuel' : 'Prévisualisation' ?>
                      </span>
                      <button type="button" id="btn-remove-pack-image" style="position: absolute; top: 10px; right: 10px; background: #EF4444; color: white; border: none; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.2); font-weight: bold;" title="Annuler / Retirer l'image">
                        &times;
                      </button>
                    </div>
                    <div id="preview-pack-info" style="font-size: 12px; color: #64748B; margin-top: 6px; font-weight: 600;">
                      <?= !empty($item['image_pack'] ?? '') ? htmlspecialchars($item['image_pack']) : '' ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #E2E8F0;">
              <button type="button" id="btn-step-1-next" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 8px; padding: 10px 24px; display: inline-flex; align-items: center; gap: 8px;">
                Continuer <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
              </button>
            </div>
          </div>

          <!-- ÉTAPE 2 : COMPOSANTS DU PACK -->
          <div id="step-2" class="form-step" style="display: none;">
            <div style="margin-bottom: 24px;">
              <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
                <i data-lucide="settings" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Étape 2 : Composants du Pack
              </h3>
              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                <div class="form-group">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Libellé Session <span style="color: #EF4444;">*</span></label>
                  <select name="session_code" id="session_code" class="form-control select2" style="width: 100%; box-sizing: border-box;" required>
                    <option value="">-- Sélectionner une session --</option>
                    <?php foreach ($sessions as $s): ?>
                      <option value="<?= $s['code_session'] ?>" data-jours="<?= (int)($s['nombre_jour_session'] ?? 0) ?>" <?= ($item['session_code'] ?? '') === $s['code_session'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['libelle_session']) ?> (<?= (int)($s['nombre_jour_session'] ?? 0) ?> jours)
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="form-group">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Libellé Catégorie <span style="color: #EF4444;">*</span></label>
                  <select name="categorie_pack_code" id="categorie_pack_code" class="form-control select2" style="width: 100%; box-sizing: border-box;" required>
                    <option value="">-- Sélectionner une catégorie --</option>
                    <?php foreach ($categories as $cat): ?>
                      <option value="<?= $cat['code_categorie_pack'] ?>" <?= ($item['categorie_pack_code'] ?? '') === $cat['code_categorie_pack'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['libelle_categorie_pack']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="form-group">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Zone <span style="color: #EF4444;">*</span></label>
                  <select name="zone_code" id="zone_code" class="form-control select2" style="width: 100%; box-sizing: border-box;" required>
                    <option value="">-- Sélectionner une zone --</option>
                    <?php foreach ($zones as $z): ?>
                      <option value="<?= $z['code_zone'] ?>" <?= ($item['zone_code'] ?? '') === $z['code_zone'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($z['libelle_zone']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="form-group">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Nombre de Jours</label>
                  <input type="number" name="nombre_jour_pack" id="nombre_jour_pack" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-weight: 800; color: #1E3A5F; outline: none; background: #F8FAFC;" value="<?= htmlspecialchars($item['nombre_jour_pack'] ?? '') ?>" readonly>
                </div>

                <div class="form-group">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Montant Total (FCFA)</label>
                  <input type="text" id="montant_pack_display" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; font-weight: 800; color: #1E3A5F; outline: none; background: #F8FAFC;" value="0" readonly>
                  <input type="hidden" name="montant_pack" id="montant_pack" value="0">
                </div>
              </div>
            </div>

            <div style="display: flex; justify-content: space-between; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #E2E8F0;">
              <button type="button" id="btn-step-2-prev" class="btn btn-secondary" style="font-weight: 600; border-radius: 8px; padding: 10px 24px; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Précédent
              </button>
              <button type="button" id="btn-step-2-next" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 8px; padding: 10px 24px; display: inline-flex; align-items: center; gap: 8px;">
                Continuer <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
              </button>
            </div>
          </div>

          <!-- ÉTAPE 3 : SÉLECTION DES ARTICLES -->
          <div id="step-3" class="form-step" style="display: none;">
            <div style="margin-bottom: 24px;">
              <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
                <i data-lucide="shopping-bag" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Étape 3 : Articles du Pack
              </h3>

              <div style="display: flex; gap: 12px; align-items: flex-end; margin-bottom: 16px;">
                <div style="flex: 1;">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Sélectionner un article</label>
                  <select id="article-select" class="form-control select2" style="width: 100%; box-sizing: border-box;">
                    <option value="">-- Choisir un article --</option>
                    <?php foreach ($articles as $art): ?>
                      <option value="<?= $art['code_article'] ?>" data-libelle="<?= htmlspecialchars($art['libelle_article'], ENT_QUOTES) ?>">
                        <?= htmlspecialchars($art['libelle_article']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <button type="button" id="btn-add-article" class="btn btn-success" style="height: 42px; min-width: 42px; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; background: #15803D; border-color: #15803D;">
                  <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Ajouter
                </button>
              </div>

              <div style="width: 100%; overflow-x: auto;">
                <table id="table-articles-pack" class="table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                  <thead>
                    <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                      <th style="padding: 10px 12px;">Libellé Article</th>
                      <th style="padding: 10px 12px; text-align: center; width: 120px;">Quantité</th>
                      <th style="padding: 10px 12px; text-align: center; width: 80px;">Action</th>
                    </tr>
                  </thead>
                  <tbody id="articles-pack-body">
                    <?php foreach ($packArticles as $pa): ?>
                      <tr data-article-code="<?= htmlspecialchars($pa['article_code']) ?>">
                        <td style="padding: 10px 12px; font-weight: 700; color: #0F172A;"><?= htmlspecialchars($pa['libelle_article'] ?? $pa['article_code']) ?></td>
                        <td style="padding: 10px 12px; text-align: center;">
                          <input type="number" name="articles[<?= $pa['article_code'] ?>][quantite_article]" value="<?= (int)($pa['quantite_article'] ?? 1) ?>" min="1" style="width: 80px; padding: 6px 10px; border-radius: 6px; border: 1px solid #CBD5E1; text-align: center;">
                          <input type="hidden" name="articles[<?= $pa['article_code'] ?>][article_code]" value="<?= htmlspecialchars($pa['article_code']) ?>">
                        </td>
                        <td style="padding: 10px 12px; text-align: center;">
                          <button type="button" class="btn btn-sm remove-article-row" style="border-radius: 6px; font-weight: 600; background: #DC2626; border-color: #DC2626; color: #FFF;">
                            <i class="fa-solid fa-trash" data-lucide="trash" style="font-size: 13px; width: 14px; height: 14px;"></i>
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <?php if (empty($packArticles)): ?>
                  <p id="empty-articles-msg" style="color: #94A3B8; text-align: center; padding: 20px 0; font-style: italic;">Aucun article sélectionné pour le moment.</p>
                <?php endif; ?>
              </div>
            </div>

            <div style="display: flex; justify-content: space-between; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #E2E8F0;">
              <button type="button" id="btn-step-3-prev" class="btn btn-secondary" style="font-weight: 600; border-radius: 8px; padding: 10px 24px; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Précédent
              </button>
              <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 8px; padding: 10px 24px; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="check" style="width: 18px; height: 18px;"></i> <?= $isEdit ? 'Enregistrer les modifications' : 'Créer le Pack' ?>
              </button>
            </div>
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

  // Prévisualisation dynamique de l'image sélectionnée
  $('#image_pack').on('change', function(e) {
    var file = e.target.files[0];
    if (file) {
      if (!file.type.match('image.*')) {
        if (window.toastr) toastr.error("Veuillez sélectionner un fichier image valide (JPG, PNG, WEBP, GIF)");
        $(this).val('');
        $('#preview-pack-wrapper').hide();
        return;
      }
      var reader = new FileReader();
      reader.onload = function(evt) {
        $('#preview-pack-image').attr('src', evt.target.result);
        $('#preview-pack-badge').text('Nouvelle image sélectionnée').css({'background': '#2563EB', 'color': '#FFFFFF'});
        $('#preview-pack-info').text(file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)');
        $('#preview-pack-wrapper').show();
      };
      reader.readAsDataURL(file);
    }
  });

  $('#btn-remove-pack-image').on('click', function() {
    $('#image_pack').val('');
    $('#preview-pack-wrapper').hide();
    $('#preview-pack-image').attr('src', '');
  });

  if (typeof window.toastr === 'undefined' && typeof showToast === 'function') {
    window.toastr = {
      success: function(msg) { showToast(msg, 'success'); },
      error: function(msg) { showToast(msg, 'error'); },
      warning: function(msg) { showToast(msg, 'warning'); },
      info: function(msg) { showToast(msg, 'info'); }
    };
  }

  function showMessage(type, message) {
    var toastType = (type === 'danger') ? 'error' : type;
    if (window.toastr && typeof window.toastr[toastType] === 'function') {
      window.toastr[toastType](message);
    } else if (typeof showToast === 'function') {
      showToast(message, toastType);
    }
  }

  function hideMessage() {
    $('#form-messages').hide();
  }

  function showStep(step) {
    currentStep = step;
    $('.form-step').hide();
    $('#step-' + step).show();

    $('.step-indicator').each(function() {
      var idx = $(this).attr('id').split('-')[2];
      if (parseInt(idx) === step) {
        $(this).css({ 'background': '#1E3A5F', 'color': '#FFF' });
      } else {
        $(this).css({ 'background': '#F1F5F9', 'color': '#64748B' });
      }
    });

    hideMessage();
    if (window.lucide) lucide.createIcons();
    if (step === 3) {
      updateArticleCount();
    }
  }

  function updateArticleCount() {
    var count = $('#articles-pack-body tr').length;
    $('#article-count').text('(' + count + ')');
    if (count > 0) {
      $('#empty-articles-msg').hide();
    } else {
      $('#empty-articles-msg').show();
    }
  }

  var baseCotisPack = 0;

  function calculerMontantTotalEtape2() {
    baseCotisPack = parseFloat($('#prix_cotisation_pack').val() || '0');
    var jours = parseInt($('#nombre_jour_pack').val() || '0', 10);
    if (!jours && $('#session_code').val()) {
      jours = parseInt($('#session_code').find('option:selected').data('jours') || '0', 10);
      if (jours > 0) {
        $('#nombre_jour_pack').val(jours);
      }
    }
    var total = Math.round(jours * baseCotisPack);
    $('#montant_pack').val(total);
    $('#montant_pack_display').val(Number(total).toLocaleString('fr-FR') + ' FCFA');
  }

  $('#prix_cotisation_pack').on('input change', function() {
    calculerMontantTotalEtape2();
  });

  $('#session_code').on('change', function() {
    var jours = parseInt($(this).find('option:selected').data('jours') || '0', 10);
    $('#nombre_jour_pack').val(jours);
    calculerMontantTotalEtape2();
  });

  calculerMontantTotalEtape2();

  $('#btn-step-1-next').on('click', function() {
    var libelle = $('#libelle_pack').val().trim();
    var cotis = parseFloat($('#prix_cotisation_pack').val() || '0');
    if (!libelle) {
      showMessage('danger', 'Veuillez saisir la désignation du pack');
      $('#libelle_pack').focus();
      return;
    }
    if (cotis <= 0) {
      showMessage('danger', 'Veuillez saisir une cotisation journalière valide');
      $('#prix_cotisation_pack').focus();
      return;
    }
    baseCotisPack = cotis;
    calculerMontantTotalEtape2();
    showStep(2);
  });

  $('#btn-step-2-prev').on('click', function() {
    showStep(1);
  });

  $('#btn-step-2-next').on('click', function() {
    var session = $('#session_code').val();
    var categorie = $('#categorie_pack_code').val();
    var zone = $('#zone_code').val();
    if (!session) {
      showMessage('danger', 'Veuillez sélectionner une session');
      $('#session_code').focus();
      return;
    }
    if (!categorie) {
      showMessage('danger', 'Veuillez sélectionner une catégorie');
      $('#categorie_pack_code').focus();
      return;
    }
    if (!zone) {
      showMessage('danger', 'Veuillez sélectionner une zone');
      $('#zone_code').focus();
      return;
    }
    showStep(3);
  });

  $('#btn-step-3-prev').on('click', function() {
    showStep(2);
  });

  $('#btn-add-article').on('click', function(e) {
    if (e) e.preventDefault();
    hideMessage();
    var codeArticle = $('#article-select').val();
    var $opt = $('#article-select').find('option:selected');
    var libelleArticle = $opt.data('libelle') || $opt.text().trim() || codeArticle;

    if (!codeArticle) {
      showMessage('danger', 'Veuillez sélectionner un article à ajouter');
      return;
    }
    if ($('#articles-pack-body tr[data-article-code="' + codeArticle + '"]').length > 0) {
      showMessage('warning', 'Cet article est déjà présent dans le pack');
      return;
    }

    var rowHtml = '<tr data-article-code="' + codeArticle + '">' +
      '<td style="padding: 10px 12px; font-weight: 700; color: #0F172A;">' + libelleArticle + '</td>' +
      '<td style="padding: 10px 12px; text-align: center;">' +
        '<input type="number" name="articles[' + codeArticle + '][quantite_article]" value="1" min="1" style="width: 80px; padding: 6px 10px; border-radius: 6px; border: 1px solid #CBD5E1; text-align: center;">' +
        '<input type="hidden" name="articles[' + codeArticle + '][article_code]" value="' + codeArticle + '">' +
      '</td>' +
      '<td style="padding: 10px 12px; text-align: center;">' +
        '<button type="button" class="btn btn-sm remove-article-row" style="border-radius: 6px; font-weight: 600; background: #DC2626; border-color: #DC2626; color: #FFF;">' +
          '<i class="fa-solid fa-trash" data-lucide="trash" style="font-size: 13px; width: 14px; height: 14px;"></i>' +
        '</button>' +
      '</td>' +
    '</tr>';

    $('#articles-pack-body').append(rowHtml);
    articleRowIndex++;
    if ($.fn.select2 && $('#article-select').hasClass('select2-hidden-accessible')) {
      $('#article-select').val('').trigger('change');
    } else {
      $('#article-select').val('');
    }

    if (window.lucide) lucide.createIcons();
    updateArticleCount();
  });

  $(document).on('click', '.remove-article-row', function(e) {
    if (e) e.preventDefault();
    $(this).closest('tr').remove();
    updateArticleCount();
  });

  $('#form-pack').on('submit', function(e) {
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
          showMessage('success', res.message || 'Opération réussie');
          setTimeout(function() { window.location.href = '<?= RACINE ?>pack/formulaire'; }, 1500);
        } else {
          showMessage('danger', res.message || 'Erreur lors de l\'enregistrement');
        }
      },
      error: function() {
        showMessage('danger', 'Erreur réseau ou serveur indisponible');
      }
    });
  });

  <?php if ($isEdit): ?>
  showStep(1);
  updateArticleCount();
  <?php endif; ?>
});
</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
