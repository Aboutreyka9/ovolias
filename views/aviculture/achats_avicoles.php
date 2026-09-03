<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php $fournisseurs = $fournisseurs ?? []; ?>

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

<!-- Modal Nouveau Bon d'Achat Intrants -->
<div class="modal fade" id="modalAchat" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 560px; margin: auto;">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: #1E3A5F; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="shopping-cart" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Enregistrement d'Achat Produit Fini
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formAchat">
        <div class="modal-body" style="padding: 20px;">
          <div style="margin-bottom: 16px;">
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

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Gamme Produit Fini *</label>
              <select name="categorie_intrant" class="form-select" style="border-radius: 8px; height: 42px;" required>
                <option value="poulets_frais">Poulets entiers frais</option>
                <option value="oeufs_frais">Œufs frais (Plateaux)</option>
                <option value="poulets_fumes">Poulets fumés</option>
                <option value="poules_pondeuses">Poules pondeuses</option>
                <option value="pintades">Pintades fraîches</option>
              </select>
            </div>
            <div class="col-md-6">
              <label style="font-weight: 600; font-size: 13px; color: #475569; margin-bottom: 6px; display: block;">N° Facture Fournisseur</label>
              <input type="text" name="numero_facture_fournisseur" class="form-control" style="border-radius: 8px; height: 42px;" placeholder="Ex: FAC-2026-089">
            </div>
          </div>

          <div style="margin-bottom: 16px;">
            <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Désignation / Détails du Lot *</label>
            <input type="text" name="libelle_article" class="form-control" style="border-radius: 8px; height: 42px;" required placeholder="Ex: Lot de 200 Poulets Frais Prêts à Payer">
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Quantité *</label>
              <input type="number" step="0.01" name="quantite" id="inputAchatQte" class="form-control" style="border-radius: 8px; height: 42px;" required placeholder="200">
            </div>
            <div class="col-md-4">
              <label style="font-weight: 600; font-size: 13px; color: #475569; margin-bottom: 6px; display: block;">Unité</label>
              <input type="text" name="unite_mesure" class="form-control" style="border-radius: 8px; height: 42px;" placeholder="Pièces / Plateaux">
            </div>
            <div class="col-md-4">
              <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Prix Unitaire *</label>
              <input type="number" step="100" name="prix_unitaire" id="inputAchatPU" class="form-control" style="border-radius: 8px; height: 42px;" required placeholder="2200">
            </div>
          </div>

          <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 12px 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div style="font-weight: 700; font-size: 13px; color: #334155;">Montant Total Calculé :</div>
            <div style="font-size: 20px; font-weight: 900; color: #DC2626;" id="valTotalAchat">0 FCFA</div>
          </div>

          <div style="margin-bottom: 16px;">
            <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Statut du Règlement</label>
            <select name="statut_reglement" class="form-select" style="border-radius: 8px; height: 42px;">
              <option value="paye">Payé Intégralement (Caisse / Banques)</option>
              <option value="partiel">Règlement Partiel</option>
              <option value="impaye">Achat à Crédit (Dette Fournisseur)</option>
            </select>
          </div>
        </div>

        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
          <button type="button" class="btn btn-secondary" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; border-radius: 8px; font-weight: 700; padding: 8px 18px;">Enregistrer Achat</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    const baseApi = (typeof RACINE !== 'undefined') ? RACINE : '/ovolias/';

    function getProduitBadge(cat) {
        cat = (cat || 'poulets_frais').toLowerCase();
        const styles = {
            'poulets_frais': { bg: '#EFF6FF', color: '#1D4ED8', label: 'Poulets entiers frais' },
            'oeufs_frais': { bg: '#FEF3C7', color: '#B45309', label: 'Œufs frais' },
            'poulets_fumes': { bg: '#FFF7ED', color: '#C2410C', label: 'Poulets fumés' },
            'poules_pondeuses': { bg: '#F5F3FF', color: '#6D28D9', label: 'Poules pondeuses' },
            'pintades': { bg: '#ECFDF5', color: '#047857', label: 'Pintades fraîches' }
        };
        const s = styles[cat] || { bg: '#F1F5F9', color: '#475569', label: cat };
        return `<span style="background: ${s.bg}; color: ${s.color}; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px;">${s.label}</span>`;
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
            { data: 'categorie_intrant', render: d => getProduitBadge(d) },
            { data: 'numero_facture_fournisseur', render: d => d || '-' },
            { data: 'montant_total', render: d => `<strong style="color:#DC2626; font-size:14px;">${parseFloat(d||0).toLocaleString('fr-FR')} FCFA</strong>` },
            { data: 'date_achat', render: d => d ? new Date(d).toLocaleDateString('fr-FR') : '-' },
            { data: 'statut_reglement', className: 'text-center', render: d => d === 'paye' ? `<span style="background: #DCFCE7; color: #166534; font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 12px;">Payé</span>` : `<span style="background: #FEF3C7; color: #92400E; font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 12px;">${d}</span>` }
        ],
        language: { url: baseApi + 'json/datatables-i18n-fr-FR.json' },
        drawCallback: function() { if (window.lucide) lucide.createIcons(); }
    });

    $('#inputAchatQte, #inputAchatPU').on('input keyup change', function() {
        let qte = parseFloat($('#inputAchatQte').val()) || 0;
        let pu = parseFloat($('#inputAchatPU').val()) || 0;
        let tot = qte * pu;
        $('#valTotalAchat').text(tot.toLocaleString('fr-FR') + ' FCFA');
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
