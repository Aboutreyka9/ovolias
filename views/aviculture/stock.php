<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>

<div class="content-wrapper" style="padding: 24px; background: #F8FAFC; min-height: 100vh;">
  <!-- EN-TÊTE PAGE ET ACTIONS -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
      <h1 style="font-size: 24px; font-weight: 900; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 10px;">
        <i data-lucide="package" style="width: 28px; height: 28px; color: #2563EB;"></i> État des Stocks &amp; Disponibilités
      </h1>
      <p style="font-size: 13px; color: #64748B; margin: 4px 0 0 0;">
        Gestion et inventaire en temps réel des produits avicoles et grilles de poids
      </p>
    </div>

    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
      <a href="<?= RACINE ?>aviculture/mouvements_stock" class="btn btn-light" style="font-weight: 700; border-radius: 8px; font-size: 13px; border: 1px solid #CBD5E1; display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px;">
        <i data-lucide="history" style="width: 16px; height: 16px; color: #475569;"></i> Journal des Mouvements
      </a>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAjustementStock" style="background: #2563EB; border-color: #2563EB; font-weight: 800; border-radius: 8px; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; box-shadow: 0 4px 12px rgba(37,99,235,0.2);">
        <i data-lucide="sliders" style="width: 16px; height: 16px;"></i> Ajustement / Inventaire Manuel
      </button>
    </div>
  </div>

  <!-- CARTES KPIS EXÉCUTIVES -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <!-- KPI 1 : PIÈCES GLOBAL -->
    <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Total Pièces / Unités</span>
          <div style="font-size: 22px; font-weight: 900; color: #0F172A; margin-top: 6px;"><?= number_format($totPieces, 0, ',', ' ') ?></div>
        </div>
        <div style="background: #EFF6FF; color: #2563EB; padding: 10px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
          <i data-lucide="layers" style="width: 22px; height: 22px;"></i>
        </div>
      </div>
      <div style="font-size: 12px; color: #64748B; margin-top: 8px; font-weight: 600;">
        Stock physique cumulé
      </div>
    </div>

    <!-- KPI 2 : POIDS TOTAL KG -->
    <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Poids Total En Stock</span>
          <div style="font-size: 22px; font-weight: 900; color: #0369A1; margin-top: 6px;"><?= number_format($totPoidsKg, 2, ',', ' ') ?> <small style="font-size: 14px;">Kg</small></div>
        </div>
        <div style="background: #E0F2FE; color: #0284C7; padding: 10px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
          <i data-lucide="scale" style="width: 22px; height: 22px;"></i>
        </div>
      </div>
      <div style="font-size: 12px; color: #64748B; margin-top: 8px; font-weight: 600;">
        Volume pesé global
      </div>
    </div>

    <!-- KPI 3 : VALEUR ESTIMÉE DU STOCK -->
    <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Valeur Estimée Stock</span>
          <div style="font-size: 22px; font-weight: 900; color: #059669; margin-top: 6px;"><?= number_format($valeurEstimee, 0, ',', ' ') ?> <small style="font-size: 13px;">FCFA</small></div>
        </div>
        <div style="background: #ECFDF5; color: #10B981; padding: 10px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
          <i data-lucide="banknote" style="width: 22px; height: 22px;"></i>
        </div>
      </div>
      <div style="font-size: 12px; color: #64748B; margin-top: 8px; font-weight: 600;">
        Valorisation au prix tarif par défaut
      </div>
    </div>

    <!-- KPI 4 : ALERTES SEUIL CRITIQUE -->
    <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Alertes Stock Bas</span>
          <div style="font-size: 22px; font-weight: 900; color: <?= ($nbAlertes > 0) ? '#DC2626' : '#166534' ?>; margin-top: 6px;"><?= $nbAlertes ?> <small style="font-size: 13px;">ligne(s)</small></div>
        </div>
        <div style="background: <?= ($nbAlertes > 0) ? '#FEE2E2' : '#DCFCE7' ?>; color: <?= ($nbAlertes > 0) ? '#DC2626' : '#166534' ?>; padding: 10px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
          <i data-lucide="alert-triangle" style="width: 22px; height: 22px;"></i>
        </div>
      </div>
      <div style="font-size: 12px; color: #64748B; margin-top: 8px; font-weight: 600;">
        Lignes sous le seuil de 5 unités
      </div>
    </div>
  </div>

  <!-- TABLEAU DES STOCKS PAR PRODUIT & GRILLE -->
  <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #F1F5F9; flex-wrap: wrap; gap: 12px;">
      <h3 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="box" style="width: 20px; height: 20px; color: #2563EB;"></i> Inventaire des Produits &amp; Catégories de Poids
      </h3>
    </div>

    <div class="table-responsive">
      <table id="tableStock" class="table table-hover align-middle" style="width: 100%; font-size: 13px;">
        <thead style="background: #F8FAFC; color: #475569; font-weight: 800; border-bottom: 2px solid #E2E8F0;">
          <tr>
            <th style="padding: 12px 16px;">Produit / Intrant</th>
            <th style="padding: 12px 16px;">Catégorie de Poids</th>
            <th style="padding: 12px 16px; text-align: center;">Stock (Quantité)</th>
            <th style="padding: 12px 16px; text-align: center;">Poids Cumulé (Kg)</th>
            <th style="padding: 12px 16px; text-align: right;">Prix Unitaire Moyen</th>
            <th style="padding: 12px 16px; text-align: center;">Statut Niveau</th>
            <th style="padding: 12px 16px; text-align: center;">Dernier Mouvement</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($stocks)): ?>
            <?php foreach ($stocks as $s): ?>
              <?php 
                $qte = (int)$s['stock_pieces'];
                $poids = (float)$s['stock_poids_kg'];
                $prix = (float)($s['prix_vente_defaut'] ?? 0);
                $dateMvt = !empty($s['dernier_mouvement']) ? date('d/m/Y H:i', strtotime($s['dernier_mouvement'])) : 'Aucun';
              ?>
              <tr>
                <td style="padding: 14px 16px; font-weight: 800; color: #0F172A;">
                  <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="background: #EFF6FF; color: #2563EB; padding: 6px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                      <i data-lucide="package" style="width: 16px; height: 16px;"></i>
                    </div>
                    <span><?= htmlspecialchars($s['libelle_produit'] ?? 'Produit') ?></span>
                  </div>
                </td>

                <td style="padding: 14px 16px;">
                  <?php if (!empty($s['libelle_categorie_poids'])): ?>
                    <span style="display: inline-flex; align-items: center; gap: 6px; background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 6px;">
                      <i data-lucide="scale" style="width: 13px; height: 13px;"></i> <?= htmlspecialchars($s['libelle_categorie_poids']) ?>
                      <?php if ($s['poids_min'] !== null): ?>
                        (<?= number_format($s['poids_min'], 2, ',', ' ') ?> - <?= number_format($s['poids_max'], 2, ',', ' ') ?> kg)
                      <?php endif; ?>
                    </span>
                  <?php else: ?>
                    <span style="color: #94A3B8; font-style: italic;">Standard (Non soumis)</span>
                  <?php endif; ?>
                </td>

                <td style="padding: 14px 16px; text-align: center;">
                  <span style="background: #F1F5F9; color: #0F172A; border: 1px solid #CBD5E1; padding: 6px 14px; border-radius: 8px; font-weight: 900; font-size: 13px; display: inline-block;">
                    <?= number_format($qte, 0, ',', ' ') ?> <?= htmlspecialchars($s['unite_mesure'] ?? 'unités') ?>
                  </span>
                </td>

                <td style="padding: 14px 16px; text-align: center; font-weight: 800; color: #0369A1;">
                  <?= number_format($poids, 2, ',', ' ') ?> kg
                </td>

                <td style="padding: 14px 16px; text-align: right; font-weight: 700; color: #475569;">
                  <?= number_format($prix, 0, ',', ' ') ?> FCFA
                </td>

                <td style="padding: 14px 16px; text-align: center;">
                  <?php if ($qte > 10): ?>
                    <span style="background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;">
                      <i data-lucide="check-circle" style="width: 12px; height: 12px;"></i> Stock Conforme
                    </span>
                  <?php elseif ($qte > 0): ?>
                    <span style="background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;">
                      <i data-lucide="alert-triangle" style="width: 12px; height: 12px;"></i> Seuil Critique
                    </span>
                  <?php else: ?>
                    <span style="background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;">
                      <i data-lucide="x-circle" style="width: 12px; height: 12px;"></i> Rupture de Stock
                    </span>
                  <?php endif; ?>
                </td>

                <td style="padding: 14px 16px; text-align: center; color: #64748B; font-size: 12px; font-weight: 600;">
                  <?= $dateMvt ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL AJUSTEMENT / INVENTAIRE DE STOCK -->
