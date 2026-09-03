<?php 
require_once __DIR__ . '/../../public/inc/header.php'; 
$categories = $categories ?? [];
$grilles = $grilles ?? [];
?>

<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">
      
      <!-- Page Header -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0;">Grille Tarifaire & Catégories de Poids OVOLIA</h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Barème officiel par tranche de poids net pour Poulets entiers frais, Pintades & Volailles</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
          <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAddPrixGrille" style="background: #059669; border-color: #059669; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; color: white;">
            <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Nouveau Tarif de Grille
          </button>
          <a href="<?= RACINE ?>aviculture/pesees" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="qr-code" style="width: 18px; height: 18px;"></i> Registre des Pesées
          </a>
        </div>
      </div>

      <!-- Style Sobres, Élégants & Professionnels -->
      <style>
        .cat-poids-card-sobre {
          background: #FFFFFF;
          border-radius: 12px;
          padding: 20px;
          border: 1px solid #E2E8F0;
          box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
          position: relative;
          transition: all 0.3s ease;
        }
        .cat-poids-card-sobre:hover {
          transform: translateY(-4px);
          box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
          border-color: #CBD5E1;
        }
        .cat-poids-card-sobre .cat-icon-sobre {
          width: 40px;
          height: 40px;
          border-radius: 10px;
          background: #F1F5F9;
          color: #1E3A5F;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: background 0.3s ease, color 0.3s ease;
        }
        .cat-poids-card-sobre:hover .cat-icon-sobre {
          background: #1E3A5F;
          color: #FFFFFF;
        }
        .cat-poids-card-sobre .weight-box-sobre {
          background: #F8FAFC;
          border: 1px solid #E2E8F0;
          border-radius: 8px;
          padding: 10px 14px;
          text-align: center;
          margin-top: 12px;
          transition: background 0.3s ease;
        }
        .cat-poids-card-sobre:hover .weight-box-sobre {
          background: #F1F5F9;
        }

        /* Style du Switch Toggle Statut Moderne & Badge */
        .status-cell-wrapper {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          gap: 10px;
        }
        .toggle-switch-container {
          position: relative;
          display: inline-block;
          width: 40px;
          height: 22px;
          margin: 0;
          cursor: pointer;
          vertical-align: middle;
        }
        .toggle-statut-grille {
          opacity: 0;
          width: 0;
          height: 0;
          position: absolute;
        }
        .toggle-slider {
          position: absolute;
          cursor: pointer;
          top: 0; left: 0; right: 0; bottom: 0;
          background-color: #CBD5E1;
          transition: background-color .25s ease;
          border-radius: 22px;
        }
        .toggle-slider-knob {
          position: absolute;
          height: 16px;
          width: 16px;
          left: 3px;
          top: 3px;
          background-color: #FFFFFF;
          transition: transform .25s cubic-bezier(0.4, 0, 0.2, 1);
          border-radius: 50%;
          box-shadow: 0 1px 3px rgba(15, 23, 42, 0.25);
        }
        .toggle-statut-grille:checked + .toggle-slider {
          background-color: #16A34A !important;
        }
        .toggle-statut-grille:checked + .toggle-slider .toggle-slider-knob {
          transform: translateX(18px) !important;
        }
        .status-pill-badge {
          font-size: 11px;
          font-weight: 700;
          padding: 3px 10px;
          border-radius: 12px;
          display: inline-block;
          min-width: 54px;
          text-align: center;
          transition: all 0.25s ease;
        }
        .status-pill-badge.actif {
          background-color: #DCFCE7;
          color: #15803D;
          border: 1px solid #BBF7D0;
        }
        .status-pill-badge.inactif {
          background-color: #F1F5F9;
          color: #64748B;
          border: 1px solid #E2E8F0;
        }
      </style>

      <!-- Grille des 6 Catégories de Référence OVOLIA en col-md-4 avec Couleurs Douces -->
      <div style="margin-bottom: 28px;">
        <h2 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="scale" style="width: 18px; height: 18px; color: #1E3A5F;"></i> Les 6 Catégories de Référence OVOLIA
        </h2>
        <div class="row">
          <?php 
          $softThemes = [
              'CATP-ESSENTIEL' => [
                  'icon'      => 'feather',
                  'accent'    => '#10B981',
                  'bg_icon'   => '#ECFDF5',
                  'text_icon' => '#047857',
                  'bg_box'    => '#F0FDF4'
              ],
              'CATP-CLASSIQUE' => [
                  'icon'      => 'award',
                  'accent'    => '#0284C7',
                  'bg_icon'   => '#F0F9FF',
                  'text_icon' => '#0369A1',
                  'bg_box'    => '#F0F9FF'
              ],
              'CATP-GRAND' => [
                  'icon'      => 'package',
                  'accent'    => '#6366F1',
                  'bg_icon'   => '#EEF2FF',
                  'text_icon' => '#4338CA',
                  'bg_box'    => '#EEF2FF'
              ],
              'CATP-EXTRA' => [
                  'icon'      => 'star',
                  'accent'    => '#8B5CF6',
                  'bg_icon'   => '#F3E8FF',
                  'text_icon' => '#6D28D9',
                  'bg_box'    => '#F5F3FF'
              ],
              'CATP-SIGNATURE' => [
                  'icon'      => 'crown',
                  'accent'    => '#F59E0B',
                  'bg_icon'   => '#FEF3C7',
                  'text_icon' => '#B45309',
                  'bg_box'    => '#FFFBEB'
              ],
              'CATP-PRESTIGE' => [
                  'icon'      => 'sparkles',
                  'accent'    => '#EF4444',
                  'bg_icon'   => '#FEE2E2',
                  'text_icon' => '#B91C1C',
                  'bg_box'    => '#FEF2F2'
              ]
          ];
          foreach ($categories as $cat): 
              $st = $softThemes[$cat['code_categorie_poids']] ?? [
                  'icon' => 'scale', 'accent' => '#1E3A5F', 'bg_icon' => '#F1F5F9', 'text_icon' => '#1E3A5F', 'bg_box' => '#F8FAFC'
              ];
          ?>
          <div class="col-md-4 col-sm-6 mb-3">
            <div class="cat-poids-card-sobre h-100" style="overflow: hidden; position: relative;">
              <div style="position: absolute; top: 0; left: 0; right: 0; height: 3.5px; background: <?= $st['accent'] ?>;"></div>
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; pt-1;">
                <div style="display: flex; align-items: center; gap: 12px;">
                  <div class="cat-icon-sobre" style="background: <?= $st['bg_icon'] ?>; color: <?= $st['text_icon'] ?>;">
                    <i data-lucide="<?= $st['icon'] ?>" style="width: 20px; height: 20px;"></i>
                  </div>
                  <div>
                    <span style="font-weight: 800; font-size: 15px; color: #0F172A; display: block;"><?= htmlspecialchars($cat['libelle_categorie_poids']) ?></span>
                    <code style="font-weight:700; color: <?= $st['text_icon'] ?>; font-size: 11px; background: <?= $st['bg_icon'] ?>; padding: 2px 6px; border-radius: 4px;">
                      <?= htmlspecialchars($cat['code_categorie_poids']) ?>
                    </code>
                  </div>
                </div>
              </div>
              <div class="weight-box-sobre" style="background: <?= $st['bg_box'] ?>; border-color: <?= $st['accent'] ?>30;">
                <span style="color: #64748B; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 2px;">Plage de Poids Net</span>
                <div style="font-size: 17px; font-weight: 800; color: <?= $st['text_icon'] ?>;">
                  ⚖️ <?= number_format($cat['poids_min'], 2, ',', ' ') ?> kg &mdash; <?= number_format($cat['poids_max'], 2, ',', ' ') ?> kg
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Card Table Matrice Tarifaire (Exactement sur le modèle de table-annees) -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; max-width: 100%; box-sizing: border-box; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; pb-2; border-bottom: 1px solid #F1F5F9;">
          <h2 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="layers" style="width: 18px; height: 18px; color: #1E3A5F;"></i> Matrice Tarifaire Complète par Produit
          </h2>
          <span style="background: #EFF6FF; color: #1E3A5F; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
            Catalogue Produits OVOLIA
          </span>
        </div>

        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
          <table id="table-categories-poids" class="table display nowrap" style="width:100%; max-width:100%; border-collapse: collapse;">
            <thead>
              <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                <th style="padding: 12px;">Produit Avicole</th>
                <th style="padding: 12px;">Code Catégorie</th>
                <th style="padding: 12px;">Libellé Catégorie</th>
                <th style="padding: 12px;">Tranche Poids Net (Min - Max)</th>
                <th style="padding: 12px; text-align: right;">Tarif Vente Appliqué</th>
                <th class="text-center" style="padding: 12px;">Statut</th>
                <th class="text-end" style="padding: 12px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($grilles as $g): ?>
                <tr>
                  <td style="padding: 12px; font-weight: 700; color: #0F172A;">
                    <i data-lucide="feather" style="width: 14px; height: 14px; color: #047857; margin-right: 4px;"></i>
                    <?= htmlspecialchars($g['libelle_produit']) ?>
                  </td>
                  <td style="padding: 12px;">
                    <code style="font-weight:700; color:#334155; background:#F1F5F9; padding:2px 6px; border-radius:4px;">
                      <?= htmlspecialchars(!empty($g['categorie_poids_code']) ? $g['categorie_poids_code'] : (!empty($g['code_categorie_poids']) ? $g['code_categorie_poids'] : 'FIXE')) ?>
                    </code>
                  </td>
                  <td style="padding: 12px; font-weight: 600; color: #334155;">
                    <?= htmlspecialchars($g['libelle_categorie_poids']) ?>
                  </td>
                  <td style="padding: 12px; color: #64748B;">
                    <?= number_format($g['poids_min'], 2, ',', ' ') ?> kg &mdash; <?= number_format($g['poids_max'], 2, ',', ' ') ?> kg
                  </td>
                  <td style="padding: 12px; text-align: right; font-weight: 800; color: #059669; font-size: 15px;">
                    <?= number_format($g['prix_vente'], 0, ',', ' ') ?> FCFA
                  </td>
                  <td class="text-center" style="padding: 12px;">
                    <?php 
                    $isActif = ($g['statut_grille'] === 'actif');
                    $checkedAttr = $isActif ? 'checked' : '';
                    ?>
                    <div style="display:flex; justify-content:center; align-items:center;">
                      <label style="position:relative; display:inline-block; width:38px; height:20px; margin:0; cursor:pointer;" title="<?= $isActif ? 'Actif - Cliquez pour désactiver' : 'Inactif - Cliquez pour activer' ?>">
                        <input type="checkbox" class="toggle-statut-grille" data-id="<?= $g['id_grille_tarif'] ?>" <?= $checkedAttr ?> style="opacity:0; width:0; height:0;">
                        <span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:<?= $isActif ? '#15803D' : '#CBD5E1' ?>; transition:.3s; border-radius:20px;">
                          <span style="position:absolute; content:''; height:14px; width:14px; left:<?= $isActif ? '20px' : '3px' ?>; bottom:3px; background-color:white; transition:.3s; border-radius:50%;"></span>
                        </span>
                      </label>
                    </div>
                  </td>
                  <td class="text-end" style="padding: 12px;">
                    <button class="btn btn-sm btn-secondary btn-edit-prix-grille" 
                            data-id="<?= $g['id_grille_tarif'] ?>" 
                            data-libelle="<?= htmlspecialchars($g['libelle_produit'] . ' - ' . $g['libelle_categorie_poids']) ?>" 
                            data-prix="<?= $g['prix_vente'] ?>"
                            style="margin-right: 6px; font-weight: 600; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                      <i data-lucide="edit" style="width: 14px; height: 14px;"></i> Éditer
                    </button>
                    <button class="btn btn-sm btn-secondary btn-details-prix-grille" 
                            data-grille='<?= json_encode($g, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
                            style="font-weight: 600; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                      <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Détails
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Modal Modification de Prix -->
<div class="modal fade" id="modalEditPrix" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 450px; margin: auto;">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: #1E3A5F; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="coins" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Modifier le Tarif de Vente
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditPrix">
        <input type="hidden" name="code_categorie" id="edit_code_cat" value="">
        <input type="hidden" name="id_grille" id="edit_id_grille" value="">
        <div class="modal-body" style="padding: 20px;">
          <div style="margin-bottom: 16px;">
            <label style="font-weight: 700; font-size: 13px; color: #64748B; margin-bottom: 4px; display: block;">Catégorie / Produit Concerné</label>
            <div id="target_label" style="font-weight: 800; font-size: 15px; color: #0F172A; background: #F8FAFC; padding: 10px 14px; border-radius: 8px; border: 1px solid #E2E8F0;"></div>
          </div>
          <div style="margin-bottom: 16px;">
            <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Nouveau Prix de Vente (FCFA) *</label>
            <div class="input-group">
              <input type="number" name="prix_vente" id="edit_prix_val" class="form-control" style="border-radius: 8px 0 0 8px; height: 44px; font-size: 16px; font-weight: 800;" required min="1" step="50" placeholder="Ex: 2500">
              <span class="input-group-text" style="border-radius: 0 8px 8px 0; background: #F1F5F9; font-weight: 700; color: #475569;">FCFA</span>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
          <button type="button" class="btn btn-secondary" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; border-radius: 8px; font-weight: 700; padding: 8px 18px;">Enregistrer Tarif</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  const baseApi = (typeof RACINE !== 'undefined') ? RACINE : '/ovolias/';

  $('#table-categories-poids').DataTable({
    processing: true,
    autoWidth: false,
    language: { url: baseApi + 'json/datatables-i18n-fr-FR.json' },
    drawCallback: function() { if (window.lucide) lucide.createIcons(); }
  });

  // Bascule de statut via switch toggle
  $(document).on('change', '.toggle-statut-grille', function() {
    var id = $(this).data('id');
    var isChecked = $(this).is(':checked');
    var $input = $(this);
    var $span = $input.siblings('span');
    var $knob = $span.children('span');

    $.ajax({
      url: baseApi + 'aviculture/toggleStatutGrille',
      type: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      data: { 
        id_grille: id,
        csrf_token: '<?= Validator::generateCsrfToken() ?>'
      },
      dataType: 'json',
      success: function(res) {
        if (res.status === 'success' || res.status === 1 || res.success) {
          if (window.toastr) toastr.success(res.message || 'Statut mis à jour avec succès');
          if (isChecked) {
            $span.css('background-color', '#15803D');
            $knob.css('left', '20px');
          } else {
            $span.css('background-color', '#CBD5E1');
            $knob.css('left', '3px');
          }
        } else {
          if (window.toastr) toastr.error(res.message || 'Erreur lors du changement de statut');
          $input.prop('checked', !isChecked);
        }
      },
      error: function() {
        if (window.toastr) toastr.error('Erreur réseau');
        $input.prop('checked', !isChecked);
      }
    });
  });

  // Édition Prix Défaut Catégorie Reference
  $(document).on('click', '.btn-edit-prix', function() {
    var code = $(this).data('code');
    var libelle = $(this).data('libelle');
    var prix = $(this).data('prix');

    $('#edit_code_cat').val(code);
    $('#edit_id_grille').val('');
    $('#target_label').text(libelle + ' (' + code + ')');
    $('#edit_prix_val').val(prix);

    var modalEl = document.getElementById('modalEditPrix');
    var bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    bsModal.show();
  });

  // Édition Prix Grille Spécifique Produit
  $(document).on('click', '.btn-edit-prix-grille', function() {
    var id = $(this).data('id');
    var libelle = $(this).data('libelle');
    var prix = $(this).data('prix');

    $('#edit_code_cat').val('');
    $('#edit_id_grille').val(id);
    $('#target_label').text(libelle);
    $('#edit_prix_val').val(prix);

    var modalEl = document.getElementById('modalEditPrix');
    var bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    bsModal.show();
  });

  // Affichage des Détails d'un tarif de grille
  $(document).on('click', '.btn-details-prix-grille', function() {
    var g = $(this).data('grille');
    if (typeof g === 'string') g = JSON.parse(g);

    $('#detail_grille_produit').text(g.libelle_produit || '-');
    var codeCat = g.categorie_poids_code || g.code_categorie_poids || 'FIXE';
    $('#detail_grille_code_cat').text(codeCat);
    $('#detail_grille_libelle_cat').text(g.libelle_categorie_poids || '-');
    
    var pmin = parseFloat(g.poids_min) || 0;
    var pmax = parseFloat(g.poids_max) || 0;
    if (pmin === 0 && pmax === 0) {
      $('#detail_grille_tranche').text('Prix Fixe Unitaire (Non soumis)');
    } else {
      $('#detail_grille_tranche').text(pmin.toFixed(2).replace('.', ',') + ' kg — ' + pmax.toFixed(2).replace('.', ',') + ' kg');
    }

    var px = parseFloat(g.prix_vente) || 0;
    $('#detail_grille_prix').text(new Intl.NumberFormat('fr-FR').format(px) + ' FCFA');

    var isActif = (g.statut_grille === 'actif');
    $('#detail_grille_statut').html(isActif ? '<span style="color:#15803D;">Actif</span>' : '<span style="color:#64748B;">Inactif</span>');

    if (window.lucide) lucide.createIcons();

    var modalEl = document.getElementById('modalDetailsGrille');
    var bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    bsModal.show();
  });

  // Soumission AJAX Formulaire Prix Modification
  $('#formEditPrix').on('submit', function(e) {
    e.preventDefault();
    $.post(baseApi + 'aviculture/updatePrixGrille', $(this).serialize(), function(res) {
      if (res.status === 'success' || res.status === 1 || res.success) {
        if (window.toastr) toastr.success(res.message || 'Prix mis à jour avec succès');
        else alert(res.message);

        var modalEl = document.getElementById('modalEditPrix');
        var bsModal = bootstrap.Modal.getInstance(modalEl);
        if (bsModal) bsModal.hide();

        setTimeout(function() { location.reload(); }, 600);
      } else {
        if (window.toastr) toastr.error(res.message || 'Erreur lors de la mise à jour');
        else alert(res.message);
      }
    }, 'json');
  });

  // Soumission AJAX Formulaire Nouveau Tarif de Grille
  $('#formAddPrixGrille').on('submit', function(e) {
    e.preventDefault();
    $.post(baseApi + 'aviculture/addPrixGrille', $(this).serialize(), function(res) {
      if (res.status === 'success' || res.status === 1 || res.success) {
        if (window.toastr) toastr.success(res.message || 'Tarif enregistré avec succès');
        else alert(res.message);

        var modalEl = document.getElementById('modalAddPrixGrille');
        var bsModal = bootstrap.Modal.getInstance(modalEl);
        if (bsModal) bsModal.hide();

        setTimeout(function() { location.reload(); }, 600);
      } else {
        if (window.toastr) toastr.error(res.message || 'Erreur lors de l\'enregistrement');
        else alert(res.message);
      }
    }, 'json');
  });

  // Écouteur sur la sélection du produit avicole (soumis_grille_poids)
  $('#select_produit_code').on('change', function() {
    var selectedOpt = $(this).find(':selected');
    var soumis = parseInt(selectedOpt.data('soumis'));

    var $catSelect = $('#select_categorie_poids');
    var $infoBox = $('#info_soumis_poids_status');

    if (soumis === 1) {
      $catSelect.prop('disabled', false).prop('required', true);
      $infoBox.html('<div class="alert alert-info py-2 px-3 mb-0" style="font-size:12px; border-radius:8px; background:#EFF6FF; border-color:#BFDBFE; color:#1E40AF; display:flex; align-items:center; gap:6px;"><i data-lucide="info" style="width:16px;height:16px;"></i> Ce produit est <strong>soumis aux grilles de poids</strong>. Sélection de la tranche requise.</div>');
    } else if (soumis === 0) {
      $catSelect.prop('disabled', true).prop('required', false).val('');
      $infoBox.html('<div class="alert alert-warning py-2 px-3 mb-0" style="font-size:12px; border-radius:8px; background:#FEF3C7; border-color:#FDE68A; color:#92400E; display:flex; align-items:center; gap:6px;"><i data-lucide="alert-triangle" style="width:16px;height:16px;"></i> Ce produit <strong>n\'est pas soumis aux tranches de poids</strong> (Prix fixe unitaire). La tranche est désactivée.</div>');
    } else {
      $catSelect.prop('disabled', false).prop('required', true);
      $infoBox.empty();
    }
    if (window.lucide) lucide.createIcons();
  });
});
</script>

