<?php 
require_once __DIR__ . '/../../public/inc/header.php'; 
$clients = $clients ?? [];
$produits = $produits ?? [];
$categoriesPoids = $categoriesPoids ?? [];
$grillesTarifs = $grillesTarifs ?? [];
$etiquettes = $etiquettes ?? [];
$kpis = $kpis ?? ['total_ventes' => 0, 'ca_jour' => 0, 'ca_comptoir' => 0, 'cmd_a_livrer' => 0];
?>

<style>
@media print {
  .sidebar, .navbar, .header, #sidebarToggle, .btn, .nav-pills,
  .dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate, .modal {
    display: none !important;
  }
  .content-wrapper { padding: 0 !important; background: #FFFFFF !important; }
  .card { border: none !important; box-shadow: none !important; padding: 0 !important; }
  .table { width: 100% !important; border-collapse: collapse !important; }
  .table th, .table td { border: 1px solid #94A3B8 !important; padding: 6px 10px !important; font-size: 11px !important; }
}
.type-sale-btn {
  border: 2px solid #CBD5E1;
  background: #F8FAFC;
  color: #475569;
  font-weight: 700;
  border-radius: 8px;
  padding: 10px 16px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.type-sale-btn.active {
  border-color: #2563EB;
  background: #EFF6FF;
  color: #1E40AF;
}
</style>

<div class="content-wrapper" style="padding: 24px; background: #F8FAFC; min-height: 100vh;">
  
  <!-- EN-TÊTE PAGE ET ACTIONS -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
      <h1 style="font-size: 24px; font-weight: 900; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 10px;">
        <i data-lucide="shopping-bag" style="width: 28px; height: 28px; color: #059669;"></i> Ventes Avicoles &amp; Caisse POS
      </h1>
      <p style="font-size: 13px; color: #64748B; margin: 4px 0 0 0;">
        Gestion des tickets de caisse comptoir, factures clients pro et encaissements journaliers
      </p>
    </div>

    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
      <button type="button" onclick="imprimerJournalVentes()" class="btn btn-dark" style="font-weight: 800; border-radius: 8px; font-size: 13px; background: #0F172A; border-color: #0F172A; color: #FFFFFF; display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; box-shadow: 0 2px 6px rgba(15,23,42,0.2);">
        <i data-lucide="printer" style="width: 16px; height: 16px; color: #FFFFFF;"></i> Imprimer le Journal
      </button>
      <button class="btn btn-success" style="background: #059669; border-color: #059669; display: inline-flex; align-items: center; gap: 8px; font-weight: 800; border-radius: 8px; padding: 10px 18px; box-shadow: 0 4px 12px rgba(5,150,105,0.25);" data-bs-toggle="modal" data-bs-target="#modalVente">
        <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Nouvelle Vente / Ticket Caisse
      </button>
    </div>
  </div>

  <!-- CARTES KPIS EXÉCUTIVES DU JOUR -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Chiffre d'Affaires du Jour</span>
          <div style="font-size: 22px; font-weight: 900; color: #059669; margin-top: 6px;"><?= number_format($kpis['ca_jour'], 0, ',', ' ') ?> FCFA</div>
        </div>
        <div style="background: #ECFDF5; color: #059669; padding: 10px; border-radius: 10px;">
          <i data-lucide="dollar-sign" style="width: 22px; height: 22px;"></i>
        </div>
      </div>
      <div style="font-size: 12px; color: #64748B; margin-top: 8px; font-weight: 600;"><?= number_format($kpis['total_ventes'], 0, ',', ' ') ?> transaction(s) aujourd'hui</div>
    </div>

    <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Recettes Caisse Comptoir</span>
          <div style="font-size: 22px; font-weight: 900; color: #2563EB; margin-top: 6px;"><?= number_format($kpis['ca_comptoir'], 0, ',', ' ') ?> FCFA</div>
        </div>
        <div style="background: #EFF6FF; color: #2563EB; padding: 10px; border-radius: 10px;">
          <i data-lucide="shopping-cart" style="width: 22px; height: 22px;"></i>
        </div>
      </div>
      <div style="font-size: 12px; color: #64748B; margin-top: 8px; font-weight: 600;">Ventes directes sans livraison</div>
    </div>

    <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Commandes à Livrer</span>
          <div style="font-size: 22px; font-weight: 900; color: #D97706; margin-top: 6px;"><?= $kpis['cmd_a_livrer'] ?></div>
        </div>
        <div style="background: #FEF3C7; color: #D97706; padding: 10px; border-radius: 10px;">
          <i data-lucide="truck" style="width: 22px; height: 22px;"></i>
        </div>
      </div>
      <div style="font-size: 12px; color: #64748B; margin-top: 8px; font-weight: 600;">Circuit expéditions &amp; chauffeurs</div>
    </div>
  </div>

  <!-- TABLEAU DES VENTES & HISTORIQUE -->
  <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <div class="table-responsive">
      <table id="tableVentesAvicoles" class="table table-hover align-middle" style="width: 100%; font-size: 13px;">
        <thead style="background: #F8FAFC; color: #475569; font-weight: 800;">
          <tr>
            <th>Code Vente</th>
            <th>Type</th>
            <th>Client Avicole</th>
            <th>Règlement</th>
            <th>Montant Net</th>
            <th>Rendu Monnaie</th>
            <th>Date &amp; Heure</th>
            <th>Caissier</th>
            <th style="text-align: center;">Impressions / Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

</div>

<!-- MODAL CAISSE POS & NOUVELLE VENTE -->
<div class="modal fade" id="modalVente" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content" style="border-radius: 14px; border: none; box-shadow: 0 12px 30px rgba(0,0,0,0.25);">
      
      <!-- Modal Header -->
      <div class="modal-header" style="background: #0F172A; color: white; border-top-left-radius: 14px; border-top-right-radius: 14px; padding: 16px 24px;">
        <h5 class="modal-title" style="font-weight: 900; font-size: 17px; margin: 0; display: flex; align-items: center; gap: 10px;">
          <i data-lucide="shopping-cart" style="width: 22px; height: 22px; color: #10B981;"></i> Terminal de Caisse POS - Vente Avicole OVOLIA
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form id="formVente">
        <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
        <input type="hidden" name="type_vente" id="input_type_vente" value="comptoir_direct">

        <div class="modal-body" style="padding: 24px; background: #F8FAFC;">
          
          <!-- SÉLECTEUR TYPE DE VENTE -->
          <div style="display: flex; gap: 12px; margin-bottom: 20px;">
            <button type="button" class="type-sale-btn active" id="btnTypeComptoir" onclick="selectTypeVente('comptoir_direct')">
              <i data-lucide="store" style="width: 18px; height: 18px;"></i> 1. Vente Comptoir Directe (Ticket Caisse)
            </button>
            <button type="button" class="type-sale-btn" id="btnTypeCommande" onclick="selectTypeVente('commande_livraison')">
              <i data-lucide="truck" style="width: 18px; height: 18px;"></i> 2. Commande Pro / Particulier (Livraison)
            </button>
          </div>

          <div class="row g-3 mb-4" style="background: #FFFFFF; padding: 16px; border-radius: 10px; border: 1px solid #E2E8F0;">
            <!-- Client -->
            <div class="col-md-6">
              <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Client Avicole *</label>
              <select name="client_avicole_code" id="selectClient" class="form-select" style="border-radius: 8px; font-size: 13px; height: 42px;">
                <option value="">-- Client Comptoir Direct / Anonyme --</option>
                <?php foreach ($clients as $c): ?>
                  <option value="<?= htmlspecialchars($c['code_client_avicole']) ?>">
                    <?= htmlspecialchars($c['nom_client_avicole']) ?> (<?= strtoupper(htmlspecialchars($c['type_client_avicole'])) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Mode Règlement -->
            <div class="col-md-6">
              <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Mode de Règlement *</label>
              <select name="type_reglement" id="selectReglement" class="form-select" onchange="checkReglement()" style="border-radius: 8px; font-size: 13px; height: 42px;">
                <option value="comptant_especes">Espèces / Comptoir</option>
                <option value="mobile_money">Mobile Money (Wave / Orange)</option>
                <option value="cheque">Chèque Bancaire</option>
                <option value="virement">Virement Bancaire</option>
                <option value="credit">Crédit Client (Compte Courant)</option>
              </select>
            </div>
          </div>

          <!-- AJOUT D'ARTICLES AU PANIER -->
          <div style="background: #FFFFFF; padding: 16px; border-radius: 10px; border: 1px solid #E2E8F0; margin-bottom: 20px;">
            <div style="font-weight: 800; font-size: 14px; color: #0F172A; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
              <i data-lucide="plus-circle" style="width: 18px; height: 18px; color: #2563EB;"></i> Saisie des Produits &amp; Articles
            </div>

            <div class="row g-2 align-items-end">
              <div class="col-md-3">
                <label style="font-size: 11px; font-weight: 700; color: #475569;">Produit Avicole</label>
                <select id="pos_produit" class="form-select form-select-sm" style="border-radius: 6px;">
                  <option value="">-- Sélectionner produit --</option>
                  <?php foreach ($produits as $p): ?>
                    <option value="<?= htmlspecialchars($p['code_produit_aviculture']) ?>" data-nom="<?= htmlspecialchars($p['libelle_produit']) ?>" data-prix="<?= $p['prix_vente_standard'] ?>">
                      <?= htmlspecialchars($p['libelle_produit']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-3">
                <label style="font-size: 11px; font-weight: 700; color: #475569;">Catégorie de Poids</label>
                <select id="pos_cat" class="form-select form-select-sm" style="border-radius: 6px;">
                  <option value="">-- Standard / Aucune --</option>
                  <?php foreach ($categoriesPoids as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['code_categorie_poids']) ?>" data-nom="<?= htmlspecialchars($cat['libelle_categorie_poids']) ?>" data-prix="<?= $cat['prix_kilo_moyen'] ?>">
                      <?= htmlspecialchars($cat['libelle_categorie_poids']) ?> (<?= $cat['poids_min'] ?> - <?= $cat['poids_max'] ?> kg)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-2">
                <label style="font-size: 11px; font-weight: 700; color: #475569;">Quantité (Pcs/Sacs)</label>
                <input type="number" id="pos_qte" value="1" min="1" class="form-control form-control-sm" style="border-radius: 6px;">
              </div>

              <div class="col-md-2">
                <label style="font-size: 11px; font-weight: 700; color: #475569;">Prix Unitaire (F)</label>
                <input type="number" id="pos_prix" value="0" min="0" step="any" readonly class="form-control form-control-sm" style="border-radius: 6px; background-color: #F1F5F9; cursor: not-allowed; font-weight: 700;">
              </div>

              <div class="col-md-2">
                <button type="button" onclick="ajouterArticlePanier()" class="btn btn-primary btn-sm w-100" style="background: #2563EB; font-weight: 800; border-radius: 6px; padding: 6px 10px;">
                  + Ajouter Panier
                </button>
              </div>
            </div>

            <!-- TABLEAU PANIER ARTICLE -->
            <div class="table-responsive style-scroll" style="margin-top: 16px; max-height: 180px; overflow-y: auto;">
              <table class="table table-sm table-bordered align-middle" id="tablePanierPOS" style="font-size: 12px;">
                <thead style="background: #F1F5F9; color: #334155;">
                  <tr>
                    <th>Article / Libellé</th>
                    <th>Catégorie Poids</th>
                    <th style="text-align: center;">Qte</th>
                    <th style="text-align: right;">Prix Unitaire</th>
                    <th style="text-align: right;">Sous-Total</th>
                    <th style="text-align: center; width: 40px;">#</th>
                  </tr>
                </thead>
                <tbody id="tbodyPanier">
                  <tr id="emptyPanierRow">
                    <td colspan="6" style="text-align: center; color: #94A3B8; padding: 12px;">
                      Aucun article dans le panier. Sélectionnez un produit ci-dessus et cliquez sur <strong>+ Ajouter Panier</strong>.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- ACCORDEON OPTIONNEL : VOLAILLES PESÉES & ÉTIQUETÉES -->
            <?php if (!empty($etiquettes)): ?>
              <div style="margin-top: 12px;">
                <a class="btn btn-sm btn-link p-0 text-decoration-none" data-bs-toggle="collapse" href="#collapseEtiquettes" style="font-weight: 700; font-size: 12px; color: #0284C7;">
                  🏷️ Ou sélectionner des volailles pesées individuelles en stock (<?= count($etiquettes) ?> dispo)
                </a>
                <div class="collapse mt-2" id="collapseEtiquettes">
                  <div style="max-height: 140px; overflow-y: auto; border: 1px solid #E2E8F0; border-radius: 8px; padding: 10px; background: #F8FAFC;">
                    <div class="row g-2">
                      <?php foreach ($etiquettes as $etiq): ?>
                        <div class="col-md-6">
                          <div style="background: #FFFFFF; border: 1px solid #E2E8F0; padding: 6px 10px; border-radius: 6px; display: flex; align-items: center; justify-content: space-between;">
                            <div class="form-check d-flex align-items-center gap-2" style="margin: 0;">
                              <input class="form-check-input chk-etiq" type="checkbox" name="etiquettes[]" value="<?= htmlspecialchars($etiq['code_etiquette']) ?>" data-prix="<?= $etiq['prix_unitaire_applique'] ?>" id="chk_<?= $etiq['id_pesee'] ?>" onchange="recalculerTotaux()">
                              <label class="form-check-label" style="font-weight: 700; font-size: 12px; color: #0F172A; cursor: pointer;" for="chk_<?= $etiq['id_pesee'] ?>">
                                <?= htmlspecialchars($etiq['libelle_produit']) ?> (<?= htmlspecialchars($etiq['libelle_categorie_poids']) ?>)
                                <span style="font-size: 10px; color: #64748B; font-family: monospace; display: block;"><?= htmlspecialchars($etiq['code_etiquette']) ?> - Poids: <?= number_format($etiq['poids_net_reel'], 2, ',', ' ') ?> kg</span>
                              </label>
                            </div>
                            <span style="background: #DCFCE7; color: #166534; font-size: 11px; font-weight: 800; padding: 2px 6px; border-radius: 4px;"><?= number_format($etiq['prix_unitaire_applique'], 0, ',', ' ') ?> F</span>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>

          </div>

          <!-- ENCAISSEMENT & FINANCIER REAL-TIME -->
          <div style="background: #0F172A; color: #FFFFFF; padding: 20px; border-radius: 12px;">
            <div class="row g-3 align-items-center">
              <div class="col-md-3">
                <label style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Sous-Total HT</label>
                <div style="font-size: 18px; font-weight: 800;" id="displaySousTotal">0 FCFA</div>
              </div>

              <div class="col-md-3">
                <label style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Remise Accordée (F)</label>
                <input type="number" name="montant_remise" id="inputRemise" value="0" min="0" readonly oninput="recalculerTotaux()" class="form-control form-control-sm" style="border-radius: 6px; font-size: 14px; font-weight: 700; background: #1E293B; color: #94A3B8; border-color: #334155; cursor: not-allowed;">
              </div>

              <div class="col-md-3" style="border-left: 1px solid #334155;">
                <label style="font-size: 11px; font-weight: 800; color: #34D399; text-transform: uppercase;">NET À PAYER</label>
                <div style="font-size: 24px; font-weight: 900; color: #10B981;" id="displayNetPay">0 FCFA</div>
              </div>

              <div class="col-md-3" id="blockCalculMonnaie" style="border-left: 1px solid #334155;">
                <label style="font-size: 11px; font-weight: 800; color: #60A5FA; text-transform: uppercase;">Montant Reçu (Client)</label>
                <input type="number" name="montant_recu" id="inputMontantRecu" value="0" min="0" oninput="recalculerTotaux()" class="form-control form-control-sm" placeholder="ex: 20000" style="border-radius: 6px; font-size: 14px; font-weight: 800; background: #1E293B; color: #FFF; border-color: #475569;">
                
                <div style="font-size: 12px; margin-top: 6px; font-weight: 700;">
                  Monnaie à Rendre : <span id="displayMonnaieRendue" style="font-size: 16px; font-weight: 900; color: #38BDF8;">0 FCFA</span>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 14px; border-bottom-right-radius: 14px; padding: 16px 24px;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-weight: 600; border-radius: 8px;">Annuler</button>
          <button type="submit" id="btnSubmitVente" class="btn btn-success" style="background: #059669; border-color: #059669; font-weight: 900; border-radius: 8px; padding: 10px 24px; font-size: 14px; box-shadow: 0 4px 12px rgba(5,150,105,0.3);">
            <i data-lucide="check" style="width: 18px; height: 18px; display: inline-block; vertical-align: text-bottom;"></i> Encaisser &amp; Générer Ticket / Facture
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- MODAL APERÇU DÉTAILS VENTE -->
<div class="modal fade" id="modalDetailVente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: #0F172A; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0;">
          Détails de la Vente N° <span id="dt_code_display" style="font-family: monospace; font-size: 17px; color: #38BDF8;"></span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="padding: 20px;">
        <div id="dt_content">Chargement des détails...</div>
      </div>
      <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
        <a id="dt_btn_ticket" href="#" target="_blank" class="btn btn-dark" style="font-weight: 700; border-radius: 8px; font-size: 13px;">
          <i data-lucide="printer" style="width: 14px; height: 14px;"></i> Ticket 80mm
        </a>
        <a id="dt_btn_facture" href="#" target="_blank" class="btn btn-primary" style="font-weight: 700; border-radius: 8px; font-size: 13px;">
          <i data-lucide="file-text" style="width: 14px; height: 14px;"></i> Facture A4
        </a>
      </div>
    </div>
  </div>
</div>

<script>
let panier = [];

function selectTypeVente(type) {
    $('#input_type_vente').val(type);
    if (type === 'comptoir_direct') {
        $('#btnTypeComptoir').addClass('active');
        $('#btnTypeCommande').removeClass('active');
        $('#selectClient').val('').prop('disabled', true).css({ 'background-color': '#F1F5F9', 'cursor': 'not-allowed' });
    } else {
        $('#btnTypeCommande').addClass('active');
        $('#btnTypeComptoir').removeClass('active');
        $('#selectClient').prop('disabled', false).css({ 'background-color': '#FFFFFF', 'cursor': 'pointer' });
    }
    verifierBoutonEncaissement();
}

function checkReglement() {
    const reg = $('#selectReglement').val();
    if (reg === 'mobile_money' || reg === 'cheque' || reg === 'virement') {
        const net = parseFloat($('#displayNetPay').text().replace(/[^\d.-]/g, '')) || 0;
        if (net > 0) {
            $('#inputMontantRecu').val(net);
            recalculerTotaux();
        }
    }
    verifierBoutonEncaissement();
}

function ajouterArticlePanier() {
    const $prod = $('#pos_produit option:selected');
    const $cat = $('#pos_cat option:selected');
    const qte = parseInt($('#pos_qte').val()) || 1;
    const pu = parseFloat($('#pos_prix').val()) || 0;

    const prodCode = $prod.val();
    const prodNom = $prod.data('nom') || '';

    if (!prodCode) {
        showToast('error', 'Veuillez sélectionner un produit.', 'Champs Requis');
        return;
    }

    if (!pu || pu <= 0) {
        showToast('warning', 'Le prix unitaire doit être défini et supérieur à 0 FCFA.', 'Tarif Invalide');
        return;
    }

    const catCode = $cat.val() || null;
    const catNom = $cat.data('nom') || '-';

    // Empêcher la sélection/ajout si le couple Produit + Catégorie est déjà dans le panier
    const isDuplicate = panier.some(item => 
        item.produit_code === prodCode && 
        ((!item.categorie_poids_code && !catCode) || item.categorie_poids_code === catCode)
    );

    if (isDuplicate) {
        showToast('warning', `L'article "${prodNom}" (${catNom}) existe déjà dans la liste des produits sélectionnés.`, 'Article Déjà Sélectionné');
        return;
    }

    panier.push({
        produit_code: prodCode,
        produit_nom: prodNom,
        categorie_poids_code: catCode,
        categorie_poids_nom: catNom,
        quantite: qte,
        poids_total_kg: 0,
        prix_unitaire: pu,
        montant_total: qte * pu
    });

    renderPanier();
    recalculerTotaux();
}

function supprimerArticlePanier(index) {
    panier.splice(index, 1);
    renderPanier();
    recalculerTotaux();
}

function renderPanier() {
    const $tbody = $('#tbodyPanier');
    $tbody.empty();

    if (panier.length === 0) {
        $tbody.append(`
            <tr id="emptyPanierRow">
                <td colspan="6" style="text-align: center; color: #94A3B8; padding: 12px;">
                    Aucun article dans le panier. Sélectionnez un produit ci-dessus et cliquez sur <strong>+ Ajouter Panier</strong>.
                </td>
            </tr>
        `);
        $('#pos_produit').trigger('change');
        return;
    }

    panier.forEach((item, idx) => {
        $tbody.append(`
            <tr>
                <td style="font-weight: 700; color: #0F172A;">${item.produit_nom}</td>
                <td style="color: #475569;">${item.categorie_poids_nom}</td>
                <td style="text-align: center; font-weight: 800;">${item.quantite}</td>
                <td style="text-align: right; font-weight: 700;">${item.prix_unitaire.toLocaleString('fr-FR')} F</td>
                <td style="text-align: right; font-weight: 900; color: #059669;">${item.montant_total.toLocaleString('fr-FR')} F</td>
                <td style="text-align: center;">
                    <button type="button" onclick="supprimerArticlePanier(${idx})" class="btn btn-sm btn-outline-danger" style="padding: 1px 6px; font-size: 11px;">&times;</button>
                </td>
            </tr>
        `);
    });

    $('#pos_produit').trigger('change');
}

function recalculerTotaux() {
    let subtotal = 0;
    
    // Total du panier
    panier.forEach(item => {
        subtotal += item.montant_total;
    });

    // Total des étiquettes individuelles cochées
    $('.chk-etiq:checked').each(function() {
        subtotal += parseFloat($(this).data('prix')) || 0;
    });

    const remise = parseFloat($('#inputRemise').val()) || 0;
    const net = Math.max(0, subtotal - remise);
    const recu = parseFloat($('#inputMontantRecu').val()) || 0;
    const monnaie = Math.max(0, recu - net);

    $('#displaySousTotal').text(subtotal.toLocaleString('fr-FR') + ' FCFA');
    $('#displayNetPay').text(net.toLocaleString('fr-FR') + ' FCFA');
    $('#displayMonnaieRendue').text(monnaie.toLocaleString('fr-FR') + ' FCFA');

    verifierBoutonEncaissement();
}

function verifierBoutonEncaissement() {
    const typeVente = $('#input_type_vente').val() || 'comptoir_direct';
    const typeReglement = $('#selectReglement').val() || 'comptant_especes';
    const recu = parseFloat($('#inputMontantRecu').val()) || 0;
    const net = parseFloat($('#displayNetPay').text().replace(/[^\d.-]/g, '')) || 0;
    const $btn = $('#btnSubmitVente');

    if (typeVente === 'commande_livraison') {
        $btn.prop('disabled', false).css({ 'opacity': '1', 'cursor': 'pointer' });
        return;
    }

    const hasItems = (panier.length > 0 || $('.chk-etiq:checked').length > 0);

    if (!hasItems) {
        $btn.prop('disabled', true).css({ 'opacity': '0.5', 'cursor': 'not-allowed' });
        return;
    }

    const isEspecesComptoir = (typeVente === 'comptoir_direct' && typeReglement === 'comptant_especes');

    if (isEspecesComptoir) {
        if (!recu || recu <= 0 || recu < net) {
            $btn.prop('disabled', true).css({ 'opacity': '0.5', 'cursor': 'not-allowed' });
        } else {
            $btn.prop('disabled', false).css({ 'opacity': '1', 'cursor': 'pointer' });
        }
    } else {
        $btn.prop('disabled', false).css({ 'opacity': '1', 'cursor': 'pointer' });
    }
}

function imprimerJournalVentes() {
    if ($.fn.DataTable.isDataTable('#tableVentesAvicoles')) {
        let dt = $('#tableVentesAvicoles').DataTable();
        dt.page.len(-1).draw();
        setTimeout(function() {
            window.print();
        }, 400);
    } else {
        window.print();
    }
}

function showToast(type, message, title) {
    if (typeof toastr !== 'undefined' && typeof toastr[type] === 'function') {
        toastr[type](message, title || '');
    } else {
        alert((title ? title + ' : ' : '') + message);
    }
}

$(document).ready(function() {
    const baseApi = (typeof RACINE !== 'undefined') ? RACINE : '/ovolias/';

    if (new URLSearchParams(window.location.search).get('print') === '1') {
        setTimeout(function() {
            imprimerJournalVentes();
        }, 800);
    }

    const grillesTarifs = <?= json_encode($grillesTarifs) ?>;

    // Écoute dynamique du montant reçu pour verrouiller/déverrouiller le bouton d'encaissement
    $('#inputMontantRecu').on('input change keyup', function() {
        recalculerTotaux();
        verifierBoutonEncaissement();
    });

    $('#modalVente').on('shown.bs.modal', function() {
        selectTypeVente($('#input_type_vente').val() || 'comptoir_direct');
    });

    // Filtre des catégories de poids selon le produit sélectionné
    $('#pos_produit').on('change', function() {
        const pCode = $(this).val();
        const $catSelect = $('#pos_cat');

        if (!pCode) {
            $catSelect.find('option').show();
            $('#pos_prix').val('');
            return;
        }

        // Récupérer les codes de catégories de poids configurées et actives pour ce produit
        const activeCatCodes = grillesTarifs
            .filter(g => g.produit_code === pCode)
            .map(g => g.categorie_poids_code);

        // Masquer / afficher les options du select catégorie de poids (en excluant celles déjà dans le panier)
        let hasValidCat = false;
        $catSelect.find('option').each(function() {
            const val = $(this).val();
            if (!val) {
                $(this).show();
            } else if (activeCatCodes.includes(val)) {
                const isAlreadyInCart = panier.some(item => 
                    item.produit_code === pCode && 
                    ((!item.categorie_poids_code && val === 'CATP-NON-SOUMIS') || item.categorie_poids_code === val)
                );

                if (isAlreadyInCart) {
                    $(this).hide();
                } else {
                    $(this).show();
                    hasValidCat = true;
                }
            } else {
                $(this).hide();
            }
        });

        // Si des catégories spécifiques existent pour ce produit, pré-sélectionner la première valide
        if (hasValidCat) {
            const firstCat = activeCatCodes.find(c => c !== 'CATP-NON-SOUMIS');
            if (firstCat && $catSelect.find(`option[value="${firstCat}"]`).length > 0) {
                $catSelect.val(firstCat);
            } else {
                $catSelect.val('');
            }
        } else {
            $catSelect.val('');
        }

        updatePosPrix();
    });

    $('#pos_cat').on('change', function() {
        updatePosPrix();
    });

    function updatePosPrix() {
        const pCode = $('#pos_produit').val();
        const cCode = $('#pos_cat').val();

        if (!pCode) {
            $('#pos_prix').val('');
            return;
        }

        let matchPrix = null;

        // 1. Recherche par couple exact (produit + catégorie de poids)
        if (cCode) {
            const found = grillesTarifs.find(g => g.produit_code === pCode && g.categorie_poids_code === cCode);
            if (found) matchPrix = parseFloat(found.prix_vente);
        }

        // 2. Recherche pour produit non soumis à la grille de poids (CATP-NON-SOUMIS)
        if (matchPrix === null) {
            const foundFixe = grillesTarifs.find(g => g.produit_code === pCode && g.categorie_poids_code === 'CATP-NON-SOUMIS');
            if (foundFixe) matchPrix = parseFloat(foundFixe.prix_vente);
        }

        // 3. Fallback: premier tarif trouvé pour ce produit
        if (matchPrix === null) {
            const foundAny = grillesTarifs.find(g => g.produit_code === pCode);
            if (foundAny) matchPrix = parseFloat(foundAny.prix_vente);
        }

        if (matchPrix !== null && matchPrix > 0) {
            $('#pos_prix').val(matchPrix);
        } else {
            $('#pos_prix').val('');
            const prodText = $('#pos_produit option:selected').text().trim();
            const msg = "Aucun tarif configuré dans la grille pour " + (prodText || "cet article") + ". Veuillez saisir le prix unitaire.";
            showToast('warning', msg, "Tarif Non Renseigné");
        }
    }

    let dt = $('#tableVentesAvicoles').DataTable({
        ajax: {
            url: baseApi + 'aviculture/apiListVentes',
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { 
                data: 'code_vente_avicole', 
                render: d => `<code style="font-weight:900; color:#0F172A; background:#F1F5F9; padding:3px 8px; border-radius:6px; font-size:12px;">${d}</code>` 
            },
            {
                data: 'type_vente',
                render: d => (d === 'commande_livraison')
                    ? `<span style="background:#FEF3C7; color:#B45309; border:1px solid #FDE68A; font-size:11px; font-weight:800; padding:2px 8px; border-radius:10px;">Commande Pro</span>`
                    : `<span style="background:#DCFCE7; color:#166534; border:1px solid #BBF7D0; font-size:11px; font-weight:800; padding:2px 8px; border-radius:10px;">Comptoir Direct</span>`
            },
            { data: 'client_nom', render: d => `<strong style="color:#0F172A;">${d}</strong>` },
            { 
                data: 'type_reglement', 
                render: (d, t, row) => `<span class="badge bg-secondary text-uppercase" style="font-weight:700;">${d.replace('_', ' ')}</span>` 
            },
            { 
                data: 'montant_total_net', 
                render: d => `<strong style="color:#059669; font-size:14px;">${parseFloat(d||0).toLocaleString('fr-FR')} FCFA</strong>` 
            },
            {
                data: 'monnaie_rendue',
                render: d => parseFloat(d||0) > 0 
                    ? `<span style="color:#0284C7; font-weight:800;">${parseFloat(d).toLocaleString('fr-FR')} F</span>`
                    : `<span style="color:#94A3B8;">-</span>`
            },
            { data: 'date_vente', render: d => d ? new Date(d).toLocaleString('fr-FR') : '-' },
            { data: 'agent_nom', render: d => d || 'Caisse' },
            { 
                data: 'code_vente_avicole',
                className: 'text-center',
                render: (code) => `
                    <div style="display:flex; gap:6px; justify-content:center;">
                        <a href="${baseApi}aviculture/imprimerTicket/${code}" target="_blank" class="btn btn-dark btn-sm" style="background:#0F172A; border-color:#0F172A; font-weight:700; font-size:11px; padding:4px 8px; border-radius:6px;" title="Ticket Caisse 80mm">
                            🧾 Ticket
                        </a>
                        <a href="${baseApi}aviculture/imprimerFacture/${code}" target="_blank" class="btn btn-primary btn-sm" style="background:#2563EB; border-color:#2563EB; font-weight:700; font-size:11px; padding:4px 8px; border-radius:6px;" title="Facture A4">
                            📄 Facture
                        </a>
                        <button type="button" class="btn btn-light btn-sm btn-voir-detail" data-code="${code}" style="border:1px solid #CBD5E1; font-weight:700; font-size:11px; padding:4px 8px; border-radius:6px;">
                            🔍 Détails
                        </button>
                    </div>
                `
            }
        ],
        language: { url: baseApi + 'json/datatables-i18n-fr-FR.json' },
        order: [[0, 'desc']],
        drawCallback: function() { if (window.lucide) lucide.createIcons(); }
    });

    // Submit Vente POS
    $('#formVente').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');

        if (panier.length === 0 && $('.chk-etiq:checked').length === 0) {
            showToast('warning', 'Veuillez ajouter au moins un article dans le panier.', 'Panier Vide');
            return;
        }

        const typeVente = $('#input_type_vente').val() || 'comptoir_direct';
        const typeReglement = $('#selectReglement').val() || 'comptant_especes';
        const montantRecu = parseFloat($('#inputMontantRecu').val()) || 0;
        const totalNet = parseFloat($('#displayNetPay').text().replace(/[^\d.-]/g, '')) || 0;

        if (typeVente === 'comptoir_direct' && typeReglement === 'comptant_especes') {
            if (montantRecu <= 0) {
                showToast('warning', 'Veuillez saisir le montant reçu en espèces de la part du client.', 'Montant Reçu Requis');
                $('#inputMontantRecu').focus();
                return;
            }
            if (montantRecu < totalNet) {
                showToast('warning', 'Le montant reçu (' + montantRecu.toLocaleString('fr-FR') + ' FCFA) est inférieur au montant net à payer (' + totalNet.toLocaleString('fr-FR') + ' FCFA).', 'Montant Insuffisant');
                $('#inputMontantRecu').focus();
                return;
            }
        }

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Traitement...');

        const formData = $(this).serializeArray();
        formData.push({ name: 'cart_items', value: JSON.stringify(panier) });

        $.ajax({
            url: baseApi + 'aviculture/addVente',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                if (res.status == 1 || res.status === 'success' || res.success) {
                    showToast('success', res.message || 'Vente enregistrée avec succès !', 'Vente Validée');

                    $('#modalVente').modal('hide');
                    dt.ajax.reload();

                    // Proposer immédiatement d'imprimer le ticket ou la facture
                    if (res.ticket_url && res.type_vente === 'comptoir_direct') {
                        window.open(res.ticket_url, '_blank');
                    } else if (res.facture_url) {
                        window.open(res.facture_url, '_blank');
                    }

                    setTimeout(() => { location.reload(); }, 1500);
                } else {
                    showToast('error', res.message || 'Erreur lors de la vente.', 'Erreur Vente');
                    $btn.prop('disabled', false).html('<i data-lucide="check"></i> Encaisser & Générer Ticket');
                }
            },
            error: function(xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Une erreur est survenue.';
                showToast('error', msg, 'Erreur Serveur');
                $btn.prop('disabled', false).html('<i data-lucide="check"></i> Encaisser & Générer Ticket');
            }
        });
    });

    // Modal Voir Détails Vente
    $(document).on('click', '.btn-voir-detail', function() {
        const code = $(this).data('code');
        $('#dt_code_display').text(code);
        $('#dt_btn_ticket').attr('href', baseApi + 'aviculture/imprimerTicket/' + code);
        $('#dt_btn_facture').attr('href', baseApi + 'aviculture/imprimerFacture/' + code);
        $('#modalDetailVente').modal('show');

        $.get(baseApi + 'aviculture/apiDetailsVente', { code: code }, function(res) {
            if (res.status == 1 || res.status === 'success' || res.success) {
                const v = res.vente;
                const items = res.items;

                let html = `
                    <div style="background:#F8FAFC; padding:12px; border-radius:8px; margin-bottom:16px;">
                        <div><strong>Client :</strong> ${v.nom_client_avicole || 'Client Comptoir Direct'}</div>
                        <div><strong>Mode de Règlement :</strong> ${v.type_reglement.replace('_', ' ').toUpperCase()}</div>
                        <div><strong>Statut Vente :</strong> <span class="badge bg-success">${v.statut_vente}</span></div>
                        <div><strong>Type Vente :</strong> ${v.type_vente === 'commande_livraison' ? 'Commande Pro avec Livraison' : 'Vente Comptoir Directe'}</div>
                    </div>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Catégorie Poids</th>
                                <th class="text-center">Qte</th>
                                <th class="text-end">Prix U.</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                items.forEach(it => {
                    html += `
                        <tr>
                            <td>${it.libelle_produit || '-'}</td>
                            <td>${it.libelle_categorie_poids || '-'}</td>
                            <td class="text-center">${it.quantite}</td>
                            <td class="text-end">${parseFloat(it.prix_unitaire).toLocaleString('fr-FR')} F</td>
                            <td class="text-end font-weight-bold">${parseFloat(it.montant_total).toLocaleString('fr-FR')} F</td>
                        </tr>
                    `;
                });

                html += `
                        </tbody>
                    </table>
                    <div class="text-end font-weight-bold style="font-size:16px;">
                        NET À PAYER : <span style="color:#059669;">${parseFloat(v.montant_total_net).toLocaleString('fr-FR')} FCFA</span>
                    </div>
                `;

                if (v.montant_recu > 0) {
                    html += `
                        <div class="text-end text-muted style="font-size:13px;">
                            Montant Remis : ${parseFloat(v.montant_recu).toLocaleString('fr-FR')} F | Monnaie Rendue : <strong>${parseFloat(v.monnaie_rendue).toLocaleString('fr-FR')} F</strong>
                        </div>
                    `;
                }

                $('#dt_content').html(html);
            }
        }, 'json');
    });
});
</script>

<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
