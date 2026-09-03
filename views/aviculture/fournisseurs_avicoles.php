<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">
      
      <!-- Page Header -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0;">Fournisseurs de Produits Avicoles Finis</h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Producteurs & Fournisseurs direct de Poulets frais, Œufs, Poulets fumés & Poules pondeuses</p>
        </div>
        <button id="btnOpenAddFournisseur" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;" data-bs-toggle="modal" data-bs-target="#modalAddFournisseur">
          <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Nouveau Fournisseur
        </button>
      </div>

      <!-- Card Table -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; max-width: 100%; box-sizing: border-box; overflow: hidden;">
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
          <table id="tableFournisseursAvicoles" class="table display nowrap" style="width:100%; max-width:100%; border-collapse: collapse;">
            <thead>
              <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;">Code Fournisseur</th>
                <th style="padding: 12px;">Raison Sociale</th>
                <th style="padding: 12px;">Gamme Produits Fournis</th>
                <th style="padding: 12px;">Téléphone</th>
                <th style="padding: 12px;">Adresse</th>
                <th style="padding: 12px; text-align: center;">Statut</th>
                <th style="padding: 12px; text-align: right;">Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Modal Formulaire Fournisseur (Ajout & Édition) -->
<div class="modal fade" id="modalAddFournisseur" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 560px; margin: auto;">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: #1E3A5F; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" id="modalFournisseurTitle" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="truck" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Nouveau Fournisseur Avicole
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formAddFournisseur">
        <input type="hidden" name="id_fournisseur_avicole" id="fournisseur_id_field" value="">
        <div class="modal-body" style="padding: 20px;">
          <div style="margin-bottom: 16px;">
            <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Nom / Raison Sociale *</label>
            <input type="text" name="nom_fournisseur_avicole" id="fournisseur_nom_field" class="form-control" style="border-radius: 8px; height: 42px;" required placeholder="Ex: Ferme Avicole Ivoire Volailles">
          </div>
          <div style="margin-bottom: 16px;">
            <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Gamme Principale de Produits Finis</label>
            <select name="categorie_intrants" id="fournisseur_cat_field" class="form-select" style="border-radius: 8px; height: 42px;">
              <option value="poulets_frais">Poulets entiers frais</option>
              <option value="oeufs_frais">Œufs frais (Plateaux)</option>
              <option value="poulets_fumes">Poulets fumés</option>
              <option value="poules_pondeuses">Poules pondeuses</option>
              <option value="pintades">Pintades fraîches</option>
              <option value="tous_produits">Multi-produits Volailles OVOLIA</option>
            </select>
          </div>
          <div style="margin-bottom: 16px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569; margin-bottom: 6px; display: block;">Téléphone</label>
            <input type="text" name="telephone_fournisseur_avicole" id="fournisseur_tel_field" class="form-control" style="border-radius: 8px; height: 42px;" placeholder="Ex: 0505050505">
          </div>
          <div style="margin-bottom: 16px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569; margin-bottom: 6px; display: block;">Adresse / Localisation</label>
            <input type="text" name="adresse_fournisseur_avicole" id="fournisseur_adresse_field" class="form-control" style="border-radius: 8px; height: 42px;" placeholder="Ex: Grand-Bassam, Zone Industrielle">
          </div>
        </div>
        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
          <button type="button" class="btn btn-secondary" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" id="btnSubmitFournisseur" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; border-radius: 8px; font-weight: 700; padding: 8px 18px;">Enregistrer Fournisseur</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Details Fournisseur -->
