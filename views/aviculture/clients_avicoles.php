<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">
      
      <!-- Page Header -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0;">Répertoire des Clients Avicoles</h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Grossistes, détaillants, rôtisseries & clients particuliers OVOLIA</p>
        </div>
        <button id="btnOpenAddClient" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;" data-bs-toggle="modal" data-bs-target="#modalAddClient">
          <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i> Nouveau Client Avicole
        </button>
      </div>

      <!-- Card Table -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; max-width: 100%; box-sizing: border-box; overflow: hidden;">
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
          <table id="tableClientsAvicoles" class="table display nowrap" style="width:100%; max-width:100%; border-collapse: collapse;">
            <thead>
              <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;">Code Client</th>
                <th style="padding: 12px;">Nom / Raison Sociale</th>
                <th style="padding: 12px;">Type</th>
                <th style="padding: 12px;">Téléphone</th>
                <th style="padding: 12px;">Adresse</th>
                <th style="padding: 12px;">Solde Compte</th>
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

<!-- Modal Formulaire Client (Ajout & Édition) -->
<div class="modal fade" id="modalAddClient" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 560px; margin: auto;">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: #1E3A5F; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" id="modalClientTitle" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="user-plus" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Nouveau Client Avicole
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formAddClient">
        <input type="hidden" name="id_client_avicole" id="client_id_field" value="">
        <div class="modal-body" style="padding: 20px;">
          <div style="margin-bottom: 16px;">
            <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Nom / Raison Sociale *</label>
            <input type="text" name="nom_client_avicole" id="client_nom_field" class="form-control" style="border-radius: 8px; height: 42px;" required placeholder="Ex: Rôtisserie le Poulet d'Or">
          </div>
          <div style="margin-bottom: 16px;">
            <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Type de Client</label>
            <select name="type_client_avicole" id="client_type_field" class="form-select" style="border-radius: 8px; height: 42px;">
              <option value="particulier">Particulier</option>
              <option value="rotisserie">Rôtisserie</option>
              <option value="grossiste">Grossiste</option>
              <option value="detaillant">Détaillant</option>
              <option value="supermarche">Supermarché</option>
            </select>
          </div>
          <div style="margin-bottom: 16px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569; margin-bottom: 6px; display: block;">Téléphone</label>
            <input type="text" name="telephone_client_avicole" id="client_tel_field" class="form-control" style="border-radius: 8px; height: 42px;" placeholder="Ex: 0701020304">
          </div>
          <div style="margin-bottom: 16px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569; margin-bottom: 6px; display: block;">Adresse / Localisation</label>
            <input type="text" name="adresse_client_avicole" id="client_adresse_field" class="form-control" style="border-radius: 8px; height: 42px;" placeholder="Ex: Abidjan, Cocody">
          </div>
        </div>
        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
          <button type="button" class="btn btn-secondary" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" id="btnSubmitClient" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; border-radius: 8px; font-weight: 700; padding: 8px 18px;">Enregistrer Client</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Details Client -->
