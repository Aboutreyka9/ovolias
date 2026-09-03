<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">
      
      <!-- Page Header -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0;">Catalogue des Produits Avicoles Finis</h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Gestion des volailles (Poulets entiers, Œufs, Poulets fumés, Poules pondeuses & Pintades)</p>
        </div>
        <button id="btnOpenAddProduit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;" data-bs-toggle="modal" data-bs-target="#modalAddProduit">
          <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Nouveau Produit
        </button>
      </div>

      <!-- Card Table -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; max-width: 100%; box-sizing: border-box; overflow: hidden;">
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
          <table id="tableProduitsAvicoles" class="table display nowrap" style="width:100%; max-width:100%; border-collapse: collapse;">
            <thead>
              <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                <th style="padding: 12px;">Code Produit</th>
                <th style="padding: 12px;">Désignation Produit</th>
                <th style="padding: 12px;">Conditionnement</th>
                <th style="padding: 12px;">Unité Mesure</th>
                <th class="text-center" style="padding: 12px;">Grille Poids</th>
                <th class="text-center" style="padding: 12px;">Statut</th>
                <th class="text-end" style="padding: 12px;">Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Modal Formulaire Produit (Ajout & Édition) -->
<div class="modal fade" id="modalAddProduit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 560px; margin: auto;">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: #1E3A5F; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" id="modalProduitTitle" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="package-plus" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Nouveau Produit Avicole
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formAddProduit">
        <input type="hidden" name="id_produit_aviculture" id="produit_id_field" value="">
        <div class="modal-body" style="padding: 20px;">
          <div style="margin-bottom: 16px;">
            <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Désignation Produit *</label>
            <input type="text" name="libelle_produit" id="produit_libelle_field" class="form-control" style="border-radius: 8px; height: 42px;" required placeholder="Ex: Poulets entiers frais OVOLIA">
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Conditionnement *</label>
              <select name="type_conditionnement" id="produit_cond_field" class="form-select" style="border-radius: 8px; height: 42px;">
                <option value="piece_au_poids">Pièce au poids réel</option>
                <option value="plateau_oeufs">Plateau d'œufs</option>
                <option value="unite_fixe">Unité fixe / Pièce</option>
              </select>
            </div>
            <div class="col-md-6">
              <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Unité de Mesure *</label>
              <input type="text" name="unite_mesure" id="produit_unite_field" class="form-control" style="border-radius: 8px; height: 42px;" required placeholder="Ex: kg, plateau, pièce">
            </div>
          </div>

          <div style="margin-bottom: 16px;">
            <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Soumis à la Grille de Poids ?</label>
            <select name="soumis_grille_poids" id="produit_soumis_field" class="form-select" style="border-radius: 8px; height: 42px;">
              <option value="1">Oui (Classé par tranches Essentiel, Classique, Grand, Extra...)</option>
              <option value="0">Non (Tarif unitaire fixe)</option>
            </select>
          </div>

          <div style="margin-bottom: 16px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569; margin-bottom: 6px; display: block;">Description / Remarques</label>
            <textarea name="description_produit" id="produit_desc_field" class="form-control" style="border-radius: 8px; height: 80px;" placeholder="Description détaillée du produit avicole..."></textarea>
          </div>
        </div>
        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
          <button type="button" class="btn btn-secondary" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" id="btnSubmitProduit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; border-radius: 8px; font-weight: 700; padding: 8px 18px;">Enregistrer Produit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Détails Produit -->
