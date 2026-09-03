<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
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
            <span>Nouvelle Souscription Client</span>
          </h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Processus de souscription en 3 étapes</p>
        </div>
        <a href="<?= RACINE ?>souscription/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
          <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour aux souscriptions
        </a>
      </div>

      <!-- INDICATEUR D'ÉTAPES -->
      <div style="display: flex; gap: 8px; margin-bottom: 24px; align-items: center;">
        <div id="step-indicator-1" class="step-indicator active" style="flex: 1; padding: 12px; text-align: center; background: #1E3A5F; color: #FFF; border-radius: 8px; font-weight: 700; font-size: 13px;">Étape 1 : Informations Client</div>
        <div style="color: #94A3B8; font-size: 18px;">→</div>
        <div id="step-indicator-2" class="step-indicator" style="flex: 1; padding: 12px; text-align: center; background: #F1F5F9; color: #64748B; border-radius: 8px; font-weight: 700; font-size: 13px;">Étape 2 : Sélection des Packs</div>
        <div style="color: #94A3B8; font-size: 18px;">→</div>
        <div id="step-indicator-3" class="step-indicator" style="flex: 1; padding: 12px; text-align: center; background: #F1F5F9; color: #64748B; border-radius: 8px; font-weight: 700; font-size: 13px;">Étape 3 : Récapitulatif</div>
      </div>

      <!-- CARTE FORMULAIRE PRINCIPALE -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <div id="form-messages" style="display: none; margin-bottom: 20px; padding: 12px 16px; border-radius: 8px; font-weight: 600; font-size: 14px;"></div>
        <form id="form-souscription-wizard" action="<?= RACINE ?>souscription/wizardSubmit" method="POST" style="width: 100%;">
          <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">

          <!-- ÉTAPE 1 : INFORMATIONS CLIENT -->
          <div id="step-1" class="form-step">
            <div style="margin-bottom: 24px;">
              <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
                <i data-lucide="user" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Étape 1 : Informations personnelles du client
              </h3>
              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
                <div class="form-group" style="width: 100%; box-sizing: border-box;">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Nom complet <span style="color: #EF4444;">*</span></label>
                  <input type="text" name="nom_client" id="nom_client" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" required placeholder="Ex: KOUASSI Jean">
                </div>

                <div class="form-group" style="width: 100%; box-sizing: border-box;">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Téléphone <span style="color: #EF4444;">*</span></label>
                  <input type="text" name="telephone_client" id="telephone_client" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" required placeholder="Ex: 0708091011">
                </div>

                <div class="form-group" style="width: 100%; box-sizing: border-box;">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Genre <span style="color: #EF4444;">*</span></label>
                  <select name="sexe_client" id="sexe_client" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="M">Masculin</option>
                    <option value="F">Féminin</option>
                  </select>
                </div>

                <div class="form-group" style="width: 100%; box-sizing: border-box;">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Lieu de résidence <span style="color: #EF4444;">*</span></label>
                  <input type="text" name="lieu_residence_client" id="lieu_residence_client" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" required placeholder="Ex: Cocody">
                </div>

                <div class="form-group" style="width: 100%; box-sizing: border-box;">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Email <small>(optionnel)</small></label>
                  <input type="email" name="email_client" id="email_client" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" placeholder="Ex: client@email.com">
                </div>

                <div class="form-group" style="width: 100%; box-sizing: border-box;">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Profession <small>(optionnel)</small></label>
                  <input type="text" name="profession_client" id="profession_client" class="form-control" style="width: 100%; box-sizing: border-box; padding: 11px 14px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1; outline: none;" placeholder="Ex: Commerçant">
                </div>
              </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #E2E8F0;">
              <button type="button" id="btn-step-1-next" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 8px; padding: 10px 24px; display: inline-flex; align-items: center; gap: 8px;">
                Continuer <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
              </button>
            </div>
          </div>

          <!-- ÉTAPE 2 : SÉLECTION DES PACKS -->
          <div id="step-2" class="form-step" style="display: none;">
            <div style="margin-bottom: 24px;">
              <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
                <i data-lucide="shopping-bag" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Étape 2 : Sélection des packs
              </h3>

              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 20px;">
                <div class="form-group" style="width: 100%; box-sizing: border-box;">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Session d'activité <span style="color: #EF4444;">*</span></label>
                  <select id="filter-session" class="form-control select2" style="width: 100%; box-sizing: border-box;" required>
                    <option value="">-- Choisir une session --</option>
                    <?php foreach ($sessions as $s): ?>
                      <option value="<?= $s['code_session'] ?>"><?= htmlspecialchars($s['libelle_session']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="form-group" style="width: 100%; box-sizing: border-box;">
                  <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Catégorie</label>
                  <select id="filter-categorie" class="form-control select2" style="width: 100%; box-sizing: border-box;">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $cat): ?>
                      <option value="<?= $cat['code_categorie_pack'] ?>"><?= htmlspecialchars($cat['libelle_categorie_pack']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div id="packs-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px;">
                <p style="color: #94A3B8; text-align: center; padding: 40px 0; font-style: italic;">Sélectionnez une session et/ou une catégorie pour afficher les packs disponibles.</p>
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

          <!-- ÉTAPE 3 : RÉCAPITULATIF -->
          <div id="step-3" class="form-step" style="display: none;">
            <div style="margin-bottom: 24px;">
              <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F1F5F9; padding-bottom: 8px;">
                <i data-lucide="check-circle-2" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Étape 3 : Récapitulatif et validation
              </h3>

              <!-- CARD CLIENT -->
              <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 20px; margin-bottom: 24px;">
                <h4 style="font-size: 14px; font-weight: 800; color: #1E3A5F; margin: 0 0 12px 0; display: flex; align-items: center; gap: 8px;">
                  <i data-lucide="user" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Informations du client
                </h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; font-size: 14px; color: #334155;">
                  <div><strong>Nom :</strong> <span id="recap-nom"></span></div>
                  <div><strong>Prénom :</strong> <span id="recap-prenom"></span></div>
                  <div><strong>Téléphone :</strong> <span id="recap-telephone"></span></div>
                  <div><strong>Email :</strong> <span id="recap-email"></span></div>
                  <div><strong>Genre :</strong> <span id="recap-sexe"></span></div>
                  <div><strong>Lieu de résidence :</strong> <span id="recap-lieu"></span></div>
                  <div><strong>Profession :</strong> <span id="recap-profession"></span></div>
                </div>
              </div>

              <!-- TABLEAU RÉCAPITULATIF DES PACKS -->
              <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 10px; padding: 20px; margin-bottom: 24px; overflow-x: auto;">
                <h4 style="font-size: 14px; font-weight: 800; color: #1E3A5F; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px;">
                  <i data-lucide="package" style="width: 16px; height: 16px; color: #1E3A5F;"></i> Packs sélectionnés
                </h4>
                <table id="table-recap-packs" class="table display nowrap" style="width:100%; border-collapse: collapse; font-size: 14px;">
                  <thead>
                    <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                      <th style="padding: 10px 12px;">Pack</th>
                      <th style="padding: 10px 12px;">Catégorie</th>
                      <th style="padding: 10px 12px; text-align: right;">Montant (FCFA)</th>
                      <th style="padding: 10px 12px; text-align: center;">Articles</th>
                      <th style="padding: 10px 12px; text-align: center;">Durée (jours)</th>
                      <th style="padding: 10px 12px; text-align: center;">Action</th>
                    </tr>
                  </thead>
                  <tbody id="recap-packs-body"></tbody>
                  <tfoot>
                    <tr style="background: #F1F5F9; font-weight: 800; color: #1E3A5F;">
                      <td colspan="2" style="padding: 12px; text-align: right;">Montant total :</td>
                      <td id="recap-montant-total" style="padding: 12px; text-align: right; color: #15803D; font-size: 16px;">0 FCFA</td>
                      <td colspan="3"></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

            <input type="hidden" name="nom_client" id="hidden-nom_client">
            <input type="hidden" name="telephone_client" id="hidden-telephone_client">
            <input type="hidden" name="email_client" id="hidden-email_client">
            <input type="hidden" name="sexe_client" id="hidden-sexe_client">
            <input type="hidden" name="lieu_residence_client" id="hidden-lieu_residence_client">
            <input type="hidden" name="profession_client" id="hidden-profession_client">
            <input type="hidden" name="zone_code" id="hidden-zone_code" value="">
            <input type="hidden" name="session_code" id="hidden-session_code" value="">
            <input type="hidden" name="packs" id="hidden-packs" value="">

            <div style="display: flex; justify-content: space-between; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #E2E8F0;">
              <button type="button" id="btn-step-3-prev" class="btn btn-secondary" style="font-weight: 600; border-radius: 8px; padding: 10px 24px; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Précédent
              </button>
              <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 8px; padding: 10px 24px; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="check" style="width: 18px; height: 18px;"></i> Valider la Souscription
              </button>
            </div>
          </div>

        </form>
      </div>

    </div>
  </main>
</div>

<script>
var selectedPacks = [];

function showMessage(type, message) {
  var $msg = $('#form-messages');
  $msg.removeClass('alert-success', 'alert-danger', 'alert-warning', 'alert-info')
      .css({ 'display': 'block', 'background': type === 'success' ? '#DCFCE7' : type === 'danger' ? '#FEE2E2' : type === 'warning' ? '#FEF3C7' : '#EFF6FF', 'color': type === 'success' ? '#15803D' : type === 'danger' ? '#B91C1C' : type === 'warning' ? '#92400E' : '#1E3A5F', 'border': '1px solid ' + (type === 'success' ? '#BBF7D0' : type === 'danger' ? '#FECACA' : type === 'warning' ? '#FDE68A' : '#BFDBFE') })
      .html('<strong>' + (type === 'success' ? 'Succès' : type === 'danger' ? 'Erreur' : type === 'warning' ? 'Attention' : 'Information') + ' :</strong> ' + message);
}

function hideMessage() {
  $('#form-messages').hide();
}

function showStep(step) {
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
  if ($.fn.select2) {
    $('.select2').select2({ width: '100%' });
  }
  if (step === 3) {
    renderRecap();
  }
}

function loadPacks() {
  var sessionCode = $('#filter-session').val();
  var categorieCode = $('#filter-categorie').val();
  var container = $('#packs-container');

  if (!sessionCode) {
    container.html('<p style="color: #94A3B8; text-align: center; padding: 40px 0; font-style: italic;">Sélectionnez une session pour afficher les packs disponibles.</p>');
    return;
  }

  $.ajax({
    url: '<?= RACINE ?>souscription/wizardData',
    data: { session_code: sessionCode, categorie_code: categorieCode },
    dataType: 'json',
    success: function(res) {
      if (res.status === 1 && res.data.length) {
        var html = '';
        res.data.forEach(function(pack) {
          var isSelected = selectedPacks.indexOf(pack.code_pack) !== -1;
          var borderColor = isSelected ? '#1E3A5F' : '#E2E8F0';
          var bgColor = isSelected ? '#EFF6FF' : '#FFFFFF';
          html += '<div class="pack-card" data-code="' + pack.code_pack + '" style="background: ' + bgColor + '; border: 2px solid ' + borderColor + '; border-radius: 12px; padding: 16px; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: all 0.2s;">' +
            '<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">' +
              '<div>' +
                '<strong style="font-size: 15px; color: #0F172A; display: block; margin-bottom: 6px;">' + (pack.libelle_pack || 'Pack') + '</strong>' +
                '<span style="display: inline-block; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; background: #1E3A5F; color: #FFF;">' + (pack.libelle_categorie_pack || '') + '</span>' +
              '</div>' +
              (pack.image_pack ? '<img src="<?= RACINE ?>public/assets/images/packs/' + pack.image_pack + '" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #E2E8F0;">' : '<div style="width: 60px; height: 60px; background: #F1F5F9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #64748B; font-size: 11px; font-weight: 700;">Sans image</div>') +
            '</div>' +
            '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px; color: #475569;">' +
              '<div><strong>Montant :</strong> <span style="color: #15803D; font-weight: 700;">' + Number(pack.prix_cotisation_pack || 0).toLocaleString('fr-FR') + ' FCFA</span></div>' +
              '<div><strong>Durée :</strong> ' + (pack.nombre_jour_session || 0) + ' jours</div>' +
              '<div><strong>Articles :</strong> ' + (pack.nombre_articles || 0) + ' article(s)</div>' +
              '<div><strong>Souscriptions :</strong> ' + (pack.nombre_souscriptions || 0) + '</div>' +
            '</div>' +
          '</div>';
        });
        container.html(html);
        if (window.lucide) lucide.createIcons();
      } else {
        container.html('<p style="color: #94A3B8; text-align: center; padding: 40px 0; font-style: italic;">Aucun pack disponible pour ces critères.</p>');
      }
    },
    error: function() {
      container.html('<p style="color: #DC2626; text-align: center; padding: 40px 0;">Erreur lors du chargement des packs.</p>');
    }
  });
}

function renderRecap() {
  var tbody = $('#recap-packs-body');
  tbody.empty();
  var montantTotal = 0;

  selectedPacks.forEach(function(code) {
    $.ajax({
      url: '<?= RACINE ?>souscription/wizardData',
      data: { session_code: $('#hidden-session_code').val(), categorie_code: '' },
      dataType: 'json',
      async: false,
      success: function(res) {
        if (res.status === 1) {
          var pack = res.data.find(function(p) { return p.code_pack === code; });
          if (pack) {
            montantTotal += parseFloat(pack.prix_cotisation_pack || 0);
            var row = '<tr data-code="' + pack.code_pack + '" style="border-bottom: 1px solid #E2E8F0;">' +
              '<td style="padding: 10px 12px; font-weight: 700; color: #0F172A;">' + (pack.libelle_pack || 'Pack') + '</td>' +
              '<td style="padding: 10px 12px;"><span style="display: inline-block; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; background: #1E3A5F; color: #FFF;">' + (pack.libelle_categorie_pack || '') + '</span></td>' +
              '<td style="padding: 10px 12px; text-align: right; font-weight: 700; color: #15803D;">' + Number(pack.prix_cotisation_pack || 0).toLocaleString('fr-FR') + ' FCFA</td>' +
              '<td style="padding: 10px 12px; text-align: center;">' + (pack.nombre_articles || 0) + '</td>' +
              '<td style="padding: 10px 12px; text-align: center;">' + (pack.nombre_jour_session || 0) + '</td>' +
              '<td style="padding: 10px 12px; text-align: center;">' +
                '<button type="button" class="btn btn-sm remove-pack-row" style="border-radius: 6px; font-weight: 600; background: #DC2626; border-color: #DC2626; color: #FFF;">' +
                  '<i data-lucide="trash" style="width: 14px; height: 14px;"></i>' +
                '</button>' +
              '</td>' +
            '</tr>';
            tbody.append(row);
          }
        }
      }
    });
  });

  $('#recap-montant-total').text(montantTotal.toLocaleString('fr-FR') + ' FCFA');
  if (window.lucide) lucide.createIcons();
}

$(document).ready(function() {
  if (window.lucide) lucide.createIcons();
  if ($.fn.select2) {
    $('.select2').select2({ width: '100%' });
  }

  $('#btn-step-1-next').on('click', function() {
    var nom = $('#nom_client').val().trim();
    var tel = $('#telephone_client').val().trim();
    var sexe = $('#sexe_client').val();
    var lieu = $('#lieu_residence_client').val().trim();
    if (!nom || !tel || !sexe || !lieu) {
      showMessage('danger', 'Veuillez remplir tous les champs obligatoires (Nom, Téléphone, Genre, Lieu de résidence).');
      return;
    }
    showStep(2);
    loadPacks();
  });

  $('#btn-step-2-prev').on('click', function() {
    showStep(1);
  });

  $('#btn-step-3-prev').on('click', function() {
    showStep(2);
  });

  $(document).on('click', '.pack-card', function() {
    var code = $(this).data('code');
    var idx = selectedPacks.indexOf(code);
    if (idx === -1) {
      selectedPacks.push(code);
    } else {
      selectedPacks.splice(idx, 1);
    }
    loadPacks();
  });

  $('#filter-session').on('change', function() {
    selectedPacks = [];
    loadPacks();
  });
  $('#filter-categorie').on('change', function() {
    loadPacks();
  });

  $(document).on('click', '.remove-pack-row', function() {
    var code = $(this).closest('tr').data('code');
    var idx = selectedPacks.indexOf(code);
    if (idx !== -1) {
      selectedPacks.splice(idx, 1);
    }
    renderRecap();
    loadPacks();
  });

  $('#btn-step-2-next').on('click', function() {
    if (!$('#filter-session').val()) {
      showMessage('danger', 'Veuillez sélectionner une session d\'activité.');
      return;
    }
    if (selectedPacks.length === 0) {
      showMessage('danger', 'Veuillez sélectionner au moins un pack.');
      return;
    }
    showStep(3);
  });

  $('#form-souscription-wizard').on('submit', function(e) {
    e.preventDefault();
    if (!$('#filter-session').val()) {
      showMessage('danger', 'Veuillez sélectionner une session d\'activité.');
      return;
    }
    if (selectedPacks.length === 0) {
      showMessage('danger', 'Aucun pack sélectionné.');
      return;
    }

    $('#hidden-nom_client').val($('#nom_client').val().trim());
    $('#hidden-telephone_client').val($('#telephone_client').val().trim());
    $('#hidden-email_client').val($('#email_client').val().trim());
    $('#hidden-sexe_client').val($('#sexe_client').val());
    $('#hidden-lieu_residence_client').val($('#lieu_residence_client').val().trim());
    $('#hidden-profession_client').val($('#profession_client').val().trim());
    $('#hidden-zone_code').val($('#filter-session').val() || '');
    $('#hidden-session_code').val($('#filter-session').val() || '');
    $('#hidden-packs').val(JSON.stringify(selectedPacks));

    var formData = $(this).serialize();
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function(res) {
        if (res.status === 1 || res.success) {
          if (window.toastr) toastr.success(res.message || 'Souscription créée avec succès');
          setTimeout(function() { window.location.href = '<?= RACINE ?>souscription/list'; }, 1500);
        } else {
          showMessage('danger', res.message || 'Erreur lors de l\'enregistrement');
          if (window.toastr) toastr.error(res.message || 'Erreur lors de l\'enregistrement');
        }
      },
      error: function() {
        showMessage('danger', 'Erreur réseau ou serveur indisponible');
        if (window.toastr) toastr.error('Erreur réseau');
      }
    });
  });
});
</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
