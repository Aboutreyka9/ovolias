<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>

<style>
@media print {
  .sidebar, .navbar, .header, #sidebarToggle, .btn, .nav-pills,
  .dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate, .modal {
    display: none !important;
  }
  .content-wrapper {
    padding: 0 !important;
    background: #FFFFFF !important;
  }
  .card {
    border: none !important;
    box-shadow: none !important;
    padding: 0 !important;
  }
  .table {
    width: 100% !important;
    border-collapse: collapse !important;
  }
  .table th, .table td {
    border: 1px solid #94A3B8 !important;
    padding: 6px 10px !important;
    font-size: 11px !important;
  }
}
</style>

<div class="content-wrapper" style="padding: 24px; background: #F8FAFC; min-height: 100vh;">
  <!-- EN-TÊTE PAGE ET NAVIGATION -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
      <h1 style="font-size: 24px; font-weight: 900; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 10px;">
        <i data-lucide="truck" style="width: 28px; height: 28px; color: #D97706;"></i> Expéditions &amp; Planning des Livraisons
      </h1>
      <p style="font-size: 13px; color: #64748B; margin: 4px 0 0 0;">
        Affectation des véhicules/chauffeurs, impression des Bons de Livraison (BL) et déchargement des stocks
      </p>
    </div>

    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
      <button type="button" onclick="const pWin = window.open(window.location.href + (window.location.href.indexOf('?') > -1 ? '&' : '?') + 'print=1', '_blank');" class="btn btn-dark" style="font-weight: 800; border-radius: 8px; font-size: 13px; background: #0F172A; border-color: #0F172A; color: #FFFFFF; display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; box-shadow: 0 2px 6px rgba(15,23,42,0.2);">
        <i data-lucide="printer" style="width: 16px; height: 16px; color: #FFFFFF;"></i> Imprimer la Liste
      </button>
      <a href="<?= RACINE ?>aviculture/vehicules" class="btn btn-dark" style="font-weight: 700; border-radius: 8px; font-size: 13px; background: #0F172A; border-color: #0F172A; color: #FFFFFF; display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px;">
        <i data-lucide="car" style="width: 16px; height: 16px;"></i> Flotte de Véhicules
      </a>
      <a href="<?= RACINE ?>aviculture/stock" target="_blank" class="btn btn-light" style="font-weight: 700; border-radius: 8px; font-size: 13px; border: 1px solid #CBD5E1; display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px;">
        <i data-lucide="package" style="width: 16px; height: 16px; color: #475569;"></i> État des Stocks
      </a>
    </div>
  </div>

  <!-- CARTES KPIS EXÉCUTIVES -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Commandes À Planifier</span>
          <div style="font-size: 24px; font-weight: 900; color: #D97706; margin-top: 6px;"><?= $nbAPlanifier ?></div>
        </div>
        <div style="background: #FEF3C7; color: #D97706; padding: 10px; border-radius: 10px;">
          <i data-lucide="clock" style="width: 22px; height: 22px;"></i>
        </div>
      </div>
      <div style="font-size: 12px; color: #64748B; margin-top: 8px; font-weight: 600;">Commandes en attente de camion</div>
    </div>

    <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Livraisons En Cours</span>
          <div style="font-size: 24px; font-weight: 900; color: #2563EB; margin-top: 6px;"><?= $nbEnCours ?></div>
        </div>
        <div style="background: #EFF6FF; color: #2563EB; padding: 10px; border-radius: 10px;">
          <i data-lucide="navigation" style="width: 22px; height: 22px;"></i>
        </div>
      </div>
      <div style="font-size: 12px; color: #64748B; margin-top: 8px; font-weight: 600;">Tournées affectées &amp; BL générés</div>
    </div>

    <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Livrées Récentes</span>
          <div style="font-size: 24px; font-weight: 900; color: #059669; margin-top: 6px;"><?= $nbLivrees ?></div>
        </div>
        <div style="background: #ECFDF5; color: #10B981; padding: 10px; border-radius: 10px;">
          <i data-lucide="check-circle" style="width: 22px; height: 22px;"></i>
        </div>
      </div>
      <div style="font-size: 12px; color: #64748B; margin-top: 8px; font-weight: 600;">Marchandises déchargées avec succès</div>
    </div>
  </div>

  <!-- ONGLET DE NAVIGATION DU TABLEAU DE BORD -->
  <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist" style="background: #E2E8F0; padding: 4px; border-radius: 10px; display: inline-flex;">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="tab-planifier-btn" data-bs-toggle="pill" data-bs-target="#tab-planifier" type="button" role="tab" style="font-weight: 800; font-size: 13px; border-radius: 8px;">
        <i data-lucide="calendar" style="width: 15px; height: 15px; display: inline-block; vertical-align: text-bottom;"></i> Commandes à Planifier (<?= $nbAPlanifier ?>)
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="tab-encours-btn" data-bs-toggle="pill" data-bs-target="#tab-encours" type="button" role="tab" style="font-weight: 800; font-size: 13px; border-radius: 8px;">
        <i data-lucide="truck" style="width: 15px; height: 15px; display: inline-block; vertical-align: text-bottom;"></i> En Cours / Planifiées (<?= $nbEnCours ?>)
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="tab-livrees-btn" data-bs-toggle="pill" data-bs-target="#tab-livrees" type="button" role="tab" style="font-weight: 800; font-size: 13px; border-radius: 8px;">
        <i data-lucide="archive" style="width: 15px; height: 15px; display: inline-block; vertical-align: text-bottom;"></i> Historique Livrées (<?= $nbLivrees ?>)
      </button>
    </li>
  </ul>

  <!-- CONTENU DES ONGLETS -->
  <div class="tab-content" id="pills-tabContent">
    
    <!-- ONGLET 1 : COMMANDES À PLANIFIER -->
    <div class="tab-pane fade show active" id="tab-planifier" role="tabpanel">
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="table-responsive">
          <table class="table table-hover align-middle datatable-expedition" style="width: 100%; font-size: 13px;">
            <thead style="background: #F8FAFC; color: #475569; font-weight: 800;">
              <tr>
                <th>Réf. Commande</th>
                <th>Date Commande</th>
                <th>Client / Destination</th>
                <th>Téléphone</th>
                <th>Montant Net</th>
                <th>Zone Commerciale</th>
                <th style="text-align: center;">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($commandesAPlanifier)): ?>
                <?php foreach ($commandesAPlanifier as $c): ?>
                  <?php 
                    $clientNom = htmlspecialchars($c['raison_sociale'] ?? trim(($c['nom_client'] ?? '') . ' ' . ($c['prenom_client'] ?? '')));
                    if (empty($clientNom)) $clientNom = 'Client Particulier';
                  ?>
                  <tr>
                    <td style="font-weight: 900; font-family: monospace; color: #0F172A;"><?= htmlspecialchars($c['code_vente_avicole']) ?></td>
                    <td style="font-weight: 600; color: #64748B;"><?= date('d/m/Y H:i', strtotime($c['date_vente'])) ?></td>
                    <td style="font-weight: 800; color: #0F172A;"><?= $clientNom ?></td>
                    <td style="font-weight: 700; color: #0284C7;"><?= htmlspecialchars($c['telephone_client'] ?? '-') ?></td>
                    <td style="font-weight: 900; color: #059669;"><?= number_format($c['montant_total_net'], 0, ',', ' ') ?> FCFA</td>
                    <td style="font-size: 12px; color: #475569;"><?= htmlspecialchars($c['libelle_zone'] ?? '-') ?></td>
                    <td style="text-align: center;">
                      <button type="button" class="btn btn-primary btn-sm btn-planifier" 
                              data-vente="<?= htmlspecialchars($c['code_vente_avicole']) ?>"
                              data-client="<?= htmlspecialchars($clientNom) ?>"
                              style="background: #2563EB; border-color: #2563EB; font-weight: 800; border-radius: 8px; font-size: 12px; padding: 6px 14px;">
                        <i data-lucide="calendar" style="width: 14px; height: 14px;"></i> Planifier la Livraison
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ONGLET 2 : LIVRAISONS EN COURS / PLANIFIÉES -->
    <div class="tab-pane fade" id="tab-encours" role="tabpanel">
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="table-responsive">
          <table class="table table-hover align-middle datatable-expedition" style="width: 100%; font-size: 13px;">
            <thead style="background: #F8FAFC; color: #475569; font-weight: 800;">
              <tr>
                <th>N° Bon de Livraison (BL)</th>
                <th>Réf. Commande</th>
                <th>Date Prévue</th>
                <th>Client / Destination</th>
                <th>Chauffeur / Livreur</th>
                <th>Véhicule</th>
                <th>Statut</th>
                <th style="text-align: center;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($livraisonsEnCours)): ?>
                <?php foreach ($livraisonsEnCours as $l): ?>
                  <?php 
                    $clientNom = htmlspecialchars($l['raison_sociale'] ?? trim(($l['nom_client'] ?? '') . ' ' . ($l['prenom_client'] ?? '')));
                    if (empty($clientNom)) $clientNom = 'Client Particulier';
                    $livreurNom = htmlspecialchars(trim(($l['nom_user'] ?? '') . ' ' . ($l['prenom_user'] ?? '')));
                  ?>
                  <tr>
                    <td style="font-weight: 900; font-family: monospace; color: #2563EB;"><?= htmlspecialchars($l['code_livraison']) ?></td>
                    <td style="font-weight: 700; color: #475569;"><?= htmlspecialchars($l['vente_code']) ?></td>
                    <td style="font-weight: 700; color: #D97706;"><?= date('d/m/Y H:i', strtotime($l['date_planification'])) ?></td>
                    <td style="font-weight: 800; color: #0F172A;"><?= $clientNom ?></td>
                    <td style="font-weight: 700; color: #0369A1;"><?= $livreurNom ?></td>
                    <td style="font-size: 12px; color: #334155; font-weight: 700;">
                      <?= htmlspecialchars($l['libelle_vehicule'] ?? 'Non spécifié') ?> 
                      <?= !empty($l['immatriculation']) ? '('.htmlspecialchars($l['immatriculation']).')' : '' ?>
                    </td>
                    <td>
                      <span style="background: #FEF3C7; color: #B45309; border: 1px solid #FDE68A; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;">
                        <i data-lucide="navigation" style="width: 12px; height: 12px;"></i> En cours
                      </span>
                    </td>
                    <td style="text-align: center;">
                      <div style="display: flex; gap: 6px; justify-content: center;">
                        <a href="<?= RACINE ?>aviculture/imprimerBL/<?= htmlspecialchars($l['code_livraison']) ?>" target="_blank" class="btn btn-light btn-sm" style="border: 1px solid #CBD5E1; border-radius: 8px; font-weight: 700; font-size: 12px;" title="Imprimer le Bon de Livraison">
                          <i data-lucide="printer" style="width: 14px; height: 14px;"></i> Imprimer BL
                        </a>
                        <button type="button" class="btn btn-success btn-sm btn-valider-liv" 
                                data-bl="<?= htmlspecialchars($l['code_livraison']) ?>"
                                data-client="<?= htmlspecialchars($clientNom) ?>"
                                style="background: #059669; border-color: #059669; font-weight: 800; border-radius: 8px; font-size: 12px;">
                          <i data-lucide="check" style="width: 14px; height: 14px;"></i> Confirmer Livraison
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ONGLET 3 : HISTORIQUE DES LIVRAISONS LIVRÉES -->
    <div class="tab-pane fade" id="tab-livrees" role="tabpanel">
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="table-responsive">
          <table class="table table-hover align-middle datatable-expedition" style="width: 100%; font-size: 13px;">
            <thead style="background: #F8FAFC; color: #475569; font-weight: 800;">
              <tr>
                <th>N° Bon de Livraison (BL)</th>
                <th>Réf. Commande</th>
                <th>Date Effectuée</th>
                <th>Client</th>
                <th>Réceptionnaire</th>
                <th>Livreur</th>
                <th>Statut</th>
                <th style="text-align: center;">Impression</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($livraisonsLivrees)): ?>
                <?php foreach ($livraisonsLivrees as $l): ?>
                  <?php 
                    $clientNom = htmlspecialchars($l['raison_sociale'] ?? trim(($l['nom_client'] ?? '') . ' ' . ($l['prenom_client'] ?? '')));
                    if (empty($clientNom)) $clientNom = 'Client Particulier';
                  ?>
                  <tr>
                    <td style="font-weight: 900; font-family: monospace; color: #0F172A;"><?= htmlspecialchars($l['code_livraison']) ?></td>
                    <td style="font-weight: 700; color: #475569;"><?= htmlspecialchars($l['vente_code']) ?></td>
                    <td style="font-weight: 700; color: #059669;"><?= date('d/m/Y H:i', strtotime($l['date_livraison_effective'])) ?></td>
                    <td style="font-weight: 800; color: #0F172A;"><?= $clientNom ?></td>
                    <td style="font-weight: 700; color: #0369A1;"><?= htmlspecialchars($l['nom_receptionnaire'] ?? 'Non renseigné') ?></td>
                    <td style="font-size: 12px; color: #475569;"><?= htmlspecialchars(trim(($l['nom_user'] ?? '') . ' ' . ($l['prenom_user'] ?? ''))) ?></td>
                    <td>
                      <span style="background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;">
                        <i data-lucide="check-circle" style="width: 12px; height: 12px;"></i> Livré &amp; Déchargé
                      </span>
                    </td>
                    <td style="text-align: center;">
                      <a href="<?= RACINE ?>aviculture/imprimerBL/<?= htmlspecialchars($l['code_livraison']) ?>" target="_blank" class="btn btn-dark btn-sm" style="background: #0F172A; border-color: #0F172A; border-radius: 8px; font-weight: 700; font-size: 12px;">
                        <i data-lucide="printer" style="width: 14px; height: 14px;"></i> BL A4
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- MODAL PLANIFIER UNE LIVRAISON -->
<div class="modal fade" id="modalPlanifierLivraison" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: #2563EB; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="truck" style="width: 20px; height: 20px; color: #93C5FD;"></i> Affectation &amp; Planification de Livraison
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formPlanifierLivraison">
        <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
        <input type="hidden" name="vente_code" id="planif_vente_code">

        <div class="modal-body" style="padding: 20px;">
          <div style="background: #EFF6FF; border: 1px solid #BFDBFE; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
            <div style="font-size: 12px; color: #1E40AF; font-weight: 800;">Commande N° <span id="planif_code_display" style="font-family: monospace; font-size: 14px;"></span></div>
            <div style="font-size: 13px; color: #0F172A; font-weight: 700; margin-top: 2px;" id="planif_client_display"></div>
          </div>

          <div class="mb-3">
            <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Livreur / Chauffeur Responsable *</label>
            <select name="livreur_user_code" class="form-select" required style="border-radius: 8px; font-size: 13px;">
              <option value="">-- Sélectionner un livreur --</option>
              <?php foreach ($livreurs as $liv): ?>
                <option value="<?= $liv['code_user'] ?>"><?= htmlspecialchars($liv['nom_user'] . ' ' . $liv['prenom_user']) ?> (<?= htmlspecialchars($liv['telephone_user'] ?? 'N/A') ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Véhicule de Transport (Facultatif)</label>
            <select name="vehicule_code" class="form-select" style="border-radius: 8px; font-size: 13px;">
              <option value="">-- Aucun / Transport Externe --</option>
              <?php foreach ($vehicules as $v): ?>
                <option value="<?= $v['code_vehicule'] ?>"><?= htmlspecialchars($v['libelle_vehicule']) ?> (<?= htmlspecialchars($v['immatriculation']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Date &amp; Heure de Livraison Prévue *</label>
            <input type="datetime-local" name="date_planification" value="<?= date('Y-m-d\TH:i', strtotime('+2 hours')) ?>" required class="form-control" style="border-radius: 8px; font-size: 13px;">
          </div>

          <div class="mb-3">
            <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Notes / Instructions de livraison</label>
            <textarea name="notes_livraison" class="form-control" rows="2" placeholder="ex: Livrer à la réception arrière de l'Hôtel, appeler le gérant à l'arrivée..." style="border-radius: 8px; font-size: 13px;"></textarea>
          </div>
        </div>

        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-weight: 600; border-radius: 8px;">Annuler</button>
          <button type="submit" class="btn btn-primary" style="background: #2563EB; border-color: #2563EB; font-weight: 800; border-radius: 8px; padding: 8px 20px; font-size: 13px;">
            Générer BL &amp; Planifier
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL CONFIRMER LA LIVRAISON & DÉCHARGER STOCK -->
<div class="modal fade" id="modalValiderLivraison" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: #059669; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #A7F3D0;"></i> Confirmation de Réception &amp; Déchargement Stock
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formValiderLivraison">
        <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
        <input type="hidden" name="code_livraison" id="val_code_livraison">

        <div class="modal-body" style="padding: 20px;">
          <div style="background: #ECFDF5; border: 1px solid #A7F3D0; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
            <div style="font-size: 12px; color: #065F46; font-weight: 800;">Bon de Livraison N° <span id="val_bl_display" style="font-family: monospace; font-size: 14px;"></span></div>
            <div style="font-size: 13px; color: #0F172A; font-weight: 700; margin-top: 2px;" id="val_client_display"></div>
            <p style="font-size: 12px; color: #047857; margin: 6px 0 0 0; font-weight: 600;">
              ⚠️ La confirmation de cette livraison validera définitivement la sortie physique des marchandises (`SORTIE_DISTRIBUTION`) dans l'inventaire du stock.
            </p>
          </div>

          <div class="mb-3">
            <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Nom &amp; Prénom du Réceptionnaire Client</label>
            <input type="text" name="nom_receptionnaire" class="form-control" placeholder="ex: M. Koffi (Chef Cuisinier)" style="border-radius: 8px; font-size: 13px;">
          </div>

          <div class="mb-3">
            <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Remarques / Écarts ou réserves éventuelles</label>
            <textarea name="notes_livraison" class="form-control" rows="2" placeholder="ex: Livré conforme sans réserve..." style="border-radius: 8px; font-size: 13px;"></textarea>
          </div>
        </div>

        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-weight: 600; border-radius: 8px;">Annuler</button>
          <button type="submit" class="btn btn-success" style="background: #059669; border-color: #059669; font-weight: 800; border-radius: 8px; padding: 8px 20px; font-size: 13px;">
            Confirmer &amp; Décharger le Stock
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('.datatable-expedition').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            },
            pageLength: 15,
            order: [[0, 'desc']]
        });
    }

    function notifyMsg(message, type = 'success') {
        if (typeof toastr !== 'undefined' && toastr[type]) {
            toastr[type](message);
        } else {
            alert(message);
        }
    }

    // Ouvrir modal planification
    $(document).on('click', '.btn-planifier', function() {
        const vente = $(this).data('vente');
        const client = $(this).data('client');
        $('#planif_vente_code').val(vente);
        $('#planif_code_display').text(vente);
        $('#planif_client_display').text('Client: ' + client);
        $('#modalPlanifierLivraison').modal('show');
    });

    // Submit Planifier
    $('#formPlanifierLivraison').on('submit', function(e) {
        e.preventDefault();
        const baseApi = (typeof RACINE !== 'undefined') ? RACINE : '/ovolias/';
        const $btn = $(this).find('button[type="submit"]');

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Génération BL...');

        $.ajax({
            url: baseApi + 'aviculture/planifierLivraison',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    notifyMsg(res.message, 'success');
                    $('#modalPlanifierLivraison').modal('hide');
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    notifyMsg(res.message || 'Erreur lors de la planification', 'error');
                    $btn.prop('disabled', false).html('Générer BL & Planifier');
                }
            },
            error: function(xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Une erreur est survenue.';
                notifyMsg(msg, 'error');
                $btn.prop('disabled', false).html('Générer BL & Planifier');
            }
        });
    });

    // Ouvrir modal valider livraison
    $(document).on('click', '.btn-valider-liv', function() {
        const bl = $(this).data('bl');
        const client = $(this).data('client');
        $('#val_code_livraison').val(bl);
        $('#val_bl_display').text(bl);
        $('#val_client_display').text('Destinataire: ' + client);
        $('#modalValiderLivraison').modal('show');
    });

    // Submit Valider Livraison
    $('#formValiderLivraison').on('submit', function(e) {
        e.preventDefault();
        const baseApi = (typeof RACINE !== 'undefined') ? RACINE : '/ovolias/';
        const $btn = $(this).find('button[type="submit"]');

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Validation...');

        $.ajax({
            url: baseApi + 'aviculture/validerLivraison',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    notifyMsg(res.message, 'success');
                    $('#modalValiderLivraison').modal('hide');
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    notifyMsg(res.message || 'Erreur lors de la validation', 'error');
                    $btn.prop('disabled', false).html('Confirmer & Décharger le Stock');
                }
            },
            error: function(xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Une erreur est survenue.';
                notifyMsg(msg, 'error');
                $btn.prop('disabled', false).html('Confirmer & Décharger le Stock');
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../public/inc/footer.php'; ?>
