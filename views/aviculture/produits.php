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
                <th style="padding: 12px; text-align: center;">Grille Poids</th>
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
                    if (d === 'plateau_oeufs') return `<span style="background:#FEF3C7; color:#B45309; font-size:12px; font-weight:700; padding:3px 10px; border-radius:12px;">Plateau d'œufs</span>`;
                    if (d === 'unite_fixe') return `<span style="background:#F3E8FF; color:#6B21A8; font-size:12px; font-weight:700; padding:3px 10px; border-radius:12px;">Unité fixe</span>`;
                    return `<span style="background:#EFF6FF; color:#1D4ED8; font-size:12px; font-weight:700; padding:3px 10px; border-radius:12px;">Pièce au poids net</span>`;
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
                className: 'text-center', 
                render: d => d === 'actif' ? `<span style="background:#DCFCE7; color:#166534; font-size:12px; font-weight:700; padding:3px 10px; border-radius:12px;">Actif</span>` : `<span style="background:#FEE2E2; color:#991B1B; font-size:12px; font-weight:700; padding:3px 10px; border-radius:12px;">Inactif</span>`
            },
            { 
                data: null, 
                orderable: false, 
                className: 'text-end',
                render: function(d, type, row) {
                    var jsonStr = JSON.stringify(row).replace(/'/g, "&#39;");
                    return `<button class="btn btn-sm btn-secondary btn-edit-produit" data-produit='${jsonStr}' style="font-weight:600; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><i data-lucide="edit" style="width:14px;height:14px;"></i> Éditer</button>`;
                } 
            }
        ],
        language: { url: baseApi + 'json/datatables-i18n-fr-FR.json' },
        drawCallback: function() { if (window.lucide) lucide.createIcons(); }
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
