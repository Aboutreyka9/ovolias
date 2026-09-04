<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Bon de Livraison <?= htmlspecialchars($bl['code_livraison']) ?> - Ovolia</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Arial, sans-serif;
    }
    body {
      background: #E2E8F0;
      padding: 20px;
      color: #0F172A;
    }
    .page-a4 {
      background: #FFFFFF;
      width: 210mm;
      min-height: 297mm;
      margin: 0 auto;
      padding: 20mm 15mm;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      position: relative;
    }
    .header-table {
      width: 100%;
      border-bottom: 3px solid #2563EB;
      padding-bottom: 15px;
      margin-bottom: 20px;
    }
    .company-title {
      font-size: 24px;
      font-weight: 900;
      color: #2563EB;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .company-sub {
      font-size: 12px;
      color: #64748B;
      margin-top: 4px;
    }
    .bl-title-box {
      background: #EFF6FF;
      border: 2px solid #2563EB;
      border-radius: 8px;
      padding: 10px 16px;
      text-align: right;
    }
    .bl-number {
      font-size: 20px;
      font-weight: 900;
      color: #1E40AF;
      font-family: monospace;
    }
    .info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 24px;
    }
    .info-card {
      border: 1px solid #CBD5E1;
      border-radius: 8px;
      padding: 14px;
      background: #F8FAFC;
    }
    .card-title {
      font-size: 11px;
      font-weight: 800;
      color: #2563EB;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 8px;
      border-bottom: 1px solid #E2E8F0;
      padding-bottom: 4px;
    }
    .info-line {
      font-size: 12px;
      margin-bottom: 4px;
      color: #334155;
    }
    .info-line strong {
      color: #0F172A;
    }
    .table-items {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 24px;
    }
    .table-items th {
      background: #1E293B;
      color: #FFFFFF;
      font-size: 12px;
      font-weight: 800;
      padding: 10px 12px;
      text-align: left;
    }
    .table-items td {
      border-bottom: 1px solid #E2E8F0;
      padding: 10px 12px;
      font-size: 12px;
      color: #1E293B;
    }
    .table-items tbody tr:nth-child(even) {
      background: #F8FAFC;
    }
    .totaux-box {
      width: 280px;
      margin-left: auto;
      border: 1px solid #CBD5E1;
      border-radius: 8px;
      padding: 12px;
      background: #F1F5F9;
      margin-bottom: 40px;
    }
    .totaux-line {
      display: flex;
      justify-content: space-between;
      font-size: 13px;
      margin-bottom: 6px;
    }
    .signatures-grid {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 15px;
      margin-top: 40px;
      page-break-inside: avoid;
    }
    .sig-box {
      border: 1px solid #94A3B8;
      border-radius: 8px;
      padding: 12px;
      min-height: 120px;
      text-align: center;
      background: #FFFFFF;
    }
    .sig-title {
      font-size: 11px;
      font-weight: 800;
      color: #475569;
      text-transform: uppercase;
      border-bottom: 1px solid #E2E8F0;
      padding-bottom: 4px;
      margin-bottom: 10px;
    }

    @media print {
      body {
        background: #FFFFFF;
        padding: 0;
      }
      .page-a4 {
        box-shadow: none;
        width: 100%;
        min-height: auto;
        padding: 10mm 10mm;
      }
      .no-print {
        display: none !important;
      }
    }
  </style>