<!-- Modal Nouveau Tarif de Grille -->
<div class="modal fade" id="modalAddPrixGrille" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 500px; margin: auto;">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: #1E3A5F; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="plus-circle" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Nouveau Tarif Produit / Grille
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formAddPrixGrille">
        <div class="modal-body" style="padding: 20px;">
          
          <div style="margin-bottom: 16px;">
            <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Produit Avicole *</label>
            <select name="produit_code" id="select_produit_code" class="form-select" style="height: 44px; border-radius: 8px; font-weight: 700; color: #0F172A;" required>
              <option value="">-- Sélectionner un produit --</option>
              <?php if (!empty($produits)): ?>
                <?php foreach ($produits as $p): ?>
                  <option value="<?= htmlspecialchars($p['code_produit_aviculture']) ?>" data-soumis="<?= (int)$p['soumis_grille_poids'] ?>">
                    <?= htmlspecialchars($p['libelle_produit']) ?> (<?= (int)$p['soumis_grille_poids'] === 1 ? 'Grille Poids' : 'Prix Fixe' ?>)
                  </option>
                <?php endforeach; ?>
              <?php else: ?>
                <option value="" disabled>Tous les produits actifs ont déjà un tarif enregistré</option>
              <?php endif; ?>
            </select>
          </div>

          <div style="margin-bottom: 12px;" id="info_soumis_poids_status"></div>

          <div style="margin-bottom: 16px;">
            <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Tranche / Catégorie de Poids *</label>
            <select name="categorie_poids_code" id="select_categorie_poids" class="form-select" style="height: 44px; border-radius: 8px; font-weight: 700; color: #0F172A;" required>
              <option value="">-- Sélectionner une catégorie --</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['code_categorie_poids']) ?>">
                  <?= htmlspecialchars($cat['libelle_categorie_poids']) ?> (<?= number_format($cat['poids_min'], 2, ',', ' ') ?> - <?= number_format($cat['poids_max'], 2, ',', ' ') ?> kg)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div style="margin-bottom: 16px;">
            <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Prix de Vente Spécifique (FCFA) *</label>
            <div class="input-group">
              <input type="number" name="prix_vente" class="form-control" style="border-radius: 8px 0 0 8px; height: 44px; font-size: 16px; font-weight: 800;" required min="1" step="50" placeholder="Ex: 3500">
              <span class="input-group-text" style="border-radius: 0 8px 8px 0; background: #F1F5F9; font-weight: 700; color: #475569;">FCFA</span>
            </div>
          </div>

          <div style="margin-bottom: 12px;">
            <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Statut du Tarif</label>
            <select name="statut_grille" class="form-select" style="height: 44px; border-radius: 8px; font-weight: 700; color: #0F172A;">
              <option value="actif" selected>🟢 Actif</option>
              <option value="inactif">⚪ Inactif</option>
            </select>
          </div>

        </div>
        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
          <button type="button" class="btn btn-secondary" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; border-radius: 8px; font-weight: 700; padding: 8px 18px;">Enregistrer le Tarif</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Détails Tarif Grille -->
