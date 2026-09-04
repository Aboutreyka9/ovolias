<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$achat = $achat ?? [];
$details = $details ?? [];
$codeAchat = htmlspecialchars($achat['code_achat_avicole'] ?? '-');
$fournisseurNom = htmlspecialchars($achat['fournisseur_nom'] ?? $achat['nom_fournisseur_avicole'] ?? 'Fournisseur Général');
$numFacture = htmlspecialchars($achat['numero_facture_fournisseur'] ?? '-');
$dateAchat = !empty($achat['date_achat']) ? date('d/m/Y H:i', strtotime($achat['date_achat'])) : '-';
$agentNom = htmlspecialchars($achat['agent_nom'] ?? 'Système');
$montantTotal = floatval($achat['montant_total'] ?? 0);
$statutReglement = strtolower($achat['statut_reglement'] ?? 'impaye');
$statutAchat = strtolower($achat['statut_achat'] ?? 'valide');
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">
      
      <!-- EN-TÊTE PAGE -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Détails du Bon d'Achat : <?= $codeAchat ?></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">
            Facture FRS : <code style="font-weight: 800; color: #1E3A5F; font-size: 13px; background: #F1F5F9; padding: 2px 8px; border-radius: 4px;"><?= $numFacture ?></code> &bull; 
            Saisi le : <strong><?= $dateAchat ?></strong>
          </p>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="<?= RACINE ?>aviculture/achats" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour aux achats
          </a>
          <button onclick="window.print();" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="printer" style="width: 18px; height: 18px;"></i> Imprimer Bon / Facture
          </button>
        </div>
      </div>

