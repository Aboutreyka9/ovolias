<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Facture de Vente - <?= htmlspecialchars($vente['code_vente_avicole'] ?? '') ?></title>
  <style>
    @page {
      size: A4;
      margin: 15mm;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #0F172A;
      margin: 0;
      padding: 0;
      font-size: 13px;
      line-height: 1.5;
      background: #FFF;
    }
    .invoice-container {
      max-width: 800px;
      margin: 0 auto;
      background: #FFF;
    }
    .header-flex {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 2px solid #0F172A;
      padding-bottom: 16px;
      margin-bottom: 20px;
    }
    .company-title {
      font-size: 24px;
      font-weight: 900;
      color: #0F172A;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .invoice-title {
      font-size: 20px;
      font-weight: 800;
      color: #2563EB;
      text-align: right;
    }
    .meta-box {
      background: #F8FAFC;
      border: 1px solid #E2E8F0;
      border-radius: 8px;
      padding: 16px;
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
    }
    .table-items {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 24px;
    }
    .table-items th {
      background: #0F172A;
      color: #FFF;
      padding: 10px 12px;
      font-weight: 700;
      text-align: left;
      font-size: 12px;
      text-transform: uppercase;
    }
    .table-items td {
      padding: 10px 12px;
      border-bottom: 1px solid #E2E8F0;
    }
    .totals-flex {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 30px;
    }
    .totals-table {
      width: 320px;
      border-collapse: collapse;
    }
    .totals-table td {
      padding: 8px 12px;
      border-bottom: 1px solid #E2E8F0;
    }
    .totals-table tr.grand-total {
      background: #0F172A;
      color: #FFF;
      font-weight: 900;
      font-size: 15px;
    }
    .signatures {
      display: flex;
      justify-content: space-between;
      margin-top: 50px;
      padding-top: 20px;
      border-top: 1px solid #E2E8F0;
    }
    .sig-box {
      width: 45%;
      text-align: center;
    }
    .btn-print {
      background: #0F172A;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      font-weight: 800;
      cursor: pointer;
      margin-bottom: 20px;
    }
    @media print {
      .no-print { display: none !important; }
      body { padding: 0; }
    }
  </style>