</head>
<body>

  <!-- BARRE DE CONTRÔLE D'IMPRESSION (MASQUÉE À L'IMPRESSION) -->
  <div class="no-print" style="max-width: 210mm; margin: 0 auto 15px auto; display: flex; justify-content: space-between; align-items: center;">
    <a href="javascript:history.back()" style="text-decoration: none; color: #475569; font-weight: 700; font-size: 13px;">
      ← Retour aux Expéditions
    </a>
    <button onclick="window.print()" style="background: #0F172A; color: #FFFFFF; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px;">
      🖨️ Imprimer le Bon de Livraison
    </button>
  </div>

  <div class="page-a4">
    <!-- EN-TÊTE DU BON DE LIVRAISON -->
    <table class="header-table">
      <tr>
        <td style="vertical-align: top;">
          <div class="company-title">OVOLIA AVICULTURE</div>
          <div class="company-sub">
            <?= htmlspecialchars($bl['nom_etablissement'] ?? 'Établissement Principal') ?><br>
            <?= htmlspecialchars($bl['adresse_etablissement'] ?? 'Zone Industrielle, Côte d\'Ivoire') ?><br>
            Tél: <?= htmlspecialchars($bl['telephone_etablissement'] ?? '+225 07 00 00 00 00') ?> | Email: <?= htmlspecialchars($bl['email_etablissement'] ?? 'contact@ovolia.ci') ?>
          </div>
        </td>
        <td style="vertical-align: top; text-align: right; width: 45%;">
          <div class="bl-title-box">
            <div style="font-size: 12px; font-weight: 800; color: #475569; text-transform: uppercase;">BON DE LIVRAISON (BL)</div>
            <div class="bl-number"><?= htmlspecialchars($bl['code_livraison']) ?></div>
            <div style="font-size: 11px; color: #64748B; margin-top: 4px;">
              Date BL: <strong><?= date('d/m/Y H:i', strtotime($bl['date_planification'])) ?></strong><br>
              Commande N°: <strong><?= htmlspecialchars($bl['vente_code']) ?></strong>
            </div>
          </div>
        </td>
      </tr>
    </table>

    <!-- ADRESSES & TRANSPORTEURS -->
    <div class="info-grid">
      <!-- CARTOUCHE CLIENT -->
      <div class="info-card">
        <div class="card-title">DESTINATAIRE / CLIENT</div>
        <div class="info-line" style="font-size: 14px; font-weight: 900; color: #0F172A;">
          <?= htmlspecialchars($bl['raison_sociale'] ?? trim(($bl['nom_client'] ?? '') . ' ' . ($bl['prenom_client'] ?? ''))) ?>
        </div>
        <div class="info-line">Adresse: <strong><?= htmlspecialchars($bl['adresse_client'] ?? 'Non spécifiée') ?> <?= !empty($bl['ville_client']) ? '('.htmlspecialchars($bl['ville_client']).')' : '' ?></strong></div>
        <div class="info-line">Téléphone: <strong><?= htmlspecialchars($bl['telephone_client'] ?? 'N/A') ?></strong></div>
        <div class="info-line">Règlement: <strong style="text-transform: uppercase; color: #2563EB;"><?= str_replace('_', ' ', $bl['type_reglement']) ?></strong></div>
      </div>

      <!-- CARTOUCHE EXPÉDITION & VÉHICULE -->
      <div class="info-card">
        <div class="card-title">LOGISTIQUE &amp; EXPÉDITION</div>
        <div class="info-line">Chauffeur / Livreur: <strong><?= htmlspecialchars(trim(($bl['livreur_nom'] ?? '') . ' ' . ($bl['livreur_prenom'] ?? ''))) ?></strong></div>
        <div class="info-line">Téléphone Livreur: <strong><?= htmlspecialchars($bl['livreur_tel'] ?? 'N/A') ?></strong></div>
        <div class="info-line">Véhicule: <strong><?= htmlspecialchars($bl['libelle_vehicule'] ?? 'Transport Interne') ?></strong></div>
        <div class="info-line">Immatriculation: <strong><?= htmlspecialchars($bl['immatriculation'] ?? 'N/A') ?></strong></div>
      </div>
    </div>

    <!-- TABLEAU DES MARCHANDISES EMBARQUÉES -->
    <table class="table-items">
      <thead>
        <tr>
          <th style="width: 5%;">#</th>
          <th>Désignation Produit</th>
          <th>Grille de Poids</th>
          <th style="text-align: center; width: 15%;">Quantité (Pièces)</th>
          <th style="text-align: center; width: 15%;">Poids Total (Kg)</th>
          <th style="text-align: right; width: 15%;">Prix Unit. FCFA</th>
          <th style="text-align: right; width: 18%;">Montant Total FCFA</th>
        </tr>
      </thead>
      <tbody>
        <?php 
          $totQte = 0;
          $totPoids = 0;
          $totMontant = 0;
          $i = 1;
        ?>
        <?php if (!empty($items)): ?>
          <?php foreach ($items as $item): ?>
            <?php 
              $qte = (int)$item['quantite'];
              $poids = (float)$item['poids_total_kg'];
              $montant = (float)$item['montant_total'];
              $totQte += $qte;
              $totPoids += $poids;
              $totMontant += $montant;
            ?>
            <tr>
              <td><?= $i++ ?></td>
              <td style="font-weight: 800; color: #0F172A;"><?= htmlspecialchars($item['libelle_produit'] ?? $item['produit_code']) ?></td>
              <td><?= htmlspecialchars($item['libelle_categorie_poids'] ?? '-') ?></td>
              <td style="text-align: center; font-weight: 900; color: #1E40AF;"><?= number_format($qte, 0, ',', ' ') ?></td>
              <td style="text-align: center; font-weight: 800; color: #0369A1;"><?= number_format($poids, 2, ',', ' ') ?> kg</td>
              <td style="text-align: right; font-weight: 700; color: #475569;"><?= number_format($item['prix_unitaire'], 0, ',', ' ') ?></td>
              <td style="text-align: right; font-weight: 900; color: #059669;"><?= number_format($montant, 0, ',', ' ') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- RÉCAPITULATIF DES TOTAUX -->
    <div class="totaux-box">
      <div class="totaux-line">
        <span>Total Pièces:</span>
        <strong style="color: #1E40AF;"><?= number_format($totQte, 0, ',', ' ') ?> unités</strong>
      </div>
      <div class="totaux-line">
        <span>Poids Total Livré:</span>
        <strong style="color: #0369A1;"><?= number_format($totPoids, 2, ',', ' ') ?> Kg</strong>
      </div>
      <div class="totaux-line" style="font-size: 15px; font-weight: 900; border-top: 2px solid #CBD5E1; padding-top: 6px; margin-top: 6px; color: #059669;">
        <span>Montant Net:</span>
        <span><?= number_format($totMontant, 0, ',', ' ') ?> FCFA</span>
      </div>
    </div>

    <!-- ZONES DE SIGNATURES OFFICIELLES -->
    <div class="signatures-grid">
      <div class="sig-box">
        <div class="sig-title">Magasinier / Émetteur</div>
        <div style="font-size: 11px; color: #64748B; margin-top: 45px;">Date &amp; Signature</div>
      </div>

      <div class="sig-box">
        <div class="sig-title">Transporteur / Livreur</div>
        <div style="font-size: 11px; color: #64748B; margin-top: 45px;">Nom: <?= htmlspecialchars($bl['livreur_nom'] ?? '') ?><br>Signature</div>
      </div>

      <div class="sig-box">
        <div class="sig-title">Réceptionnaire / Client</div>
        <div style="font-size: 10px; color: #64748B; font-style: italic; margin-bottom: 25px;">"Reçu les marchandises conformes en quantité et qualité"</div>
        <div style="font-size: 11px; color: #64748B;">Date, Cachet &amp; Signature</div>
      </div>
    </div>

  </div>

</body>
</html>
