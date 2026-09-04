<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>

<style>
@media print {
  .sidebar, .navbar, .header, #sidebarToggle, .card.mb-4, .btn,
  .dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate {
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
        <i data-lucide="history" style="width: 28px; height: 28px; color: #D97706;"></i> Journal des Mouvements de Stock
      </h1>
      <p style="font-size: 13px; color: #64748B; margin: 4px 0 0 0;">
        Historique et traçabilité chronologique complète de toutes les entrées et sorties de stock
      </p>
    </div>

    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
      <button type="button" onclick="window.print()" class="btn btn-primary" style="font-weight: 700; border-radius: 8px; font-size: 13px; background: #2563EB; border-color: #2563EB; display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; box-shadow: 0 2px 6px rgba(37,99,235,0.2);">
        <i data-lucide="printer" style="width: 16px; height: 16px;"></i> Imprimer le Journal
      </button>
      <a href="<?= RACINE ?>aviculture/stock" class="btn btn-light" style="font-weight: 700; border-radius: 8px; font-size: 13px; border: 1px solid #CBD5E1; display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px;">
        <i data-lucide="arrow-left" style="width: 16px; height: 16px; color: #475569;"></i> Retour au Stock Global
      </a>
    </div>
  </div>

  <!-- BARRE DE FILTRES -->
  <div class="card mb-4" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <form method="GET" action="<?= RACINE ?>aviculture/mouvements_stock" class="row g-3 align-items-end">
      <div class="col-md-3">
        <label style="font-weight: 800; font-size: 12px; color: #334155; margin-bottom: 6px; display: block;">Date Début</label>
        <input type="date" name="date_debut" value="<?= htmlspecialchars($dateDebut) ?>" class="form-control form-control-sm" style="border-radius: 8px; font-size: 13px;">
      </div>
      
      <div class="col-md-3">
        <label style="font-weight: 800; font-size: 12px; color: #334155; margin-bottom: 6px; display: block;">Date Fin</label>
        <input type="date" name="date_fin" value="<?= htmlspecialchars($dateFin) ?>" class="form-control form-control-sm" style="border-radius: 8px; font-size: 13px;">
      </div>

      <div class="col-md-4">
        <label style="font-weight: 800; font-size: 12px; color: #334155; margin-bottom: 6px; display: block;">Type de Mouvement</label>
        <select name="type_mouvement" class="form-select form-select-sm" style="border-radius: 8px; font-size: 13px;">
          <option value="">Tous les mouvements</option>
          <option value="ENTREE_ACHAT" <?= ($typeMvt === 'ENTREE_ACHAT') ? 'selected' : '' ?>>+ Entrée Achat Fournisseur</option>
          <option value="ENTREE_ABATTAGE" <?= ($typeMvt === 'ENTREE_ABATTAGE') ? 'selected' : '' ?>>+ Entrée Abattage Ferme</option>
          <option value="AJUSTEMENT_INVENTAIRE" <?= ($typeMvt === 'AJUSTEMENT_INVENTAIRE') ? 'selected' : '' ?>>+ Ajustement Inventaire</option>
          <option value="SORTIE_VENTE_DIRECTE" <?= ($typeMvt === 'SORTIE_VENTE_DIRECTE') ? 'selected' : '' ?>>- Sortie Vente Directe Client</option>
          <option value="SORTIE_DISTRIBUTION" <?= ($typeMvt === 'SORTIE_DISTRIBUTION') ? 'selected' : '' ?>>- Sortie Distribution</option>
          <option value="PERTE_REFORME" <?= ($typeMvt === 'PERTE_REFORME') ? 'selected' : '' ?>>- Pertes &amp; Avaries</option>
        </select>
      </div>

      <div class="col-md-2" style="display: flex; gap: 8px;">
        <button type="submit" class="btn btn-primary btn-sm w-100" style="background: #2563EB; border-color: #2563EB; font-weight: 800; border-radius: 8px; padding: 8px 14px;">
          <i data-lucide="filter" style="width: 14px; height: 14px;"></i> Filtrer
        </button>
        <a href="<?= RACINE ?>aviculture/mouvements_stock" class="btn btn-light btn-sm" style="border: 1px solid #CBD5E1; border-radius: 8px;" title="Réinitialiser">
          <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i>
        </a>
      </div>
    </form>
  </div>

  <!-- TABLEAU DES MOUVEMENTS DE STOCK -->
  <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #F1F5F9; flex-wrap: wrap; gap: 12px;">
      <h3 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="list" style="width: 20px; height: 20px; color: #D97706;"></i> Registre Chronologique des Flux
      </h3>
      <span style="font-size: 12px; font-weight: 700; color: #64748B; background: #F1F5F9; padding: 4px 12px; border-radius: 12px;">
        <?= count($mouvements) ?> enregistrement(s)
      </span>
    </div>

    <div class="table-responsive">
      <table id="tableMouvements" class="table table-hover align-middle" style="width: 100%; font-size: 13px;">
        <thead style="background: #F8FAFC; color: #475569; font-weight: 800; border-bottom: 2px solid #E2E8F0;">
          <tr>
            <th style="padding: 12px 16px;">Date &amp; Heure</th>
            <th style="padding: 12px 16px;">Code Mouvement</th>
            <th style="padding: 12px 16px;">Type de Flux</th>
            <th style="padding: 12px 16px;">Produit / Intrant</th>
            <th style="padding: 12px 16px;">Grille Poids</th>
            <th style="padding: 12px 16px; text-align: center;">Quantité</th>
            <th style="padding: 12px 16px; text-align: center;">Poids (Kg)</th>
            <th style="padding: 12px 16px;">Document Réf.</th>
            <th style="padding: 12px 16px;">Opérateur</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($mouvements)): ?>
            <?php foreach ($mouvements as $m): ?>
              <?php 
                $dateMvt = date('d/m/Y H:i', strtotime($m['date_mouvement']));
                $qte = (int)$m['quantite_pieces'];
                $poids = (float)$m['poids_total_kg'];
                $type = $m['type_mouvement'];
                $isEntree = in_array($type, ['ENTREE_ACHAT', 'ENTREE_ABATTAGE']) || ($type === 'AJUSTEMENT_INVENTAIRE' && $qte >= 0);
                $operator = trim(($m['nom_user'] ?? '') . ' ' . ($m['prenom_user'] ?? ''));
                if (empty($operator)) $operator = htmlspecialchars($m['user_code'] ?? 'Système');
              ?>
              <tr>
                <td style="padding: 14px 16px; font-weight: 700; color: #475569;">
                  <?= $dateMvt ?>
                </td>

                <td style="padding: 14px 16px; font-weight: 800; font-family: monospace; color: #0F172A;">
                  <?= htmlspecialchars($m['code_mouvement']) ?>
                </td>

                <td style="padding: 14px 16px;">
                  <?php if ($type === 'ENTREE_ACHAT'): ?>
                    <span style="background: #DCFCE7; color: #15803D; border: 1px solid #BBF7D0; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;">
                      <i data-lucide="arrow-down-left" style="width: 12px; height: 12px;"></i> + Entrée Achat
                    </span>
                  <?php elseif ($type === 'ENTREE_ABATTAGE'): ?>
                    <span style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;">
                      <i data-lucide="arrow-down-left" style="width: 12px; height: 12px;"></i> + Entrée Abattage
                    </span>
                  <?php elseif ($type === 'AJUSTEMENT_INVENTAIRE'): ?>
                    <span style="background: #F3E8FF; color: #6B21A8; border: 1px solid #E9D5FF; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;">
                      <i data-lucide="sliders" style="width: 12px; height: 12px;"></i> Ajustement
                    </span>
                  <?php elseif ($type === 'SORTIE_VENTE_DIRECTE'): ?>
                    <span style="background: #FEE2E2; color: #B91C1C; border: 1px solid #FECACA; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;">
                      <i data-lucide="arrow-up-right" style="width: 12px; height: 12px;"></i> - Sortie Vente
                    </span>
                  <?php else: ?>
                    <span style="background: #FEF3C7; color: #B45309; border: 1px solid #FDE68A; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;">
                      <i data-lucide="minus-circle" style="width: 12px; height: 12px;"></i> <?= htmlspecialchars($type) ?>
                    </span>
                  <?php endif; ?>
                </td>

                <td style="padding: 14px 16px; font-weight: 800; color: #0F172A;">
                  <?= htmlspecialchars($m['libelle_produit'] ?? $m['produit_code']) ?>
                </td>

                <td style="padding: 14px 16px;">
                  <?php if (!empty($m['libelle_categorie_poids'])): ?>
                    <span style="color: #0369A1; font-weight: 700; font-size: 12px;">
                      <?= htmlspecialchars($m['libelle_categorie_poids']) ?>
                    </span>
                  <?php else: ?>
                    <span style="color: #94A3B8;">-</span>
                  <?php endif; ?>
                </td>

                <td style="padding: 14px 16px; text-align: center; font-weight: 900; color: <?= $isEntree ? '#15803D' : '#B91C1C' ?>;">
                  <?= $isEntree ? '+' : '' ?><?= number_format($qte, 0, ',', ' ') ?>
                </td>

                <td style="padding: 14px 16px; text-align: center; font-weight: 800; color: #0369A1;">
                  <?= number_format(abs($poids), 2, ',', ' ') ?> kg
                </td>

                <td style="padding: 14px 16px; font-size: 12px; color: #334155; font-weight: 600;">
                  <?= htmlspecialchars($m['reference_document'] ?? '-') ?>
                </td>

                <td style="padding: 14px 16px; font-size: 12px; color: #475569; font-weight: 700;">
                  <?= htmlspecialchars($operator) ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#tableMouvements').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            },
            pageLength: 25,
            order: [[0, 'desc']]
        });
    }
});
</script>

<?php require_once __DIR__ . '/../../public/inc/footer.php'; ?>