</head>
<body onload="window.print()">

  <div class="invoice-container">
    <div class="no-print" style="text-align: right;">
      <button class="btn-print" onclick="window.print()">🖨️ IMPRIMER LA FACTURE A4</button>
    </div>

    <!-- EN-TÊTE -->
    <div class="header-flex">
      <div>
        <div class="company-title">OVOLIA AVICULTURE</div>
        <div style="font-weight: 700; color: #475569; margin-top: 2px;">
          <?= htmlspecialchars($vente['nom_etablissement'] ?? 'Etablissement Avicole OVOLIA') ?>
        </div>
        <div style="color: #64748B; font-size: 12px;">
          <?= htmlspecialchars($vente['adresse_etablissement'] ?? 'Abidjan, Côte d\'Ivoire') ?><br>
          Tél: <?= htmlspecialchars($vente['telephone_etablissement'] ?? '07 00 00 00 00') ?>
        </div>
      </div>
      <div>
        <div class="invoice-title">FACTURE N°</div>
        <div style="font-family: monospace; font-size: 18px; font-weight: 900; color: #0F172A; text-align: right;">
          <?= htmlspecialchars($vente['code_vente_avicole']) ?>
        </div>
        <div style="font-size: 12px; color: #64748B; text-align: right; margin-top: 4px;">
          Date : <strong><?= date('d/m/Y H:i', strtotime($vente['date_vente'])) ?></strong>
        </div>
      </div>
    </div>

    <!-- METADONNÉES CLIENT & VENTE -->
    <div class="meta-box">
      <div>
        <div style="font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase;">CLIENT / FACTURÉ À</div>
        <div style="font-size: 16px; font-weight: 900; color: #0F172A; margin-top: 4px;">
          <?= htmlspecialchars($vente['nom_client'] ?? 'Client Particulier') ?>
        </div>
        <div style="color: #475569; font-size: 12px;">
          Type : <strong><?= strtoupper(htmlspecialchars($vente['type_client_avicole'] ?? 'Comptoir')) ?></strong><br>
          Téléphone : <?= htmlspecialchars($vente['telephone_client'] ?? 'Non renseigné') ?><br>
          Adresse : <?= htmlspecialchars($vente['adresse_client'] ?? 'Non renseignée') ?>
        </div>
      </div>

      <div style="text-align: right;">
        <div style="font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase;">DETAILS DU RÈGLEMENT</div>
        <div style="font-size: 14px; font-weight: 800; color: #2563EB; margin-top: 4px;">
          Mode : <?= strtoupper(str_replace('_', ' ', $vente['type_reglement'])) ?>
        </div>
        <div style="font-size: 12px; color: #475569; margin-top: 4px;">
          Type de Vente : <strong><?= $vente['type_vente'] === 'commande_livraison' ? 'Commande avec Livraison' : 'Vente Comptoir Directe' ?></strong><br>
          Statut Livraison : <strong><?= strtoupper(htmlspecialchars($vente['statut_livraison'] ?? 'NON REQUIS')) ?></strong><br>
          Agent Caisse : <strong><?= htmlspecialchars(($vente['nom_user'] ?? '') . ' ' . ($vente['prenom_user'] ?? '')) ?></strong>
        </div>
      </div>
    </div>

    <!-- TABLEAU DE PRODUITS -->
    <table class="table-items">
      <thead>
        <tr>
          <th>Réf. / Produit</th>
          <th>Catégorie de Poids</th>
          <th style="text-align: center;">Quantité</th>
          <th style="text-align: right;">Poids Total (Kg)</th>
          <th style="text-align: right;">Prix Unitaire</th>
          <th style="text-align: right;">Montant Total</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($items)): ?>
          <?php foreach ($items as $item): ?>
            <tr>
              <td>
                <strong style="color: #0F172A;"><?= htmlspecialchars($item['libelle_produit']) ?></strong>
                <?php if (!empty($item['code_etiquette'])): ?>
                  <br><span style="font-family: monospace; font-size: 11px; color: #64748B;">Étiq: <?= htmlspecialchars($item['code_etiquette']) ?></span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($item['libelle_categorie_poids'] ?? '-') ?></td>
              <td style="text-align: center; font-weight: 800;"><?= (int)$item['quantite'] ?></td>
              <td style="text-align: right; font-weight: 700; color: #0369A1;">
                <?= $item['poids_total_kg'] > 0 ? number_format($item['poids_total_kg'], 3, ',', ' ') . ' kg' : '-' ?>
              </td>
              <td style="text-align: right; font-weight: 700;"><?= number_format($item['prix_unitaire'], 0, ',', ' ') ?> FCFA</td>
              <td style="text-align: right; font-weight: 900; color: #059669;"><?= number_format($item['montant_total'], 0, ',', ' ') ?> FCFA</td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align: center; color: #94A3B8; padding: 20px;">Aucun article dans cette facture.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- TOTALS -->
    <div class="totals-flex">
      <table class="totals-table">
        <tr>
          <td>Montant Total HT :</td>
          <td style="text-align: right; font-weight: 700;"><?= number_format($vente['montant_total_ht'], 0, ',', ' ') ?> FCFA</td>
        </tr>
        <?php if ($vente['montant_remise'] > 0): ?>
        <tr>
          <td>Remise Commerciale :</td>
          <td style="text-align: right; font-weight: 700; color: #DC2626;">-<?= number_format($vente['montant_remise'], 0, ',', ' ') ?> FCFA</td>
        </tr>
        <?php endif; ?>
        <tr class="grand-total">
          <td>NET À PAYER :</td>
          <td style="text-align: right;"><?= number_format($vente['montant_total_net'], 0, ',', ' ') ?> FCFA</td>
        </tr>
        <tr>
          <td>Montant Payé / Encaissé :</td>
          <td style="text-align: right; font-weight: 800; color: #059669;"><?= number_format($vente['montant_paye'], 0, ',', ' ') ?> FCFA</td>
        </tr>
        <?php if ($vente['type_reglement'] === 'comptant_especes' && $vente['montant_recu'] > 0): ?>
        <tr>
          <td>Montant Remis Client :</td>
          <td style="text-align: right; font-weight: 700;"><?= number_format($vente['montant_recu'], 0, ',', ' ') ?> FCFA</td>
        </tr>
        <tr>
          <td>Monnaie Rendue Client :</td>
          <td style="text-align: right; font-weight: 800; color: #2563EB;"><?= number_format($vente['monnaie_rendue'], 0, ',', ' ') ?> FCFA</td>
        </tr>
        <?php endif; ?>
      </table>
    </div>

    <!-- SIGNATURES -->
    <div class="signatures">
      <div class="sig-box">
        <div style="font-weight: 800; color: #475569;">Le Responsable Caisse / Commercial</div>
        <div style="height: 60px;"></div>
        <div style="font-size: 11px; color: #94A3B8;">(Nom, Signature & Cachet)</div>
      </div>
      <div class="sig-box">
        <div style="font-weight: 800; color: #475569;">Le Client (Bon pour accord & Réception)</div>
        <div style="height: 60px;"></div>
        <div style="font-size: 11px; color: #94A3B8;">(Nom, Signature)</div>
      </div>
    </div>

  </div>

</body>
</html>