<div class="modal fade" id="modalDetailsFournisseur" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 560px; margin: auto;">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: #0284C7; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="eye" style="width: 20px; height: 20px; color: #E0F2FE;"></i> Fiche Fournisseur Avicole
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="padding: 20px;">
        <table class="table table-borderless" style="margin: 0; font-size: 14px;">
          <tbody>
            <tr>
              <th style="color: #64748B; width: 40%; padding: 8px 0;">Code Fournisseur :</th>
              <td id="det_frs_code" style="font-weight: 700; color: #0F172A; padding: 8px 0;">-</td>
            </tr>
            <tr>
              <th style="color: #64748B; padding: 8px 0;">Raison Sociale :</th>
              <td id="det_frs_nom" style="font-weight: 700; color: #0F172A; padding: 8px 0;">-</td>
            </tr>
            <tr>
              <th style="color: #64748B; padding: 8px 0;">Produits Fournis :</th>
              <td id="det_frs_cat" style="padding: 8px 0;">-</td>
            </tr>
            <tr>
              <th style="color: #64748B; padding: 8px 0;">Téléphone :</th>
              <td id="det_frs_tel" style="font-weight: 600; padding: 8px 0;">-</td>
            </tr>
            <tr>
              <th style="color: #64748B; padding: 8px 0;">Adresse :</th>
              <td id="det_frs_adresse" style="padding: 8px 0;">-</td>
            </tr>
            <tr>
              <th style="color: #64748B; padding: 8px 0;">Statut :</th>
              <td id="det_frs_statut" style="padding: 8px 0;">-</td>
            </tr>
          </tbody>
        </table>
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
    
    // Categorie intrants / produits badge styling
    function getCatBadge(cat) {
        cat = (cat || 'poulets_frais').toLowerCase();
        const styles = {
            'poulets_frais': { bg: '#EFF6FF', color: '#1D4ED8', label: 'Poulets entiers frais' },
            'oeufs_frais': { bg: '#FEF3C7', color: '#B45309', label: 'Œufs frais' },
            'poulets_fumes': { bg: '#FFF7ED', color: '#C2410C', label: 'Poulets fumés' },
            'poules_pondeuses': { bg: '#F5F3FF', color: '#6D28D9', label: 'Poules pondeuses' },
            'pintades': { bg: '#ECFDF5', color: '#047857', label: 'Pintades fraîches' },
            'tous_produits': { bg: '#F1F5F9', color: '#475569', label: 'Multi-produits Volailles' }
        };
        const s = styles[cat] || { bg: '#F1F5F9', color: '#475569', label: cat };
        return `<span style="background: ${s.bg}; color: ${s.color}; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px;">${s.label}</span>`;
    }

    var table = $('#tableFournisseursAvicoles').DataTable({
        ajax: baseApi + 'aviculture/apiListFournisseurs',
        processing: true,
        autoWidth: false,
        columns: [
            { data: 'id_fournisseur_avicole', defaultContent: '-', width: '50px' },
            { 
                data: 'code_fournisseur_avicole', 
                width: '120px', 
                render: function(d) {
                    if (!d) return '-';
                    return '<code style="font-weight:700; color:#334155; background:#F1F5F9; padding:2px 6px; border-radius:4px;">' + d + '</code>';
                }
            },
            { 
                data: 'nom_fournisseur_avicole', 
                render: function(d) {
                    return '<strong style="color:#0F172A;">' + (d || '-') + '</strong>';
                }
            },
            { 
                data: 'categorie_intrants', 
                render: function(d) {
                    return getCatBadge(d);
                }
            },
            { data: 'telephone_fournisseur_avicole', defaultContent: '-' },
            { data: 'adresse_fournisseur_avicole', defaultContent: '-' },
            { 
                data: 'statut_fournisseur_avicole', 
                width: '80px', 
                className: 'text-center', 
                render: function(d, type, row) {
                    var isActif = (d === 'actif');
                    var checkedAttr = isActif ? 'checked' : '';
                    return '<div style="display:flex; justify-content:center; align-items:center;">' +
                           '<label style="position:relative; display:inline-block; width:38px; height:20px; margin:0; cursor:pointer;" title="' + (isActif ? 'Actif - Cliquez pour désactiver' : 'Inactif - Cliquez pour activer') + '">' +
                           '<input type="checkbox" class="toggle-statut-fournisseur" data-id="' + row.id_fournisseur_avicole + '" ' + checkedAttr + ' style="opacity:0; width:0; height:0;">' +
                           '<span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:' + (isActif ? '#15803D' : '#CBD5E1') + '; transition:.3s; border-radius:20px;">' +
                           '<span style="position:absolute; content:\'\'; height:14px; width:14px; left:' + (isActif ? '20px' : '3px') + '; bottom:3px; background-color:white; transition:.3s; border-radius:50%;"></span>' +
                           '</span>' +
                           '</label>' +
                           '</div>';
                }
            },
            { 
                data: null, 
                width: '160px', 
                orderable: false, 
                className: 'text-end',
                render: function(d, type, row) {
                    var jsonStr = JSON.stringify(row).replace(/'/g, "&#39;");
                    return '<button class="btn btn-sm btn-secondary btn-edit-fournisseur" data-fournisseur=\'' + jsonStr + '\' style="margin-right:6px; font-weight:600; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><i data-lucide="edit" style="width:14px;height:14px;"></i> Éditer</button>' +
                           '<button class="btn btn-sm btn-info btn-details-fournisseur" data-fournisseur=\'' + jsonStr + '\' style="font-weight:600; border-radius:6px; display:inline-flex; align-items:center; gap:4px; color:#fff; background:#0284C7; border-color:#0284C7;"><i data-lucide="eye" style="width:14px;height:14px;"></i> Détails</button>';
                } 
            }
        ],
        language: { url: baseApi + 'json/datatables-i18n-fr-FR.json' },
        drawCallback: function() { if (window.lucide) lucide.createIcons(); }
    });

    // Reset Modal pour Ajout
    $('#btnOpenAddFournisseur').on('click', function() {
        $('#formAddFournisseur')[0].reset();
        $('#fournisseur_id_field').val('');
        $('#modalFournisseurTitle').html('<i data-lucide="truck" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Nouveau Fournisseur Avicole');
        $('#btnSubmitFournisseur').text('Enregistrer Fournisseur');
        if (window.lucide) lucide.createIcons();
    });

    // Bascule de statut instantanée via Ajax
    $(document).on('change', '.toggle-statut-fournisseur', function() {
        var id = $(this).data('id');
        var isChecked = $(this).is(':checked');
        var $input = $(this);

        $.ajax({
            url: baseApi + 'aviculture/changerFournisseur',
            type: 'POST',
            data: {
                id: id,
                csrf_token: '<?= Validator::generateCsrfToken() ?>'
            },
            dataType: 'json',
            success: function(res) {
                if (res.status === 1 || res.status === 'success' || res.success) {
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

    // Bouton Éditer Fournisseur
    $(document).on('click', '.btn-edit-fournisseur', function() {
        var frs = $(this).data('fournisseur');
        if (typeof frs === 'string') frs = JSON.parse(frs);
        
        $('#fournisseur_id_field').val(frs.id_fournisseur_avicole);
        $('#fournisseur_nom_field').val(frs.nom_fournisseur_avicole || '');
        $('#fournisseur_cat_field').val(frs.categorie_intrants || 'poulets_frais');
        $('#fournisseur_tel_field').val(frs.telephone_fournisseur_avicole || '');
        $('#fournisseur_adresse_field').val(frs.adresse_fournisseur_avicole || '');

        $('#modalFournisseurTitle').html('<i data-lucide="edit" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Éditer Fournisseur Avicole');
        $('#btnSubmitFournisseur').text('Enregistrer Modifications');
        if (window.lucide) lucide.createIcons();

        var modalEl = document.getElementById('modalAddFournisseur');
        var bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        bsModal.show();
    });

    // Bouton Détails Fournisseur
    $(document).on('click', '.btn-details-fournisseur', function() {
        var frs = $(this).data('fournisseur');
        if (typeof frs === 'string') frs = JSON.parse(frs);

        $('#det_frs_code').html('<code style="font-weight:700; color:#334155; background:#F1F5F9; padding:2px 6px; border-radius:4px;">' + (frs.code_fournisseur_avicole || '-') + '</code>');
        $('#det_frs_nom').text(frs.nom_fournisseur_avicole || '-');
        $('#det_frs_cat').html(getCatBadge(frs.categorie_intrants));
        $('#det_frs_tel').text(frs.telephone_fournisseur_avicole || '-');
        $('#det_frs_adresse').text(frs.adresse_fournisseur_avicole || '-');
        
        var isActif = (frs.statut_fournisseur_avicole === 'actif');
        $('#det_frs_statut').html(isActif ? '<span style="background: #DCFCE7; color: #166534; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px;">Actif</span>' : '<span style="background: #FEE2E2; color: #991B1B; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px;">Inactif</span>');

        var modalEl = document.getElementById('modalDetailsFournisseur');
        var bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        bsModal.show();
    });

    // Soumission du Formulaire (Ajout / Édition)
    $('#formAddFournisseur').on('submit', function(e) {
        e.preventDefault();
        var id = $('#fournisseur_id_field').val();
        var url = id ? (baseApi + 'aviculture/editFournisseur') : (baseApi + 'aviculture/addFournisseur');

        $.post(url, $(this).serialize(), function(res) {
            if (res.status === 'success' || res.status === 1 || res.success) {
                if (window.toastr) toastr.success(res.message || 'Opération réussie');
                else alert(res.message);

                var modalEl = document.getElementById('modalAddFournisseur');
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
