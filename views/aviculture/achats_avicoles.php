<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$fournisseurs = $fournisseurs ?? []; 
$produits = $produits ?? [];
$categoriesPoids = $categoriesPoids ?? [];
$produitsAvecGrille = array_filter($produits, fn($p) => isset($p['soumis_grille_poids']) && intval($p['soumis_grille_poids']) === 1);
$produitsSansGrille = array_filter($produits, fn($p) => !isset($p['soumis_grille_poids']) || intval($p['soumis_grille_poids']) === 0);
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
              <select name="statut_reglement" class="form-select" style="border-radius: 8px; height: 42px;" required>
                <option value="">-- Sélectionner --</option>
                <option value="paye">Payé (Caisse/Banque)</option>
                <option value="partiel">Partiel</option>
                <option value="impaye">À Crédit (Dette)</option>
              </select>
            </div>
          </div>

          <div style="border-top: 1px solid #E2E8F0; padding-top: 16px; margin-top: 16px;">
            <label style="font-weight: 800; font-size: 15px; color: #0F172A; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
              <i data-lucide="shopping-bag" style="width: 18px; height: 18px; color: #2563EB;"></i> Articles & Produits Commandés
            </label>

            <!-- SECTION 1 : PRODUITS AVEC GRILLE DE POIDS -->
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 16px; margin-bottom: 16px;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                  <span style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; padding: 4px 10px; border-radius: 6px; font-weight: 800; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                    <i data-lucide="scale" style="width: 14px; height: 14px;"></i> Section 1
                  </span>
                  <span style="font-weight: 800; font-size: 13px; color: #0F172A;">Produits Soumis à la Grille de Poids</span>
                </div>
                <button type="button" id="btnAddArticleRowAvecGrille" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-weight: 700; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                  <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Ajouter (Avec Grille)
                </button>
              </div>

              <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0" style="border-color: #E2E8F0; font-size: 13px;">
                  <thead style="background: #E0F2FE; color: #0369A1; font-size: 11px; text-transform: uppercase; font-weight: 800;">
                    <tr>
                      <th style="min-width: 180px;">Produit (Avec Grille) *</th>
                      <th style="min-width: 160px;">Grille / Catégorie de Poids</th>
                      <th style="width: 90px;">Quantité *</th>
                      <th style="width: 80px;">Unité</th>
                      <th style="width: 120px;">Prix Unit. (FCFA) *</th>
                      <th style="width: 120px; text-align: right;">Sous-Total</th>
                      <th style="width: 45px; text-align: center;"></th>
                    </tr>
                  </thead>
                  <tbody id="tbodyArticlesAvecGrille">
                    <!-- Injecté dynamiquement par JS -->
                  </tbody>
                </table>
              </div>
            </div>

            <!-- SECTION 2 : PRODUITS SANS GRILLE DE POIDS -->
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 16px; margin-bottom: 16px;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                  <span style="background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; padding: 4px 10px; border-radius: 6px; font-weight: 800; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                    <i data-lucide="package" style="width: 14px; height: 14px;"></i> Section 2
                  </span>
                  <span style="font-weight: 800; font-size: 13px; color: #0F172A;">Produits & Intrants Sans Grille (Tarif Fixe)</span>
                </div>
                <button type="button" id="btnAddArticleRowSansGrille" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px; font-weight: 700; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                  <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Ajouter (Sans Grille)
                </button>
              </div>

              <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0" style="border-color: #E2E8F0; font-size: 13px;">
                  <thead style="background: #F1F5F9; color: #475569; font-size: 11px; text-transform: uppercase; font-weight: 800;">
                    <tr>
                      <th style="min-width: 200px;">Produit / Intrant (Sans Grille) *</th>
                      <th style="width: 100px;">Quantité *</th>
                      <th style="width: 90px;">Unité</th>
                      <th style="width: 130px;">Prix Unit. (FCFA) *</th>
                      <th style="width: 130px; text-align: right;">Sous-Total</th>
                      <th style="width: 45px; text-align: center;"></th>
                    </tr>
                  </thead>
                  <tbody id="tbodyArticlesSansGrille">
                    <!-- Injecté dynamiquement par JS -->
                  </tbody>
                </table>
              </div>
            </div>

            <!-- RECAPITULATIF TOTAL GLOBAL -->
            <div style="background: linear-gradient(135deg, #1E3A5F 0%, #0F172A 100%); color: #FFFFFF; border-radius: 10px; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; margin-top: 16px; border: 1px solid #1E293B; box-shadow: 0 4px 12px rgba(15,23,42,0.08);">
              <div style="display: flex; align-items: center; gap: 10px;">
                <div style="background: rgba(52, 211, 153, 0.15); color: #34D399; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                  <i data-lucide="calculator" style="width: 20px; height: 20px;"></i>
                </div>
                <div>
                  <span style="font-weight: 800; font-size: 14px; color: #F8FAFC; display: block;">Montant Total Global de la Commande</span>
                  <span style="font-size: 11px; color: #94A3B8;">Cumul calculé en temps réel des deux sections</span>
                </div>
              </div>
              <div style="font-size: 24px; font-weight: 900; color: #34D399; letter-spacing: -0.5px;" id="valTotalAchat">0 FCFA</div>
            </div>
          </div>

        </div>

        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
          <button type="button" class="btn btn-secondary" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; border-radius: 8px; font-weight: 700; padding: 8px 18px;">Valider la Commande</button>
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

    function addArticleRowAvecGrille(qte = '', pu = '', unite = '') {
        rowIndex++;
        let rowId = 'art_row_' + rowIndex;
        let html = `
        <tr id="${rowId}" class="article-item-row">
            <td>
                <select name="articles[${rowIndex}][produit_code]" class="form-select select-produit-row" style="border-radius: 8px; height: 38px;" required>
                    <option value="">-- Choisir un produit (avec grille) --</option>
                    <?php if (!empty($produitsAvecGrille)): ?>
                        <?php foreach ($produitsAvecGrille as $p): ?>
                            <option value="<?= htmlspecialchars($p['code_produit_aviculture']) ?>" data-unite="<?= htmlspecialchars($p['unite_mesure'] ?? 'kg') ?>">
                                <?= htmlspecialchars($p['libelle_produit']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
            <td>
                <select name="articles[${rowIndex}][categorie_poids_code]" class="form-select select-grille-row" style="border-radius: 8px; height: 38px;">
                    <option value="">-- Choisir grille --</option>
                    <?php if (!empty($categoriesPoids)): ?>
                        <?php foreach ($categoriesPoids as $c): ?>
                            <option value="<?= htmlspecialchars($c['code_categorie_poids']) ?>">
                                <?= htmlspecialchars($c['libelle_categorie_poids']) ?> (<?= number_format($c['poids_min'], 2, ',', ' ') ?> - <?= number_format($c['poids_max'], 2, ',', ' ') ?> kg)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
            <td>
                <input type="number" step="any" min="0.001" name="articles[${rowIndex}][quantite]" class="form-control input-qte-row" style="border-radius: 8px; height: 38px;" placeholder="Qte" value="${qte}" required>
            </td>
            <td>
                <input type="text" name="articles[${rowIndex}][unite_mesure]" class="form-control input-unite-row" style="border-radius: 8px; height: 38px;" placeholder="Ex: kg" value="${unite}">
            </td>
            <td>
                <input type="number" step="any" min="0" name="articles[${rowIndex}][prix_unitaire]" class="form-control input-pu-row" style="border-radius: 8px; height: 38px;" placeholder="FCFA" value="${pu}" required>
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

        $('#tbodyArticlesAvecGrille').append(html);
        if (window.lucide) lucide.createIcons();
        recalcTotals();
        updateProduitSelectOptions();
    }

    function addArticleRowSansGrille(qte = '', pu = '', unite = '') {
        rowIndex++;
        let rowId = 'art_row_' + rowIndex;
        let html = `
        <tr id="${rowId}" class="article-item-row">
            <td>
                <select name="articles[${rowIndex}][produit_code]" class="form-select select-produit-row" style="border-radius: 8px; height: 38px;" required>
                    <option value="">-- Choisir un produit (sans grille) --</option>
                    <?php if (!empty($produitsSansGrille)): ?>
                        <?php foreach ($produitsSansGrille as $p): ?>
                            <option value="<?= htmlspecialchars($p['code_produit_aviculture']) ?>" data-unite="<?= htmlspecialchars($p['unite_mesure'] ?? 'Pièces') ?>">
                                <?= htmlspecialchars($p['libelle_produit']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
            <td>
                <input type="number" step="any" min="0.001" name="articles[${rowIndex}][quantite]" class="form-control input-qte-row" style="border-radius: 8px; height: 38px;" placeholder="Qte" value="${qte}" required>
            </td>
            <td>
                <input type="text" name="articles[${rowIndex}][unite_mesure]" class="form-control input-unite-row" style="border-radius: 8px; height: 38px;" placeholder="Ex: sac, unité" value="${unite}">
            </td>
            <td>
                <input type="number" step="any" min="0" name="articles[${rowIndex}][prix_unitaire]" class="form-control input-pu-row" style="border-radius: 8px; height: 38px;" placeholder="FCFA" value="${pu}" required>
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

        $('#tbodyArticlesSansGrille').append(html);
        if (window.lucide) lucide.createIcons();
        recalcTotals();
        updateProduitSelectOptions();
    }

    function updateProduitSelectOptions() {
        // 1. Collecter les combinaisons (produit + grille) sélectionnées dans la Section 1
        let selectedPairsSection1 = [];
        $('#tbodyArticlesAvecGrille tr').each(function() {
            let p = $(this).find('.select-produit-row').val();
            let g = $(this).find('.select-grille-row').val();
            if (p && g) {
                selectedPairsSection1.push(p + '___' + g);
            }
        });

        // Désactiver les options en double dans la Section 1 (Produits avec grille)
        $('#tbodyArticlesAvecGrille tr').each(function() {
            let $tr = $(this);
            let curP = $tr.find('.select-produit-row').val();
            let curG = $tr.find('.select-grille-row').val();

            // Mettre à jour les grilles disponibles selon le produit sélectionné
            $tr.find('.select-grille-row option').each(function() {
                let optG = $(this).val();
                if (optG) {
                    if (curP && optG !== curG && selectedPairsSection1.includes(curP + '___' + optG)) {
                        $(this).prop('disabled', true).css('color', '#94A3B8');
                    } else {
                        $(this).prop('disabled', false).css('color', '');
                    }
                }
            });

            // Mettre à jour les produits disponibles selon la grille sélectionnée
            $tr.find('.select-produit-row option').each(function() {
                let optP = $(this).val();
                if (optP) {
                    if (curG && optP !== curP && selectedPairsSection1.includes(optP + '___' + curG)) {
                        $(this).prop('disabled', true).css('color', '#94A3B8');
                    } else {
                        $(this).prop('disabled', false).css('color', '');
                    }
                }
            });
        });

        // 2. Section 2 : Désactiver les produits sans grille déjà sélectionnés
        let selectedProdsSection2 = [];
        $('#tbodyArticlesSansGrille tr').each(function() {
            let p = $(this).find('.select-produit-row').val();
            if (p) selectedProdsSection2.push(p);
        });

        $('#tbodyArticlesSansGrille tr').each(function() {
            let curP = $(this).find('.select-produit-row').val();
            $(this).find('.select-produit-row option').each(function() {
                let optP = $(this).val();
                if (optP && selectedProdsSection2.includes(optP) && optP !== curP) {
                    $(this).prop('disabled', true).css('color', '#94A3B8');
                } else {
                    $(this).prop('disabled', false).css('color', '');
                }
            });
        });
    }

    // Initialiser avec Section 1 par défaut
    addArticleRowAvecGrille();

    $('#btnAddArticleRowAvecGrille').on('click', function(e) {
        addArticleRowAvecGrille();
    });

    $('#btnAddArticleRowSansGrille').on('click', function(e) {
        addArticleRowSansGrille();
    });

    function notifyMsg(msg, type = 'success') {
        if (window.toastr && typeof window.toastr[type] === 'function') {
            window.toastr[type](msg);
        } else if (typeof showToast === 'function') {
            showToast(msg, type);
        } else {
            alert(msg);
        }
    }

    function hideModalAchat() {
        let modalEl = document.getElementById('modalAchat');
        if (modalEl) {
            if (window.bootstrap) {
                let bsModal = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
                if (bsModal) {
                    try { bsModal.hide(); } catch(e) {}
                }
            }
            try { $('#modalAchat').modal('hide'); } catch(e) {}
            $('#modalAchat').removeClass('show').css('display', 'none');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('overflow', '');
        }
    }

    $(document).on('click', '.btn-remove-row', function() {
        if ($('.article-item-row').length > 1) {
            $(this).closest('tr').remove();
            recalcTotals();
            updateProduitSelectOptions();
        } else {
            notifyMsg("La commande doit comporter au moins un produit.", 'warning');
        }
    });

    $(document).on('change', '.select-produit-row, .select-grille-row', function() {
        let $tr = $(this).closest('tr');
        
        // Empêcher les doublons exacts Produit + Grille dans la Section 1
        if ($tr.closest('#tbodyArticlesAvecGrille').length > 0) {
            let p = $tr.find('.select-produit-row').val();
            let g = $tr.find('.select-grille-row').val();

            if (p && g) {
                let duplicateCount = 0;
                $('#tbodyArticlesAvecGrille tr').each(function() {
                    let otherP = $(this).find('.select-produit-row').val();
                    let otherG = $(this).find('.select-grille-row').val();
                    if (otherP === p && otherG === g) {
                        duplicateCount++;
                    }
                });

                if (duplicateCount > 1) {
                    notifyMsg("Cette combinaison Produit + Grille de Poids est déjà présente dans la commande.", 'warning');
                    $(this).val('');
                }
            }
        }

        let opt = $tr.find('.select-produit-row :selected');
        let u = opt.data('unite') || 'Pièces';
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
        $('.article-item-row').each(function() {
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
        let $btnSubmit = $(this).find('button[type="submit"]');
        if (typeof loading === 'function') loading($btnSubmit, true, 'Enregistrement...');

        $.post(baseApi + 'aviculture/addAchat', $(this).serialize(), function(res) {
            if (typeof loading === 'function') loading($btnSubmit, false);

            let isSuccess = (res.status === 1 || res.status === '1' || res.status === 'success' || res.success === true);

            if (isSuccess) {
                notifyMsg(res.message || 'Achat enregistré avec succès !', 'success');
                
                hideModalAchat();

                // Réinitialiser le formulaire
                $('#formAchat')[0].reset();
                $('#tbodyArticlesAvecGrille').empty();
                $('#tbodyArticlesSansGrille').empty();
                rowIndex = 0;
                addArticleRowAvecGrille();
                chargerNumeroFactureAuto();

                if (typeof dt !== 'undefined' && dt) {
                    dt.ajax.reload(null, false);
                }
            } else {
                notifyMsg(res.message || 'Erreur lors de l\'enregistrement de l\'achat', 'error');
            }
        }, 'json').fail(function(xhr) {
            if (typeof loading === 'function') loading($btnSubmit, false);
            let errMsg = 'Erreur réseau ou serveur lors de la soumission du formulaire.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errMsg = xhr.responseJSON.message;
            }
            notifyMsg(errMsg, 'error');
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