<div class="modal fade" id="modalDetailsGrille" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 520px; margin: auto;">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: #1E3A5F; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="eye" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Détails du Tarif Spécifique
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="padding: 20px;">
        <div style="display: flex; flex-direction: column; gap: 14px;">
          <div style="border-bottom: 1px solid #E2E8F0; padding-bottom: 8px;">
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Produit Avicole</span>
            <div id="detail_grille_produit" style="font-size: 15px; font-weight: 800; color: #0F172A; margin-top: 2px;"></div>
          </div>
          <div style="display: flex; gap: 16px; border-bottom: 1px solid #E2E8F0; padding-bottom: 8px;">
            <div style="flex: 1;">
              <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Code Catégorie</span>
              <div id="detail_grille_code_cat" style="font-size: 13px; font-weight: 700; color: #334155; margin-top: 2px;"></div>
            </div>
            <div style="flex: 1;">
              <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Libellé Catégorie</span>
              <div id="detail_grille_libelle_cat" style="font-size: 13px; font-weight: 700; color: #334155; margin-top: 2px;"></div>
            </div>
          </div>
          <div style="display: flex; gap: 16px; border-bottom: 1px solid #E2E8F0; padding-bottom: 8px;">
            <div style="flex: 1;">
              <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Tranche de Poids Net</span>
              <div id="detail_grille_tranche" style="font-size: 13px; font-weight: 600; color: #334155; margin-top: 2px;"></div>
            </div>
            <div style="flex: 1;">
              <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Tarif Vente Appliqué</span>
              <div id="detail_grille_prix" style="font-size: 15px; font-weight: 800; color: #059669; margin-top: 2px;"></div>
            </div>
          </div>
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Statut Actuel</span>
            <div id="detail_grille_statut" style="font-size: 13px; font-weight: 700; margin-top: 2px;"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
        <button type="button" class="btn btn-secondary" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
