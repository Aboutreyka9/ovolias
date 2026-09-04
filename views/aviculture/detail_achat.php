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

      <!-- BANNIÈRE EXECUTIVE NAVY (#0F172A) : INFORMATIONS GÉNÉRALES -->
      <div style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); border-radius: 16px; padding: 28px 32px; color: #FFFFFF; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.25); margin-bottom: 24px; border: 1px solid rgba(255,255,255,0.08); position: relative; overflow: hidden;">
        <!-- Element décoratif d'arrière-plan -->
        <div style="position: absolute; right: -30px; top: -30px; width: 180px; height: 180px; background: radial-gradient(circle, rgba(56,189,248,0.12) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; pointer-events: none;"></div>

        <!-- Rangée Supérieure : Titre & Badges Executive -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.12); padding-bottom: 18px; margin-bottom: 22px;">
          <div style="display: flex; align-items: center; gap: 12px;">
            <div style="background: rgba(56, 189, 248, 0.15); color: #38BDF8; padding: 10px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
              <i data-lucide="file-text" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
              <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: #FFFFFF; letter-spacing: 0.5px; text-transform: uppercase;">
                Informations Générales sur la Transaction
              </h3>
              <span style="font-size: 12px; color: #94A3B8;">Bon d'Achat Avicole Officiel GEICG</span>
            </div>
          </div>

          <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <!-- Badge Statut Achat -->
            <?php if ($statutAchat === 'valide' || $statutAchat === 'recu'): ?>
              <span style="background: rgba(56, 189, 248, 0.15); color: #38BDF8; border: 1px solid rgba(56, 189, 248, 0.3); font-weight: 700; padding: 6px 14px; border-radius: 20px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                <span style="width: 7px; height: 7px; background: #38BDF8; border-radius: 50%; display: inline-block;"></span> Achat Validé
              </span>
            <?php else: ?>
              <span style="background: rgba(245, 158, 11, 0.15); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.3); font-weight: 700; padding: 6px 14px; border-radius: 20px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                <span style="width: 7px; height: 7px; background: #FBBF24; border-radius: 50%; display: inline-block;"></span> En Attente
              </span>
            <?php endif; ?>

            <!-- Badge Statut Règlement -->
            <?php if ($statutReglement === 'paye'): ?>
              <span style="background: rgba(34, 197, 94, 0.15); color: #4ADE80; border: 1px solid rgba(34, 197, 94, 0.3); font-weight: 700; padding: 6px 14px; border-radius: 20px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                <span style="width: 7px; height: 7px; background: #4ADE80; border-radius: 50%; display: inline-block;"></span> Règlement Payé
              </span>
            <?php else: ?>
              <span style="background: rgba(244, 63, 94, 0.15); color: #FB7185; border: 1px solid rgba(244, 63, 94, 0.3); font-weight: 700; padding: 6px 14px; border-radius: 20px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                <span style="width: 7px; height: 7px; background: #FB7185; border-radius: 50%; display: inline-block;"></span> <?= ucfirst($statutReglement) ?>
              </span>
            <?php endif; ?>
          </div>
        </div>

        <!-- Grille Métrique Executive (4 Cartes Puces) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 20px;">
          <!-- Tuile 1 : Code Achat & Facture -->
          <div style="background: rgba(255, 255, 255, 0.05); padding: 16px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.08);">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
              <i data-lucide="qr-code" style="width: 16px; height: 16px; color: #38BDF8;"></i>
              <span style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px;">Code & Facture</span>
            </div>
            <div style="font-size: 18px; font-weight: 800; color: #FFFFFF; font-family: monospace;"><?= $codeAchat ?></div>
            <div style="font-size: 12px; color: #CBD5E1; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
              <i data-lucide="receipt" style="width: 13px; height: 13px; color: #94A3B8;"></i> Facture : <strong><?= $numFacture ?></strong>
            </div>
          </div>

          <!-- Tuile 2 : Fournisseur Avicole -->
          <div style="background: rgba(255, 255, 255, 0.05); padding: 16px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.08);">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
              <i data-lucide="truck" style="width: 16px; height: 16px; color: #4ADE80;"></i>
              <span style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px;">Fournisseur</span>
            </div>
            <div style="font-size: 16px; font-weight: 800; color: #F8FAFC; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= $fournisseurNom ?></div>
            <div style="font-size: 12px; color: #CBD5E1; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
              <i data-lucide="phone" style="width: 13px; height: 13px; color: #94A3B8;"></i> Tél : <strong><?= htmlspecialchars($achat['telephone_fournisseur_avicole'] ?? 'N/A') ?></strong>
            </div>
          </div>

          <!-- Tuile 3 : Agent & Date -->
          <div style="background: rgba(255, 255, 255, 0.05); padding: 16px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.08);">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
              <i data-lucide="user-check" style="width: 16px; height: 16px; color: #A78BFA;"></i>
              <span style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px;">Agent & Horodatage</span>
            </div>
            <div style="font-size: 16px; font-weight: 700; color: #F1F5F9;"><?= $agentNom ?></div>
            <div style="font-size: 12px; color: #CBD5E1; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
              <i data-lucide="calendar" style="width: 13px; height: 13px; color: #94A3B8;"></i> Saisi le <strong><?= $dateAchat ?></strong>
            </div>
          </div>

          <!-- Tuile 4 : Total Financier & Quantités -->
          <div style="background: rgba(244, 63, 94, 0.1); padding: 16px; border-radius: 12px; border: 1px solid rgba(244, 63, 94, 0.25);">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
              <i data-lucide="coins" style="width: 16px; height: 16px; color: #FB7185;"></i>
              <span style="font-size: 11px; font-weight: 800; color: #FDA4AF; text-transform: uppercase; letter-spacing: 0.5px;">Total Transaction</span>
            </div>
            <div style="font-size: 20px; font-weight: 900; color: #FFFFFF; font-family: system-ui, sans-serif;"><?= number_format($montantTotal, 0, ',', ' ') ?> FCFA</div>
            <div style="font-size: 12px; color: #FECDD3; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
              <i data-lucide="layers" style="width: 13px; height: 13px; color: #FB7185;"></i> Quantité Totale : <strong><?= number_format($totQteGlobal, 2, ',', ' ') ?></strong>
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
