<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$fournisseurs = $fournisseurs ?? []; 
$produits = $produits ?? [];
?>

<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">
      
      <!-- Page Header -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0;">Achats de Produits Avicoles Finis OVOLIA</h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Approvisionnement direct auprès des fournisseurs (Poulets frais, Œufs, Poulets fumés & Poules pondeuses)</p>
        </div>
        <button class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;" data-bs-toggle="modal" data-bs-target="#modalAchat">
          <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Nouveau Bon d'Achat Produit
        </button>
      </div>

      <!-- Card Table -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; max-width: 100%; box-sizing: border-box; overflow: hidden;">
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
          <table id="tableAchatsAvicoles" class="table display nowrap" style="width:100%; max-width:100%; border-collapse: collapse;">
            <thead>
              <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                <th style="padding: 12px;">Code Achat</th>
                <th style="padding: 12px;">Fournisseur</th>
                <th style="padding: 12px;">Gamme Produit</th>
                <th style="padding: 12px;">N° Facture FRS</th>
                <th style="padding: 12px;">Montant Total</th>
                <th style="padding: 12px;">Date Achat</th>
                <th style="padding: 12px; text-align: center;">Statut Règlement</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Modal Nouveau Bon d'Achat Intrants Multi-Produits -->
<div class="modal fade" id="modalAchat" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: #1E3A5F; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="shopping-cart" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Enregistrement Bon d'Achat Produit Fini / Multi-Articles
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formAchat">
        <div class="modal-body" style="padding: 20px;">
          
          <div class="row g-3 mb-3">
            <div class="col-md-5">
              <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Fournisseur / Producteur *</label>
              <select name="fournisseur_avicole_code" class="form-select" style="border-radius: 8px; height: 42px;" required>
                <option value="">-- Sélectionner un fournisseur --</option>
                <?php foreach ($fournisseurs as $f): ?>
                  <option value="<?= htmlspecialchars($f['code_fournisseur_avicole']) ?>">
                    <?= htmlspecialchars($f['nom_fournisseur_avicole']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">N° Facture Fournisseur *</label>
              <div class="input-group">
                <input type="text" name="numero_facture_fournisseur" id="num_facture_input" class="form-control" style="border-radius: 8px 0 0 8px; height: 42px; font-weight: 700; color: #0F172A;" placeholder="FACT-2026-XXXX" required>
                <button type="button" class="btn btn-outline-secondary" id="btnGenereNumFacture" title="Régénérer un numéro automatique" style="border-radius: 0 8px 8px 0;">
                  <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i>
                </button>
              </div>
            </div>
            <div class="col-md-3">
              <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Statut Règlement</label>
              <select name="statut_reglement" class="form-select" style="border-radius: 8px; height: 42px;">
                <option value="paye">Payé (Caisse/Banque)</option>
                <option value="partiel">Partiel</option>
                <option value="impaye">À Crédit (Dette)</option>
              </select>
            </div>
          </div>

          <div style="border-top: 1px solid #E2E8F0; padding-top: 16px; margin-top: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
              <label style="font-weight: 800; font-size: 14px; color: #1E293B; margin: 0; display: flex; align-items: center; gap: 6px;">
                <i data-lucide="list" style="width: 18px; height: 18px; color: #2563EB;"></i> Articles & Produits Commandés
              </label>
              <button type="button" id="btnAddArticleRow" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-weight: 700; display: flex; align-items: center; gap: 4px;">
                <i data-lucide="plus" style="width: 16px; height: 16px;"></i> Ajouter un produit
              </button>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered align-middle" id="tableArticlesModal" style="border-color: #E2E8F0;">
                <thead style="background: #F8FAFC; font-size: 12px; font-weight: 800; color: #475569;">
                  <tr>
                    <th style="min-width: 220px;">Produit Avicole *</th>
                    <th style="width: 110px;">Quantité *</th>
                    <th style="width: 110px;">Unité</th>
                    <th style="width: 140px;">Prix Unit. (FCFA) *</th>
                    <th style="width: 140px; text-align: right;">Sous-Total</th>
                    <th style="width: 50px; text-align: center;"></th>
                  </tr>
                </thead>
                <tbody id="tbodyArticlesModal">
                  <!-- Injecté dynamiquement par JS -->
                </tbody>
              </table>
            </div>

            <div style="background: #F1F5F9; border: 1px solid #CBD5E1; padding: 14px 18px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; margin-top: 12px;">
              <div style="font-weight: 800; font-size: 14px; color: #1E293B;">Montant Total Global de la Commande :</div>
              <div style="font-size: 22px; font-weight: 900; color: #DC2626;" id="valTotalAchat">0 FCFA</div>
            </div>
          </div>

        </div>

        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
          <button type="button" class="btn btn-secondary" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; border-radius: 8px; font-weight: 700; padding: 8px 18px;">Valider la Commande Multi-Produits</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    const baseApi = (typeof RACINE !== 'undefined') ? RACINE : '/ovolias/';

    function getProduitBadge(row) {
        const libelle = row.produit_libelle || row.libelle_produit || row.categorie_intrant || '-';
        const cat = (row.categorie_intrant || '').toLowerCase();
        const styles = {
            'poulets_frais': { bg: '#EFF6FF', color: '#1D4ED8' },
            'oeufs_frais': { bg: '#FEF3C7', color: '#B45309' },
            'poulets_fumes': { bg: '#FFF7ED', color: '#C2410C' },
            'poules_pondeuses': { bg: '#F5F3FF', color: '#6D28D9' },
            'pintades': { bg: '#ECFDF5', color: '#047857' }
        };
        const s = styles[cat] || { bg: '#EFF6FF', color: '#1D4ED8' };
        return `<span style="background: ${s.bg}; color: ${s.color}; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px;">${libelle}</span>`;
    }

    let dt = $('#tableAchatsAvicoles').DataTable({
        ajax: {
            url: baseApi + 'aviculture/apiListAchats',
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { data: 'code_achat_avicole', render: d => `<code style="font-weight:700; color:#334155; background:#F1F5F9; padding:2px 6px; border-radius:4px;">${d}</code>` },
            { data: 'fournisseur_nom', render: d => `<strong style="color:#0F172A;">${d || '-'}</strong>` },
            { data: null, render: (d, type, row) => getProduitBadge(row) },
            { data: 'numero_facture_fournisseur', render: d => d || '-' },
            { data: 'montant_total', render: d => `<strong style="color:#DC2626; font-size:14px;">${parseFloat(d||0).toLocaleString('fr-FR')} FCFA</strong>` },
            { data: 'date_achat', render: d => d ? new Date(d).toLocaleDateString('fr-FR') : '-' },
            { data: 'statut_reglement', className: 'text-center', render: d => d === 'paye' ? `<span style="background: #DCFCE7; color: #166534; font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 12px;">Payé</span>` : `<span style="background: #FEF3C7; color: #92400E; font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 12px;">${d}</span>` }
        ],
        language: { url: baseApi + 'json/datatables-i18n-fr-FR.json' },
        drawCallback: function() { if (window.lucide) lucide.createIcons(); }
    });

    let rowIndex = 0;

    function addArticleRow(qte = '', pu = '', unite = '') {
        rowIndex++;
        let rowId = 'art_row_' + rowIndex;
        let html = `
        <tr id="${rowId}" class="article-item-row">
            <td>
                <select name="articles[${rowIndex}][produit_code]" class="form-select select-produit-row" style="border-radius: 8px; height: 38px;" required>
                    <option value="">-- Choisir un produit --</option>
                    <?php if (!empty($produits)): ?>
                        <?php foreach ($produits as $p): ?>
                            <option value="<?= htmlspecialchars($p['code_produit_aviculture']) ?>" data-unite="<?= htmlspecialchars($p['unite_mesure'] ?? 'Pièces') ?>">
                                <?= htmlspecialchars($p['libelle_produit']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
            <td>
                <input type="number" step="0.01" min="0.01" name="articles[${rowIndex}][quantite]" class="form-control input-qte-row" style="border-radius: 8px; height: 38px;" placeholder="Qte" value="${qte}" required>
            </td>
            <td>
                <input type="text" name="articles[${rowIndex}][unite_mesure]" class="form-control input-unite-row" style="border-radius: 8px; height: 38px;" placeholder="Ex: kg, sacs" value="${unite}">
            </td>
            <td>
                <input type="number" step="50" min="0" name="articles[${rowIndex}][prix_unitaire]" class="form-control input-pu-row" style="border-radius: 8px; height: 38px;" placeholder="FCFA" value="${pu}" required>
            </td>
            <td class="text-end fw-bold align-middle cell-stot-row" style="color: #0F172A; font-size: 13px;">
                0 FCFA
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-remove-row" style="color: #EF4444; border: none; background: #FEE2E2; border-radius: 6px; padding: 4px 8px;" title="Supprimer la ligne">
                    <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                </button>
            </td>
        </tr>`;

        $('#tbodyArticlesModal').append(html);
        if (window.lucide) lucide.createIcons();
        recalcTotals();
    }

    // Initialiser avec au moins 1 ligne
    addArticleRow();

    $('#btnAddArticleRow').on('click', function() {
        addArticleRow();
    });

    $(document).on('click', '.btn-remove-row', function() {
        if ($('#tbodyArticlesModal tr').length > 1) {
            $(this).closest('tr').remove();
            recalcTotals();
        } else {
            if (window.toastr) toastr.warning("La commande doit comporter au moins un produit.");
        }
    });

    $(document).on('change', '.select-produit-row', function() {
        let opt = $(this).find(':selected');
        let u = opt.data('unite') || 'Pièces';
        let $tr = $(this).closest('tr');
        if (!$tr.find('.input-unite-row').val()) {
            $tr.find('.input-unite-row').val(u);
        }
    });

    $(document).on('input keyup change', '.input-qte-row, .input-pu-row', function() {
        recalcTotals();
    });

    function recalcTotals() {
        let grandTotal = 0;
        $('#tbodyArticlesModal tr').each(function() {
            let qte = parseFloat($(this).find('.input-qte-row').val()) || 0;
            let pu = parseFloat($(this).find('.input-pu-row').val()) || 0;
            let stot = qte * pu;
            grandTotal += stot;
            $(this).find('.cell-stot-row').text(stot.toLocaleString('fr-FR') + ' FCFA');
        });
        $('#valTotalAchat').text(grandTotal.toLocaleString('fr-FR') + ' FCFA');
    }

    function chargerNumeroFactureAuto() {
        $.get(baseApi + 'aviculture/genererNumFacture', function(res) {
            if (res && res.numero_facture) {
                $('#num_facture_input').val(res.numero_facture);
            }
        }, 'json');
    }

    var modalAchatEl = document.getElementById('modalAchat');
    if (modalAchatEl) {
        modalAchatEl.addEventListener('show.bs.modal', function() {
            chargerNumeroFactureAuto();
        });
    }

    $('#btnGenereNumFacture').on('click', function() {
        chargerNumeroFactureAuto();
    });

    $('#formAchat').on('submit', function(e) {
        e.preventDefault();
        $.post(baseApi + 'aviculture/addAchat', $(this).serialize(), function(res) {
            if (res.status === 'success' || res.success) {
                if (window.toastr) toastr.success(res.message || 'Achat enregistré avec succès');
                else alert(res.message);
                
                var modalEl = document.getElementById('modalAchat');
                var bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) bsModal.hide();

                // Réinitialiser le formulaire
                $('#formAchat')[0].reset();
                $('#tbodyArticlesModal').empty();
                rowIndex = 0;
                addArticleRow();
                chargerNumeroFactureAuto();

                dt.ajax.reload(null, false);
            } else {
                if (window.toastr) toastr.error(res.message || 'Erreur lors de l\'enregistrement');
                else alert(res.message || 'Erreur lors de l\'enregistrement');
            }
        }, 'json');
    });
});
</script>

<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