<?php
$totQteGlobal = 0;
foreach ($details as $dItem) {
    $totQteGlobal += floatval($dItem['quantite'] ?? 0);
}
?>

      <!-- OPTION 1 : LAYOUT HYBRIDE EN 2 BLOCS SÉPARÉS (FICHE + PANNEAU KPI) -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 24px;">
        
        <!-- BLOC GAUCHE : FICHE SIGNALÉTIQUE TRANSACTION (60%) -->
        <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); box-sizing: border-box;">
          <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
            <i data-lucide="file-text" style="width: 18px; height: 18px; color: #3B82F6;"></i> Informations sur la Transaction & Fournisseur
          </h3>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Item 1 : Code & Facture -->
            <div style="display: flex; gap: 12px; align-items: flex-start;">
              <div style="background: #EFF6FF; color: #3B82F6; padding: 10px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i data-lucide="qr-code" style="width: 20px; height: 20px;"></i>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Code & Facture FRS</span>
                <div style="font-size: 16px; font-weight: 800; color: #0F172A; font-family: monospace; margin-top: 2px;"><?= $codeAchat ?></div>
                <div style="font-size: 12px; color: #64748B; margin-top: 2px;">N° Facture : <strong><?= $numFacture ?></strong></div>
              </div>
            </div>

            <!-- Item 2 : Fournisseur Avicole -->
            <div style="display: flex; gap: 12px; align-items: flex-start;">
              <div style="background: #ECFDF5; color: #10B981; padding: 10px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i data-lucide="truck" style="width: 20px; height: 20px;"></i>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Fournisseur Avicole</span>
                <div style="font-size: 15px; font-weight: 800; color: #0F172A; margin-top: 2px;"><?= $fournisseurNom ?></div>
                <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Tél : <strong><?= htmlspecialchars($achat['telephone_fournisseur_avicole'] ?? 'N/A') ?></strong></div>
              </div>
            </div>

            <!-- Item 3 : Agent Saisie -->
            <div style="display: flex; gap: 12px; align-items: flex-start;">
              <div style="background: #F3E8FF; color: #8B5CF6; padding: 10px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i data-lucide="user-check" style="width: 20px; height: 20px;"></i>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Agent de Saisie</span>
                <div style="font-size: 15px; font-weight: 800; color: #334155; margin-top: 2px;"><?= $agentNom ?></div>
                <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Opérateur agréé</div>
              </div>
            </div>

            <!-- Item 4 : Date & Horodatage -->
            <div style="display: flex; gap: 12px; align-items: flex-start;">
              <div style="background: #FEF3C7; color: #D97706; padding: 10px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i data-lucide="calendar" style="width: 20px; height: 20px;"></i>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Date & Horodatage</span>
                <div style="font-size: 15px; font-weight: 800; color: #0F172A; margin-top: 2px;"><?= $dateAchat ?></div>
                <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Enregistrement système</div>
              </div>
            </div>
          </div>
        </div>

        <!-- BLOC DROIT : PANNEAU SYNTHÈSE FINANCIÈRE & STATUTS KPI (40%) -->
        <div class="card" style="background: #F8FAFC; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box;">
          <div>
            <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #E2E8F0; padding-bottom: 10px;">
              <i data-lucide="pie-chart" style="width: 18px; height: 18px; color: #DC2626;"></i> Synthèse Financière & Statuts
            </h3>

            <!-- KPI Montant Total & Quantité -->
            <div style="display: flex; justify-content: space-between; align-items: baseline; background: #FFFFFF; padding: 14px 18px; border-radius: 10px; border: 1px solid #E2E8F0; margin-bottom: 16px;">
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Montant Total Achat</span>
                <div style="font-size: 22px; font-weight: 900; color: #DC2626; margin-top: 2px;"><?= number_format($montantTotal, 0, ',', ' ') ?> FCFA</div>
              </div>
              <div style="text-align: right;">
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Qté Totale</span>
                <div style="font-size: 16px; font-weight: 800; color: #1E3A5F; margin-top: 2px;"><?= number_format($totQteGlobal, 2, ',', ' ') ?></div>
              </div>
            </div>
          </div>

          <!-- Badges de Statuts -->
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; display: block; margin-bottom: 8px;">Statut Achat & Règlement :</span>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
              <?php if ($statutAchat === 'valide' || $statutAchat === 'recu'): ?>
                <span class="badge" style="background:#E0F2FE; color:#0369A1; border:1px solid #BAE6FD; padding:8px 16px; border-radius:10px; font-weight:800; font-size:13px; display:inline-flex; align-items:center; gap:6px;">
                  <i data-lucide="check-circle" style="width: 15px; height: 15px;"></i> Achat Validé
                </span>
              <?php else: ?>
                <span class="badge" style="background:#FEF3C7; color:#92400E; border:1px solid #FDE68A; padding:8px 16px; border-radius:10px; font-weight:800; font-size:13px; display:inline-flex; align-items:center; gap:6px;">
                  <i data-lucide="clock" style="width: 15px; height: 15px;"></i> En attente
                </span>
              <?php endif; ?>

              <?php if ($statutReglement === 'paye'): ?>
                <span class="badge" style="background:#DCFCE7; color:#15803D; border:1px solid #BBF7D0; padding:8px 16px; border-radius:10px; font-weight:800; font-size:13px; display:inline-flex; align-items:center; gap:6px;">
                  <i data-lucide="credit-card" style="width: 15px; height: 15px;"></i> Payé
                </span>
              <?php else: ?>
                <span class="badge" style="background:#FEF3C7; color:#92400E; border:1px solid #FDE68A; padding:8px 16px; border-radius:10px; font-weight:800; font-size:13px; display:inline-flex; align-items:center; gap:6px;">
                  <i data-lucide="alert-circle" style="width: 15px; height: 15px;"></i> <?= ucfirst($statutReglement) ?>
                </span>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>

      <!-- CARTE 2 : LISTE DES ARTICLES COMMANDÉS -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 2px solid #EFF6FF;">
          <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="shopping-bag" style="width: 18px; height: 18px; color: #1E3A5F;"></i> Articles & Intrants Acquis
          </h3>
          <div style="font-size: 16px; font-weight: 900; color: #DC2626;">
            Montant Total : <?= number_format($montantTotal, 0, ',', ' ') ?> FCFA
          </div>
        </div>

        <?php if (empty($details)): ?>
          <p style="color: #94A3B8; text-align: center; padding: 20px 0; font-style: italic;">Aucun article n'a été répertorié pour cet achat.</p>
        <?php else: ?>
          <div style="width: 100%; overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
              <thead>
                <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                  <th style="padding: 10px 12px;">#</th>
                  <th style="padding: 10px 12px;">Désignation Intrant / Article</th>
                  <th style="padding: 10px 12px; text-align: center;">Quantité</th>
                  <th style="padding: 10px 12px; text-align: center;">Unité</th>
                  <th style="padding: 10px 12px; text-align: right;">Prix Unitaire</th>
                  <th style="padding: 10px 12px; text-align: right;">Montant Total</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $i = 0;
                $totQte = 0;
                $totMontant = 0;
                foreach ($details as $d): 
                  $i++;
                  $qte = floatval($d['quantite'] ?? 0);
                  $pu = floatval($d['prix_unitaire'] ?? 0);
                  $subTot = floatval($d['montant_total'] ?? ($qte * $pu));
                  $totQte += $qte;
                  $totMontant += $subTot;
                ?>
                  <tr style="border-bottom: 1px solid #F1F5F9;">
                    <td style="padding: 10px 12px; font-weight: 700; color: #64748B;"><?= $i ?></td>
                    <td style="padding: 10px 12px; font-weight: 700; color: #0F172A;"><?= htmlspecialchars($d['libelle_article_intrant'] ?? 'Article') ?></td>
                    <td style="padding: 10px 12px; text-align: center; font-weight: 800; color: #1E3A5F;"><?= number_format($qte, 2, ',', ' ') ?></td>
                    <td style="padding: 10px 12px; text-align: center; color: #64748B;"><?= htmlspecialchars($d['unite_mesure'] ?? '-') ?></td>
                    <td style="padding: 10px 12px; text-align: right; font-weight: 600; color: #334155;"><?= number_format($pu, 0, ',', ' ') ?> FCFA</td>
                    <td style="padding: 10px 12px; text-align: right; font-weight: 800; color: #0F172A;"><?= number_format($subTot, 0, ',', ' ') ?> FCFA</td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr style="background: #F8FAFC; border-top: 2px solid #E2E8F0;">
                  <th colspan="2" style="padding: 12px; text-align: right; font-weight: 800; color: #0F172A;">TOTAUX :</th>
                  <th style="padding: 12px; text-align: center; font-weight: 900; color: #1E3A5F;"><?= number_format($totQte, 2, ',', ' ') ?></th>
                  <th style="padding: 12px;"></th>
                  <th style="padding: 12px;"></th>
                  <th style="padding: 12px; text-align: right; font-weight: 900; color: #DC2626; font-size: 15px;"><?= number_format($totMontant, 0, ',', ' ') ?> FCFA</th>
                </tr>
              </tfoot>
            </table>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>
</div>
<script>$(document).ready(function() { if (window.lucide) lucide.createIcons(); });</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
