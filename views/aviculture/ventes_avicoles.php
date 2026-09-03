<?php
require_once __DIR__ . '/../../public/inc/header.php';
$clients = $clients ?? [];
$etiquettes = $etiquettes ?? [];
?>

<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">
      
      <!-- Page Header -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 20px; font-weight: 800; color: #0F172A; margin: 0;">Ventes Avicoles & Caisse OVOLIA</h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Vente directe de poulets pesés au comptoir & facturation clients</p>
        </div>
        <button class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;" data-bs-toggle="modal" data-bs-target="#modalVente">
          <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Nouvelle Vente / Ticket Caisse
        </button>
      </div>

      <!-- Card Table -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; max-width: 100%; box-sizing: border-box; overflow: hidden;">
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
          <table id="tableVentesAvicoles" class="table display nowrap" style="width:100%; max-width:100%; border-collapse: collapse;">
            <thead>
              <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                <th style="padding: 12px;">Code Vente</th>
                <th style="padding: 12px;">Client Avicole</th>
                <th style="padding: 12px;">Règlement</th>
                <th style="padding: 12px;">Montant Total Net</th>
                <th style="padding: 12px;">Date Vente</th>
                <th style="padding: 12px;">Agent Caisse</th>
                <th style="padding: 12px; text-align: center;">Statut</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Modal Nouvelle Vente -->
<div class="modal fade" id="modalVente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 800px; margin: auto;">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: #1E3A5F; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0;">🛒 Caisse de Vente Directe - Volaille OVOLIA</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formVente">
        <div class="modal-body" style="padding: 20px;">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Client Avicole</label>
              <select name="client_avicole_code" class="form-select" style="border-radius: 8px; height: 42px;">
                <option value="">-- Client Comptoir / Particulier --</option>
                <?php foreach ($clients as $c): ?>
                  <option value="<?= htmlspecialchars($c['code_client_avicole']) ?>">
                    <?= htmlspecialchars($c['nom_client_avicole']) ?> (<?= htmlspecialchars($c['type_client_avicole']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Mode de Règlement</label>
              <select name="type_reglement" class="form-select" style="border-radius: 8px; height: 42px;">
                <option value="comptant_especes">Espèces / Comptoir</option>
                <option value="mobile_money">Mobile Money (Wave / Orange)</option>
                <option value="cheque">Chèque</option>
                <option value="virement">Virement BTP</option>
                <option value="credit">Vente à Crédit Client</option>
              </select>
            </div>
          </div>

          <!-- Sélection des volailles pesées en stock -->
          <label style="font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 8px; display: block;">
            Sélectionner les Volailles Pesées en Stock (Étiquettes Dispo) *
          </label>

          <?php if (empty($etiquettes)): ?>
            <div style="background: #FEF3C7; color: #92400E; padding: 12px; border-radius: 8px; font-size: 13px;">
              ⚠️ Aucune volaille pesée n'est actuellement disponible en stock. Veuillez d'abord réaliser une pesée dans le module <strong>Pesées & Étiquettes</strong>.
            </div>
          <?php else: ?>
            <div style="max-height: 250px; overflow-y: auto; border: 1px solid #E2E8F0; border-radius: 8px; padding: 12px; background: #F8FAFC;">
              <div class="row g-2">
                <?php foreach ($etiquettes as $etiq): ?>
                  <div class="col-md-6">
                    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; padding: 8px 12px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
                      <div class="form-check d-flex align-items-center gap-2" style="margin: 0;">
                        <input class="form-check-input chk-etiq" type="checkbox" name="etiquettes[]" value="<?= htmlspecialchars($etiq['code_etiquette']) ?>" data-prix="<?= $etiq['prix_unitaire_applique'] ?>" id="chk_<?= $etiq['id_pesee'] ?>">
                        <label class="form-check-label" style="font-weight: 700; font-size: 13px; color: #0F172A; cursor: pointer;" for="chk_<?= $etiq['id_pesee'] ?>">
                          <?= htmlspecialchars($etiq['libelle_produit']) ?> (<?= htmlspecialchars($etiq['libelle_categorie_poids']) ?>)
                          <div style="font-size: 11px; color: #64748B; font-weight: 400;"><?= htmlspecialchars($etiq['code_etiquette']) ?> - Poids: <?= number_format($etiq['poids_net_reel'], 3, ',', ' ') ?> kg</div>
                        </label>
                      </div>
                      <span style="background: #DCFCE7; color: #166534; font-size: 12px; font-weight: 800; padding: 2px 8px; border-radius: 6px; white-space: nowrap; margin-left: 8px;"><?= number_format($etiq['prix_unitaire_applique'], 0, ',', ' ') ?> F</span>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <!-- Total de la Vente -->
          <div style="background: #0F172A; color: #FFFFFF; padding: 14px; border-radius: 8px; margin-top: 16px; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 700; text-transform: uppercase; font-size: 13px;">Total à Encaisser :</div>
            <div style="font-size: 24px; font-weight: 900; color: #10B981;" id="valTotalVente">0 FCFA</div>
          </div>
        </div>

        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
          <button type="button" class="btn btn-secondary" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary" style="background: #059669; border-color: #059669; border-radius: 8px; font-weight: 700; padding: 8px 18px;">Valider & Encaisser Vente</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    const baseApi = (typeof RACINE !== 'undefined') ? RACINE : '/ovolias/';

    let dt = $('#tableVentesAvicoles').DataTable({
        ajax: {
            url: baseApi + 'aviculture/apiListVentes',
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { data: 'code_vente_avicole', render: d => `<code style="font-weight:700; color:#334155; background:#F1F5F9; padding:2px 6px; border-radius:4px;">${d}</code>` },
            { data: 'client_nom', render: d => `<strong style="color:#0F172A;">${d}</strong>` },
            { data: 'type_reglement', render: d => `<span class="badge bg-secondary text-uppercase" style="font-weight:700;">${d.replace('_', ' ')}</span>` },
            { data: 'montant_total_net', render: d => `<strong style="color:#059669; font-size:14px;">${parseFloat(d||0).toLocaleString('fr-FR')} FCFA</strong>` },
            { data: 'date_vente', render: d => d ? new Date(d).toLocaleDateString('fr-FR') : '-' },
            { data: 'agent_nom', render: d => d || 'Caisse' },
            { data: 'statut_vente', className: 'text-center', render: d => `<span style="background: #DCFCE7; color: #166534; font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 12px;">Validée</span>` }
        ],
        language: { url: baseApi + 'json/datatables-i18n-fr-FR.json' },
        drawCallback: function() { if (window.lucide) lucide.createIcons(); }
    });

    $(document).on('change', '.chk-etiq', function() {
        let total = 0;
        $('.chk-etiq:checked').each(function() {
            total += parseFloat($(this).data('prix')) || 0;
        });
        $('#valTotalVente').text(total.toLocaleString('fr-FR') + ' FCFA');
    });

    $('#formVente').on('submit', function(e) {
        e.preventDefault();
        $.post(baseApi + 'aviculture/addVente', $(this).serialize(), function(res) {
            if (res.status === 'success' || res.success) {
                if (typeof showToast === 'function') showToast(res.message, 'success');
                else alert(res.message);
                $('#modalVente').modal('hide');
                dt.ajax.reload();
                location.reload();
            } else {
                alert(res.message || 'Erreur lors de la vente');
            }
        }, 'json');
    });
});
</script>

<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