<div class="modal fade" id="modalAjustementStock" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: #2563EB; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="sliders" style="width: 20px; height: 20px; color: #93C5FD;"></i> Ajustement / Inventaire Manuel
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formAjustementStock">
        <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
        <div class="modal-body" style="padding: 20px;">
          
          <div class="mb-3">
            <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Produit Avicoles *</label>
            <select name="produit_code" class="form-select" required style="border-radius: 8px; font-size: 13px;">
              <option value="">-- Sélectionner le produit --</option>
              <?php foreach ($produits as $p): ?>
                <option value="<?= $p['code_produit_aviculture'] ?>"><?= htmlspecialchars($p['libelle_produit']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Catégorie de Poids (Facultatif)</label>
            <select name="categorie_poids_code" class="form-select" style="border-radius: 8px; font-size: 13px;">
              <option value="">-- Non spécifiée / Standard --</option>
              <?php foreach ($categoriesPoids as $cp): ?>
                <option value="<?= $cp['code_categorie_poids'] ?>"><?= htmlspecialchars($cp['libelle_categorie_poids']) ?> (<?= $cp['poids_min'] ?>-<?= $cp['poids_max'] ?> kg)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Sens de l'Ajustement *</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
              <label style="border: 2px solid #CBD5E1; border-radius: 8px; padding: 10px; text-align: center; cursor: pointer; background: #F8FAFC;">
                <input type="radio" name="sens" value="ENTREE" checked style="margin-right: 6px;">
                <span style="font-weight: 800; color: #059669;">+ Entrée (Ajout)</span>
              </label>
              <label style="border: 2px solid #CBD5E1; border-radius: 8px; padding: 10px; text-align: center; cursor: pointer; background: #F8FAFC;">
                <input type="radio" name="sens" value="SORTIE" style="margin-right: 6px;">
                <span style="font-weight: 800; color: #DC2626;">- Sortie (Perte/Casse)</span>
              </label>
            </div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Quantité (Pièces) *</label>
              <input type="number" name="quantite_pieces" min="1" required class="form-control" placeholder="ex: 10" style="border-radius: 8px; font-size: 13px;">
            </div>
            <div class="col-6">
              <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Poids Total (Kg)</label>
              <input type="number" step="0.01" name="poids_total_kg" min="0" class="form-control" placeholder="ex: 12.50" style="border-radius: 8px; font-size: 13px;">
            </div>
          </div>

          <div class="mb-3">
            <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Motif / Observation *</label>
            <textarea name="motif" class="form-control" rows="2" placeholder="ex: Correction suite à inventaire physique, avarie lors de la manutention..." style="border-radius: 8px; font-size: 13px;" required></textarea>
          </div>
        </div>

        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-weight: 600; border-radius: 8px;">Annuler</button>
          <button type="submit" class="btn btn-primary" style="background: #2563EB; border-color: #2563EB; font-weight: 800; border-radius: 8px; padding: 8px 20px; font-size: 13px;">
            Enregistrer l'Ajustement
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#tableStock').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            },
            pageLength: 25,
            order: [[0, 'asc']]
        });
    }

    function notifyMsg(message, type = 'success') {
        if (typeof toastr !== 'undefined' && toastr[type]) {
            toastr[type](message);
        } else {
            alert(message);
        }
    }

    $('#formAjustementStock').on('submit', function(e) {
        e.preventDefault();
        const baseApi = (typeof RACINE !== 'undefined') ? RACINE : '/ovolias/';
        const $btn = $(this).find('button[type="submit"]');

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Enregistrement...');

        $.ajax({
            url: baseApi + 'aviculture/ajusterStock',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 1 || res.status === 'success') {
                    notifyMsg(res.message || 'Ajustement enregistré avec succès !', 'success');
                    $('#modalAjustementStock').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    notifyMsg(res.message || 'Erreur lors de l\'ajustement', 'error');
                    $btn.prop('disabled', false).html('Enregistrer l\'Ajustement');
                }
            },
            error: function(xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Une erreur est survenue.';
                notifyMsg(msg, 'error');
                $btn.prop('disabled', false).html('Enregistrer l\'Ajustement');
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../public/inc/footer.php'; ?>