<div class="modal fade" id="modalDetailsProduit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 520px; margin: auto;">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: #1E3A5F; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="eye" style="width: 20px; height: 20px; color: #38BDF8;"></i> Détails du Produit Avicole
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="padding: 20px;">
        <div style="display: flex; flex-direction: column; gap: 14px;">
          <div style="border-bottom: 1px solid #E2E8F0; padding-bottom: 8px;">
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Code Produit</span>
            <div id="detail_code" style="font-size: 15px; font-weight: 800; color: #0F172A; margin-top: 2px;"></div>
          </div>
          <div style="border-bottom: 1px solid #E2E8F0; padding-bottom: 8px;">
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Désignation</span>
            <div id="detail_libelle" style="font-size: 14px; font-weight: 700; color: #1E293B; margin-top: 2px;"></div>
          </div>
          <div style="display: flex; gap: 16px; border-bottom: 1px solid #E2E8F0; padding-bottom: 8px;">
            <div style="flex: 1;">
              <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Conditionnement</span>
              <div id="detail_conditionnement" style="font-size: 13px; font-weight: 600; color: #334155; margin-top: 2px;"></div>
            </div>
            <div style="flex: 1;">
              <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Unité de Mesure</span>
              <div id="detail_unite" style="font-size: 13px; font-weight: 600; color: #334155; margin-top: 2px;"></div>
            </div>
          </div>
          <div style="border-bottom: 1px solid #E2E8F0; padding-bottom: 8px;">
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Grille de Poids Tarifaire</span>
            <div id="detail_grille" style="font-size: 13px; font-weight: 600; color: #334155; margin-top: 2px;"></div>
          </div>
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Description / Remarques</span>
            <div id="detail_description" style="font-size: 13px; color: #475569; margin-top: 2px; font-style: italic;"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
        <button type="button" class="btn btn-secondary" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    const baseApi = (typeof RACINE !== 'undefined') ? RACINE : '/ovolias/';

    var table = $('#tableProduitsAvicoles').DataTable({
        ajax: baseApi + 'aviculture/apiListProduits',
        processing: true,
        autoWidth: false,
        columns: [
            { 
                data: 'code_produit_aviculture', 
                width: '140px', 
                render: d => `<code style="font-weight:700; color:#334155; background:#F1F5F9; padding:2px 6px; border-radius:4px;">${d||'-'}</code>` 
            },
            { 
                data: 'libelle_produit', 
                render: d => `<strong style="color:#0F172A; font-size:14px;">${d || '-'}</strong>` 
            },
            { 
                data: 'type_conditionnement',
                render: function(d) {
                    if (d === 'plateau_oeufs') return "Plateau d'œufs";
                    if (d === 'unite_fixe') return "Unité fixe";
                    return "Pièce au poids net";
                }
            },
            { data: 'unite_mesure', defaultContent: 'kg' },
            { 
                data: 'soumis_grille_poids', 
                className: 'text-center',
                render: d => parseInt(d) === 1 ? `<span style="background:#DCFCE7; color:#166534; font-size:12px; font-weight:700; padding:3px 8px; border-radius:12px;">Oui (Tranches)</span>` : `<span style="background:#F1F5F9; color:#475569; font-size:12px; font-weight:700; padding:3px 8px; border-radius:12px;">Non</span>`
            },
            { 
                data: 'statut_produit', 
                width: '90px', 
                className: 'text-center', 
                render: function(d, type, row) {
                    var isActif = (d === 'actif');
                    var checkedAttr = isActif ? 'checked' : '';
                    return '<div style="display:flex; justify-content:center; align-items:center;">' +
                           '<label style="position:relative; display:inline-block; width:38px; height:20px; margin:0; cursor:pointer;" title="' + (isActif ? 'Actif - Cliquez pour désactiver' : 'Inactif - Cliquez pour activer') + '">' +
                           '<input type="checkbox" class="toggle-statut-produit" data-id="' + row.id_produit_aviculture + '" ' + checkedAttr + ' style="opacity:0; width:0; height:0;">' +
                           '<span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:' + (isActif ? '#15803D' : '#CBD5E1') + '; transition:.3s; border-radius:20px;">' +
                           '<span style="position:absolute; content:\'\'; height:14px; width:14px; left:' + (isActif ? '20px' : '3px') + '; bottom:3px; background-color:white; transition:.3s; border-radius:50%;"></span>' +
                           '</span>' +
                           '</label>' +
                           '</div>';
                } 
            },
            { 
                data: null, 
                orderable: false, 
                className: 'text-end',
                render: function(d, type, row) {
                    var jsonStr = JSON.stringify(row).replace(/'/g, "&#39;");
                    return '<button class="btn btn-sm btn-secondary btn-edit-produit" data-produit=\'' + jsonStr + '\' style="margin-right:6px; font-weight:600; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><i data-lucide="edit" style="width:14px;height:14px;"></i> Éditer</button>' +
                           '<button class="btn btn-sm btn-secondary btn-details-produit" data-produit=\'' + jsonStr + '\' style="font-weight:600; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><i data-lucide="eye" style="width:14px;height:14px;"></i> Détails</button>';
                } 
            }
        ],
        language: { url: baseApi + 'json/datatables-i18n-fr-FR.json' },
        drawCallback: function() { if (window.lucide) lucide.createIcons(); }
    });

    $(document).on('change', '.toggle-statut-produit', function() {
        var id = $(this).data('id');
        var isChecked = $(this).is(':checked');
        var $input = $(this);

        $.ajax({
            url: baseApi + 'aviculture/changerProduit',
            type: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            data: {
                id: id,
                statut: isChecked ? 'actif' : 'inactif',
                csrf_token: '<?= Validator::generateCsrfToken() ?>'
            },
            dataType: 'json',
            success: function(res) {
                if (res.status === 1 || res.success || res.status === 'success') {
                    if (window.toastr) toastr.success(res.message || 'Statut mis à jour avec succès');
                    table.ajax.reload(null, false);
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

    $('#btnOpenAddProduit').on('click', function() {
        $('#formAddProduit')[0].reset();
        $('#produit_id_field').val('');
        $('#modalProduitTitle').html('<i data-lucide="package-plus" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Nouveau Produit Avicole');
        $('#btnSubmitProduit').text('Enregistrer Produit');
        if (window.lucide) lucide.createIcons();
    });

    $(document).on('click', '.btn-edit-produit', function() {
        var prod = $(this).data('produit');
        if (typeof prod === 'string') prod = JSON.parse(prod);
        
        $('#produit_id_field').val(prod.id_produit_aviculture);
        $('#produit_libelle_field').val(prod.libelle_produit || '');
        $('#produit_cond_field').val(prod.type_conditionnement || 'piece_au_poids');
        $('#produit_unite_field').val(prod.unite_mesure || 'kg');
        $('#produit_soumis_field').val(prod.soumis_grille_poids || '1');
        $('#produit_desc_field').val(prod.description_produit || '');

        $('#modalProduitTitle').html('<i data-lucide="edit" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Éditer Produit Avicole');
        $('#btnSubmitProduit').text('Enregistrer Modifications');
        if (window.lucide) lucide.createIcons();

        var modalEl = document.getElementById('modalAddProduit');
        var bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        bsModal.show();
    });

    $(document).on('click', '.btn-details-produit', function() {
        var prod = $(this).data('produit');
        if (typeof prod === 'string') prod = JSON.parse(prod);

        $('#detail_code').text(prod.code_produit_aviculture || '-');
        $('#detail_libelle').text(prod.libelle_produit || '-');
        
        var condText = 'Pièce au poids net';
        if (prod.type_conditionnement === 'plateau_oeufs') condText = 'Plateau d\'œufs';
        else if (prod.type_conditionnement === 'unite_fixe') condText = 'Unité fixe';
        $('#detail_conditionnement').text(condText);

        $('#detail_unite').text(prod.unite_mesure || 'kg');
        $('#detail_grille').text(parseInt(prod.soumis_grille_poids) === 1 ? 'Oui (Classé par tranches de poids)' : 'Non (Tarif unitaire fixe)');
        $('#detail_description').text(prod.description_produit || 'Aucune description saisie.');

        if (window.lucide) lucide.createIcons();

        var modalEl = document.getElementById('modalDetailsProduit');
        var bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        bsModal.show();
    });

    $('#formAddProduit').on('submit', function(e) {
        e.preventDefault();
        var id = $('#produit_id_field').val();
        var url = id ? (baseApi + 'aviculture/editProduit') : (baseApi + 'aviculture/addProduit');

        $.post(url, $(this).serialize(), function(res) {
            if (res.status === 'success' || res.status === 1 || res.success) {
                if (window.toastr) toastr.success(res.message || 'Opération réussie');
                else alert(res.message);

                var modalEl = document.getElementById('modalAddProduit');
                var bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) bsModal.hide();

                table.ajax.reload(null, false);
            } else {
                if (window.toastr) toastr.error(res.message || 'Erreur lors de l\'enregistrement');
                else alert(res.message || 'Erreur lors de l\'enregistrement');
            }
        }, 'json');
    });
});
</script>

<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
