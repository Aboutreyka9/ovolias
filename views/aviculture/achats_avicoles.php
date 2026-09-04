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
                <th style="padding: 12px;">N° Facture FRS</th>
                <th style="padding: 12px; text-align: center;">Statut Achat</th>
                <th style="padding: 12px; text-align: center;">Qté Totale</th>
                <th style="padding: 12px;">Montant Total</th>
                <th style="padding: 12px; text-align: center;">Statut Règlement</th>
                <th style="padding: 12px; text-align: center;">Date Achat</th>
                <th style="padding: 12px; text-align: center;">Actions</th>
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
  <div class="modal-dialog modal-lg modal-dialog-centered">
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

<!-- Modal Détails Bon d'Achat -->
<div class="modal fade" id="modalDetailsAchat" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: #1E3A5F; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="file-text" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Détails du Bon d'Achat
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="padding: 20px;">
        <div class="row g-3">
          <div class="col-md-4">
            <div style="background: #F8FAFC; padding: 12px 16px; border-radius: 8px; border: 1px solid #E2E8F0;">
              <small style="color: #64748B; font-weight: 700; text-transform: uppercase; font-size: 11px;">Code Achat</small>
              <div id="detCodeAchat" style="font-weight: 800; color: #1E3A5F; font-size: 15px; margin-top: 2px;">-</div>
            </div>
          </div>
          <div class="col-md-4">
            <div style="background: #F8FAFC; padding: 12px 16px; border-radius: 8px; border: 1px solid #E2E8F0;">
              <small style="color: #64748B; font-weight: 700; text-transform: uppercase; font-size: 11px;">Fournisseur</small>
              <div id="detFournisseur" style="font-weight: 800; color: #0F172A; font-size: 15px; margin-top: 2px;">-</div>
            </div>
          </div>
          <div class="col-md-4">
            <div style="background: #F8FAFC; padding: 12px 16px; border-radius: 8px; border: 1px solid #E2E8F0;">
              <small style="color: #64748B; font-weight: 700; text-transform: uppercase; font-size: 11px;">N° Facture FRS</small>
              <div id="detNumFacture" style="font-weight: 800; color: #0F172A; font-size: 15px; margin-top: 2px;">-</div>
            </div>
          </div>
          <div class="col-md-4">
            <div style="background: #F8FAFC; padding: 12px 16px; border-radius: 8px; border: 1px solid #E2E8F0;">
              <small style="color: #64748B; font-weight: 700; text-transform: uppercase; font-size: 11px;">Statut Achat</small>
              <div id="detStatutAchat" style="margin-top: 2px;">-</div>
            </div>
          </div>
          <div class="col-md-4">
            <div style="background: #F8FAFC; padding: 12px 16px; border-radius: 8px; border: 1px solid #E2E8F0;">
              <small style="color: #64748B; font-weight: 700; text-transform: uppercase; font-size: 11px;">Qté Totale</small>
              <div id="detQteTotale" style="font-weight: 800; color: #0F172A; font-size: 15px; margin-top: 2px;">-</div>
            </div>
          </div>
          <div class="col-md-4">
            <div style="background: #F8FAFC; padding: 12px 16px; border-radius: 8px; border: 1px solid #E2E8F0;">
              <small style="color: #64748B; font-weight: 700; text-transform: uppercase; font-size: 11px;">Date d'Achat</small>
              <div id="detDateAchat" style="font-weight: 700; color: #334155; font-size: 14px; margin-top: 2px;">-</div>
            </div>
          </div>
          <div class="col-md-4">
            <div style="background: #F8FAFC; padding: 12px 16px; border-radius: 8px; border: 1px solid #E2E8F0;">
              <small style="color: #64748B; font-weight: 700; text-transform: uppercase; font-size: 11px;">Statut Règlement</small>
              <div id="detStatut" style="margin-top: 2px;">-</div>
            </div>
          </div>
          <div class="col-md-4">
            <div style="background: #FEF2F2; padding: 12px 16px; border-radius: 8px; border: 1px solid #FCA5A5;">
              <small style="color: #991B1B; font-weight: 700; text-transform: uppercase; font-size: 11px;">Montant Total</small>
              <div id="detMontantTotal" style="font-weight: 900; color: #DC2626; font-size: 16px; margin-top: 2px;">-</div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600;">Fermer</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Facture Bon d'Achat -->
