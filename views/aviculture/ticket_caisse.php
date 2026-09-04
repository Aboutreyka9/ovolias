<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ticket de Caisse - <?= htmlspecialchars($vente['code_vente_avicole'] ?? '') ?></title>
  <style>
    @page {
      size: 80mm auto;
      margin: 0;
    }
    body {
      font-family: 'Courier New', Courier, monospace;
      width: 76mm;
      margin: 0 auto;
      padding: 10px 4px;
      font-size: 11px;
      line-height: 1.3;
      color: #000;
      background: #FFF;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .bold { font-weight: bold; }
    .logo {
      font-size: 16px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .separator {
      border-top: 1px dashed #000;
      margin: 8px 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 6px 0;
    }
    th, td {
      padding: 3px 0;
      vertical-align: top;
    }
    th {
      border-bottom: 1px solid #000;
      text-align: left;
    }
    .btn-print {
      margin-bottom: 12px;
      padding: 8px 16px;
      background: #0F172A;
      color: #FFF;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
      width: 100%;
    }
    @media print {
      .no-print { display: none !important; }
      body { width: 100%; padding: 0; }
    }
  </style>
</head>
<body onload="window.print()">

  <div class="no-print">
    <button class="btn-print" onclick="window.print()">🖨️ IMPRIMER CE TICKET (80mm)</button>
  </div>

  <div class="text-center">
    <div class="logo">OVOLIA AVICULTURE</div>
    <div style="font-size: 10px; font-weight: bold;">
      <?= htmlspecialchars($vente['nom_etablissement'] ?? 'OVOLIA S.A.') ?>
    </div>
    <div><?= htmlspecialchars($vente['adresse_etablissement'] ?? 'Abidjan, Côte d\'Ivoire') ?></div>
    <div>Tél: <?= htmlspecialchars($vente['telephone_etablissement'] ?? '07 00 00 00 00') ?></div>
  </div>

  <div class="separator"></div>

  <div>
    <div><strong>TICKET N° :</strong> <?= htmlspecialchars($vente['code_vente_avicole']) ?></div>
    <div><strong>Date :</strong> <?= date('d/m/Y H:i', strtotime($vente['date_vente'])) ?></div>
    <div><strong>Caissier(e) :</strong> <?= htmlspecialchars(($vente['nom_user'] ?? '') . ' ' . ($vente['prenom_user'] ?? '')) ?></div>
    <div><strong>Client :</strong> <?= htmlspecialchars($vente['nom_client'] ?? 'Client Comptoir') ?></div>
    <div><strong>Mode Rég. :</strong> <?= strtoupper(str_replace('_', ' ', $vente['type_reglement'])) ?></div>
  </div>

  <div class="separator"></div>

  <table>
    <thead>
      <tr>
        <th>Article / Poids</th>
        <th class="text-center">Qte</th>
        <th class="text-right">P.U</th>
        <th class="text-right">Total</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
          <tr>
            <td colspan="4" class="bold">
              <?= htmlspecialchars($item['libelle_produit']) ?>
              <?php if (!empty($item['libelle_categorie_poids'])): ?>
                (<?= htmlspecialchars($item['libelle_categorie_poids']) ?>)
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <td style="font-size: 10px; padding-left: 6px;">
              <?= $item['poids_total_kg'] > 0 ? number_format($item['poids_total_kg'], 2, ',', ' ') . ' kg' : '-' ?>
            </td>
            <td class="text-center"><?= (int)$item['quantite'] ?></td>
            <td class="text-right"><?= number_format($item['prix_unitaire'], 0, ',', ' ') ?></td>
            <td class="text-right bold"><?= number_format($item['montant_total'], 0, ',', ' ') ?> F</td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="separator"></div>

  <table>
    <tr>
      <td>Total Brut :</td>
      <td class="text-right"><?= number_format($vente['montant_total_ht'], 0, ',', ' ') ?> F</td>
    </tr>
    <?php if ($vente['montant_remise'] > 0): ?>
    <tr>
      <td>Remise Accordée :</td>
      <td class="text-right">-<?= number_format($vente['montant_remise'], 0, ',', ' ') ?> F</td>
    </tr>
    <?php endif; ?>
    <tr style="font-size: 13px;" class="bold">
      <td>NET À PAYER :</td>
      <td class="text-right"><?= number_format($vente['montant_total_net'], 0, ',', ' ') ?> F</td>
    </tr>
    <?php if ($vente['type_reglement'] === 'comptant_especes'): ?>
    <tr>
      <td>Montant Remis :</td>
      <td class="text-right bold"><?= number_format($vente['montant_recu'] > 0 ? $vente['montant_recu'] : $vente['montant_paye'], 0, ',', ' ') ?> F</td>
    </tr>
    <tr class="bold" style="font-size: 12px;">
      <td>MONNAIE RENDUE :</td>
      <td class="text-right"><?= number_format($vente['monnaie_rendue'], 0, ',', ' ') ?> F</td>
    </tr>
    <?php endif; ?>
  </table>

  <div class="separator"></div>

  <div class="text-center" style="margin-top: 8px;">
    <div class="bold">Merci de votre confiance !</div>
    <div style="font-size: 9px; margin-top: 4px;">Les marchandises vendues ne sont ni reprises ni échangées.</div>
    <div style="font-size: 8px; margin-top: 6px; color: #555;">OVOLIA AVIFARM v2.0</div>
  </div>

</body>
</html>