<div class="modal fade" id="modalDetailsClient" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 560px; margin: auto;">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: #0284C7; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="eye" style="width: 20px; height: 20px; color: #E0F2FE;"></i> Fiche Client Avicole
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="padding: 20px;">
        <table class="table table-borderless" style="margin: 0; font-size: 14px;">
          <tbody>
            <tr>
              <th style="color: #64748B; width: 40%; padding: 8px 0;">Code Client :</th>
              <td id="det_code" style="font-weight: 700; color: #0F172A; padding: 8px 0;">-</td>
            </tr>
            <tr>
              <th style="color: #64748B; padding: 8px 0;">Nom / Raison Sociale :</th>
              <td id="det_nom" style="font-weight: 700; color: #0F172A; padding: 8px 0;">-</td>
            </tr>
            <tr>
              <th style="color: #64748B; padding: 8px 0;">Type :</th>
              <td id="det_type" style="padding: 8px 0;">-</td>
            </tr>
            <tr>
              <th style="color: #64748B; padding: 8px 0;">Téléphone :</th>
              <td id="det_tel" style="font-weight: 600; padding: 8px 0;">-</td>
            </tr>
            <tr>
              <th style="color: #64748B; padding: 8px 0;">Adresse :</th>
              <td id="det_adresse" style="padding: 8px 0;">-</td>
            </tr>
            <tr>
              <th style="color: #64748B; padding: 8px 0;">Solde Compte :</th>
              <td id="det_solde" style="font-weight: 800; color: #059669; padding: 8px 0;">-</td>
            </tr>
            <tr>
              <th style="color: #64748B; padding: 8px 0;">Statut :</th>
              <td id="det_statut" style="padding: 8px 0;">-</td>
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
    
    // Type client badge styling
    function getTypeBadge(type) {
        type = (type || 'particulier').toLowerCase();
        const styles = {
            'rotisserie': { bg: '#FFF7ED', color: '#C2410C', label: 'Rôtisserie' },
            'grossiste': { bg: '#EFF6FF', color: '#1D4ED8', label: 'Grossiste' },
            'detaillant': { bg: '#F5F3FF', color: '#6D28D9', label: 'Détaillant' },
            'supermarche': { bg: '#ECFDF5', color: '#047857', label: 'Supermarché' },
            'particulier': { bg: '#F1F5F9', color: '#475569', label: 'Particulier' }
        };
        const s = styles[type] || styles['particulier'];
        return `<span style="background: ${s.bg}; color: ${s.color}; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px; text-transform: capitalize;">${s.label}</span>`;
    }

    var table = $('#tableClientsAvicoles').DataTable({
        ajax: baseApi + 'aviculture/apiListClients',
        processing: true,
        autoWidth: false,
        columns: [
            { data: 'id_client_avicole', defaultContent: '-', width: '50px' },
            { 
                data: 'code_client_avicole', 
                width: '120px', 
                render: function(d) {
                    if (!d) return '-';
                    return '<code style="font-weight:700; color:#334155; background:#F1F5F9; padding:2px 6px; border-radius:4px;">' + d + '</code>';
                }
            },
            { 
                data: 'nom_client_avicole', 
                render: function(d) {
                    return '<strong style="color:#0F172A;">' + (d || '-') + '</strong>';
                }
            },
            { 
                data: 'type_client_avicole', 
                render: function(d) {
                    return getTypeBadge(d);
                }
            },
            { data: 'telephone_client_avicole', defaultContent: '-' },
            { data: 'adresse_client_avicole', defaultContent: '-' },
            { 
                data: 'solde_compte_client', 
                render: function(d) {
                    return '<strong style="color:#059669;">' + parseFloat(d || 0).toLocaleString('fr-FR') + ' FCFA</strong>';
                }
            },
            { 
                data: 'statut_client_avicole', 
                width: '80px', 
                className: 'text-center', 
                render: function(d, type, row) {
                    var isActif = (d === 'actif');
                    var checkedAttr = isActif ? 'checked' : '';
                    return '<div style="display:flex; justify-content:center; align-items:center;">' +
                           '<label style="position:relative; display:inline-block; width:38px; height:20px; margin:0; cursor:pointer;" title="' + (isActif ? 'Actif - Cliquez pour désactiver' : 'Inactif - Cliquez pour activer') + '">' +
                           '<input type="checkbox" class="toggle-statut-client" data-id="' + row.id_client_avicole + '" ' + checkedAttr + ' style="opacity:0; width:0; height:0;">' +
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
                    return '<button class="btn btn-sm btn-secondary btn-edit-client" data-client=\'' + jsonStr + '\' style="margin-right:6px; font-weight:600; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><i data-lucide="edit" style="width:14px;height:14px;"></i> Éditer</button>' +
                           '<button class="btn btn-sm btn-info btn-details-client" data-client=\'' + jsonStr + '\' style="font-weight:600; border-radius:6px; display:inline-flex; align-items:center; gap:4px; color:#fff; background:#0284C7; border-color:#0284C7;"><i data-lucide="eye" style="width:14px;height:14px;"></i> Détails</button>';
                } 
            }
        ],
        language: { url: baseApi + 'json/datatables-i18n-fr-FR.json' },
        drawCallback: function() { if (window.lucide) lucide.createIcons(); }
    });

    // Reset Modal pour Ajout
    $('#btnOpenAddClient').on('click', function() {
        $('#formAddClient')[0].reset();
        $('#client_id_field').val('');
        $('#modalClientTitle').html('<i data-lucide="user-plus" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Nouveau Client Avicole');
        $('#btnSubmitClient').text('Enregistrer Client');
        if (window.lucide) lucide.createIcons();
    });

    // Bascule de statut instantanée via Ajax
    $(document).on('change', '.toggle-statut-client', function() {
        var id = $(this).data('id');
        var isChecked = $(this).is(':checked');
        var $input = $(this);

        $.ajax({
            url: baseApi + 'aviculture/changerClient',
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

    // Bouton Éditer Client
    $(document).on('click', '.btn-edit-client', function() {
        var client = $(this).data('client');
        if (typeof client === 'string') client = JSON.parse(client);
        
        $('#client_id_field').val(client.id_client_avicole);
        $('#client_nom_field').val(client.nom_client_avicole || '');
        $('#client_type_field').val(client.type_client_avicole || 'particulier');
        $('#client_tel_field').val(client.telephone_client_avicole || '');
        $('#client_adresse_field').val(client.adresse_client_avicole || '');

        $('#modalClientTitle').html('<i data-lucide="edit" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Éditer Client Avicole');
        $('#btnSubmitClient').text('Enregistrer Modifications');
        if (window.lucide) lucide.createIcons();

        var modalEl = document.getElementById('modalAddClient');
        var bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        bsModal.show();
    });

    // Bouton Détails Client
    $(document).on('click', '.btn-details-client', function() {
        var client = $(this).data('client');
        if (typeof client === 'string') client = JSON.parse(client);

        $('#det_code').html('<code style="font-weight:700; color:#334155; background:#F1F5F9; padding:2px 6px; border-radius:4px;">' + (client.code_client_avicole || '-') + '</code>');
        $('#det_nom').text(client.nom_client_avicole || '-');
        $('#det_type').html(getTypeBadge(client.type_client_avicole));
        $('#det_tel').text(client.telephone_client_avicole || '-');
        $('#det_adresse').text(client.adresse_client_avicole || '-');
        $('#det_solde').text(parseFloat(client.solde_compte_client || 0).toLocaleString('fr-FR') + ' FCFA');
        
        var isActif = (client.statut_client_avicole === 'actif');
        $('#det_statut').html(isActif ? '<span style="background: #DCFCE7; color: #166534; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px;">Actif</span>' : '<span style="background: #FEE2E2; color: #991B1B; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px;">Inactif</span>');

        var modalEl = document.getElementById('modalDetailsClient');
        var bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        bsModal.show();
    });

    // Soumission du Formulaire (Ajout / Édition)
    $('#formAddClient').on('submit', function(e) {
        e.preventDefault();
        var id = $('#client_id_field').val();
        var url = id ? (baseApi + 'aviculture/editClient') : (baseApi + 'aviculture/addClient');

        $.post(url, $(this).serialize(), function(res) {
            if (res.status === 'success' || res.status === 1 || res.success) {
                if (window.toastr) toastr.success(res.message || 'Opération réussie');
                else alert(res.message);

                var modalEl = document.getElementById('modalAddClient');
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