<div class="modal fade" id="modalFactureAchat" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: #0F172A; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="printer" style="width: 20px; height: 20px; color: #38BDF8;"></i> Facture d'Achat Fournisseur
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="printFactureArea" style="padding: 24px; background: #FFFFFF;">
        <!-- Header Receipt -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #E2E8F0; padding-bottom: 16px; margin-bottom: 20px;">
          <div>
            <h3 style="margin: 0; color: #1E3A5F; font-weight: 900; font-size: 20px;">GEICG - OVOLIAS AVICULTURE</h3>
            <p style="margin: 4px 0 0 0; color: #64748B; font-size: 13px;">Gestion Administrative & Approvisionnement Avicole</p>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 14px; font-weight: 800; color: #0F172A;" id="facCodeAchat">ACH-AV-000</div>
            <div style="font-size: 12px; color: #64748B;" id="facDateAchat">Date : -</div>
          </div>
        </div>

        <!-- Supplier & Invoice metadata -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; background: #F8FAFC; padding: 16px; border-radius: 8px; border: 1px solid #E2E8F0;">
          <div>
            <strong style="color: #475569; font-size: 12px; text-transform: uppercase;">INFORMATIONS FOURNISSEUR :</strong>
            <div style="font-weight: 800; color: #0F172A; font-size: 15px; margin-top: 4px;" id="facFournisseurNom">-</div>
            <div style="font-size: 13px; color: #64748B;" id="facFournisseurTel">Tél : -</div>
          </div>
          <div style="text-align: right;">
            <strong style="color: #475569; font-size: 12px; text-transform: uppercase;">RÉFÉRENCES FACTURE :</strong>
            <div style="font-weight: 800; color: #0F172A; font-size: 15px; margin-top: 4px;" id="facNumFacture">N° Facture : -</div>
            <div style="font-size: 13px; color: #64748B; margin-top: 2px;" id="facStatutReg">Statut : -</div>
          </div>
        </div>

        <!-- Line items table -->
        <table class="table table-bordered align-middle" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
          <thead>
            <tr style="background: #F1F5F9; color: #334155; font-size: 13px;">
              <th style="padding: 10px;">Désignation / Article</th>
              <th style="padding: 10px; text-align: center;">Quantité</th>
              <th style="padding: 10px; text-align: right;">Prix Unitaire</th>
              <th style="padding: 10px; text-align: right;">Montant Total</th>
            </tr>
          </thead>
          <tbody id="facTbodyArticles">
            <!-- Dynamic items -->
          </tbody>
          <tfoot>
            <tr style="background: #FEF2F2;">
              <th colspan="3" style="text-align: right; font-weight: 800; padding: 12px; color: #991B1B;">TOTAL GÉNÉRAL FACTURE :</th>
              <th style="text-align: right; font-weight: 900; font-size: 16px; padding: 12px; color: #DC2626;" id="facGrandTotal">0 FCFA</th>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600;">Fermer</button>
        <button type="button" class="btn btn-primary btn-print-facture" style="background: #0F172A; border-color: #0F172A; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
          <i data-lucide="printer" style="width: 16px; height: 16px;"></i> Imprimer la Facture
        </button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    const baseApi = (typeof RACINE !== 'undefined') ? RACINE : '/ovolias/';

    function getProduitBadge(row) {
        const libelle = row.produit_libelle || row.libelle_produit || row.categorie_intrant || '-';
        return `<span style="color: #334155; font-weight: 500;">${libelle}</span>`;
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
            { data: 'numero_facture_fournisseur', render: d => d || '-' },
            { 
                data: 'statut_achat', 
                className: 'text-center', 
                render: d => {
                    const st = (d || 'valide').toLowerCase();
                    if (st === 'valide' || st === 'recu' || st === 'confirme') {
                        return `<span style="background: #E0F2FE; color: #0369A1; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px;">Validé</span>`;
                    } else if (st === 'annule') {
                        return `<span style="background: #FEE2E2; color: #991B1B; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px;">Annulé</span>`;
                    } else {
                        return `<span style="background: #FEF3C7; color: #92400E; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px;">En attente</span>`;
                    }
                }
            },
            { data: 'quantite_totale', className: 'text-center', render: d => `<span style="font-weight:700; color:#0F172A; font-size:13px;">${parseFloat(d||0).toLocaleString('fr-FR')}</span>` },
            { data: 'montant_total', render: d => `<strong style="color:#DC2626; font-size:14px;">${parseFloat(d||0).toLocaleString('fr-FR')} FCFA</strong>` },
            { data: 'statut_reglement', className: 'text-center', render: d => d === 'paye' ? `<span style="background: #DCFCE7; color: #166534; font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 12px;">Payé</span>` : `<span style="background: #FEF3C7; color: #92400E; font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 12px;">${d}</span>` },
            { data: 'date_achat', className: 'text-center', render: d => d ? new Date(d).toLocaleDateString('fr-FR') : '-' },
            {
                data: null,
                className: 'text-center',
                render: function(d, type, row) {
                    let detailUrl = baseApi + 'aviculture/detailAchat/' + (row.editId || row.code_achat_avicole);
                    return `<div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                        <a href="${detailUrl}" class="btn btn-sm btn-secondary" style="font-weight: 600; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;">
                            <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Détails
                        </a>
                        <button class="btn btn-sm btn-outline-primary btn-facture-achat" data-code="${row.code_achat_avicole}" style="font-weight: 600; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                            <i data-lucide="file-text" style="width: 14px; height: 14px;"></i> Facture
                        </button>
                    </div>`;
                }
            }
        ],
        language: { url: baseApi + 'json/datatables-i18n-fr-FR.json' },
        drawCallback: function() { if (window.lucide) lucide.createIcons(); }
    });

    $(document).on('click', '.btn-details-achat', function() {
        let raw = $(this).attr('data-achat');
        if (!raw) return;
        let data = JSON.parse(raw);
        $('#detCodeAchat').text(data.code_achat_avicole || '-');
        $('#detFournisseur').text(data.fournisseur_nom || data.fournisseur_avicole_code || '-');
        $('#detNumFacture').text(data.numero_facture_fournisseur || '-');
        $('#detQteTotale').text(parseFloat(data.quantite_totale || 0).toLocaleString('fr-FR'));
        $('#detDateAchat').text(data.date_achat ? new Date(data.date_achat).toLocaleDateString('fr-FR') : '-');
        $('#detMontantTotal').text(parseFloat(data.montant_total || 0).toLocaleString('fr-FR') + ' FCFA');

        let stAchat = (data.statut_achat || 'valide').toLowerCase();
        let stAchatHtml = (stAchat === 'valide' || stAchat === 'recu' || stAchat === 'confirme')
            ? `<span style="background: #E0F2FE; color: #0369A1; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px;">Validé</span>`
            : (stAchat === 'annule'
                ? `<span style="background: #FEE2E2; color: #991B1B; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px;">Annulé</span>`
                : `<span style="background: #FEF3C7; color: #92400E; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px;">En attente</span>`);
        $('#detStatutAchat').html(stAchatHtml);

        let statutHtml = data.statut_reglement === 'paye'
            ? `<span style="background: #DCFCE7; color: #166534; font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 12px;">Payé</span>`
            : `<span style="background: #FEF3C7; color: #92400E; font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 12px;">${data.statut_reglement || 'Impayé'}</span>`;
        $('#detStatut').html(statutHtml);

        let modalEl = document.getElementById('modalDetailsAchat');
        let bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();
        if (window.lucide) lucide.createIcons();
    });

    $(document).on('click', '.btn-facture-achat', function() {
        let code = $(this).attr('data-code');
        if (!code) return;

        $.get(baseApi + 'aviculture/apiDetailsAchat', { code: code }, function(res) {
            if (res.status === 'success' && res.achat) {
                let a = res.achat;
                let details = res.details || [];

                $('#facCodeAchat').text(a.code_achat_avicole || '-');
                $('#facDateAchat').text('Date : ' + (a.date_achat ? new Date(a.date_achat).toLocaleDateString('fr-FR') : '-'));
                $('#facFournisseurNom').text(a.fournisseur_nom || '-');
                $('#facFournisseurTel').text('Tél : ' + (a.telephone_fournisseur_avicole || 'N/A'));
                $('#facNumFacture').text('N° Facture FRS : ' + (a.numero_facture_fournisseur || '-'));
                
                let regHtml = a.statut_reglement === 'paye' 
                    ? '<strong style="color: #166534;">Payé</strong>' 
                    : '<strong style="color: #92400E;">' + (a.statut_reglement || 'Impayé') + '</strong>';
                $('#facStatutReg').html('Statut Règlement : ' + regHtml);

                let tbodyHtml = '';
                let grandTotal = 0;

                if (details.length > 0) {
                    details.forEach(function(item) {
                        let qte = parseFloat(item.quantite || 0);
                        let pu = parseFloat(item.prix_unitaire || 0);
                        let tot = parseFloat(item.montant_total || (qte * pu));
                        grandTotal += tot;

                        tbodyHtml += `
                        <tr>
                            <td style="padding: 8px 10px; font-weight: 600; color: #1E293B;">${item.libelle_article_intrant || 'Article'}</td>
                            <td style="padding: 8px 10px; text-align: center; font-weight: 700;">${qte.toLocaleString('fr-FR')} ${item.unite_mesure || ''}</td>
                            <td style="padding: 8px 10px; text-align: right;">${pu.toLocaleString('fr-FR')} FCFA</td>
                            <td style="padding: 8px 10px; text-align: right; font-weight: 700; color: #0F172A;">${tot.toLocaleString('fr-FR')} FCFA</td>
                        </tr>`;
                    });
                } else {
                    tbodyHtml = `<tr><td colspan="4" class="text-center text-muted" style="padding: 16px;">Aucune ligne de détail disponible</td></tr>`;
                    grandTotal = parseFloat(a.montant_total || 0);
                }

                $('#facTbodyArticles').html(tbodyHtml);
                $('#facGrandTotal').text(grandTotal.toLocaleString('fr-FR') + ' FCFA');

                let modalEl = document.getElementById('modalFactureAchat');
                let bsModal = new bootstrap.Modal(modalEl);
                bsModal.show();
                if (window.lucide) lucide.createIcons();
            } else {
                if (window.toastr) toastr.error("Impossible de charger la facture.");
            }
        }, 'json');
    });

    $(document).on('click', '.btn-print-facture', function() {
        let printContents = document.getElementById('printFactureArea').innerHTML;
        let originalContents = document.body.innerHTML;
        document.body.innerHTML = '<div style="padding: 40px;">' + printContents + '</div>';
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
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
        updateProduitSelectOptions();
    }

    function updateProduitSelectOptions() {
        let selectedCodes = [];
        $('.select-produit-row').each(function() {
            let val = $(this).val();
            if (val) {
                selectedCodes.push(val);
            }
        });

        $('.select-produit-row').each(function() {
            let currentVal = $(this).val();
            $(this).find('option').each(function() {
                let optVal = $(this).val();
                if (optVal && selectedCodes.includes(optVal) && optVal !== currentVal) {
                    $(this).prop('disabled', true).css('color', '#94A3B8');
                } else {
                    $(this).prop('disabled', false).css('color', '');
                }
            });
        });

        let totalAvailable = $('.select-produit-row').first().find('option').filter(function() {
            return $(this).val() !== '';
        }).length;

        let totalRows = $('.article-item-row').length;

        if (totalAvailable > 0 && (selectedCodes.length >= totalAvailable || totalRows >= totalAvailable)) {
            $('#btnAddArticleRow').prop('disabled', true).addClass('disabled').css({'opacity': '0.5', 'cursor': 'not-allowed'});
        } else {
            $('#btnAddArticleRow').prop('disabled', false).removeClass('disabled').css({'opacity': '', 'cursor': ''});
        }
    }

    // Initialiser avec au moins 1 ligne
    addArticleRow();

    $('#btnAddArticleRow').on('click', function(e) {
        let totalAvailable = $('.select-produit-row').first().find('option').filter(function() {
            return $(this).val() !== '';
        }).length;

        let selectedCodes = [];
        $('.select-produit-row').each(function() {
            if ($(this).val()) selectedCodes.push($(this).val());
        });

        let totalRows = $('.article-item-row').length;

        if (totalAvailable > 0 && (selectedCodes.length >= totalAvailable || totalRows >= totalAvailable)) {
            e.preventDefault();
            if (window.toastr) toastr.warning("Tous les produits disponibles ont déjà été sélectionnés.");
            return false;
        }

        addArticleRow();
    });

    $(document).on('click', '.btn-remove-row', function() {
        if ($('#tbodyArticlesModal tr').length > 1) {
            $(this).closest('tr').remove();
            recalcTotals();
            updateProduitSelectOptions();
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
        updateProduitSelectOptions();
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
