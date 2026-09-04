<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$achat = $achat ?? [];
$details = $details ?? [];
$payments = $payments ?? [];
$canReglerFacture = $canReglerFacture ?? true;
$canVerifierAchat = $canVerifierAchat ?? true;
$canValiderAchat  = $canValiderAchat ?? true;

$codeAchat = htmlspecialchars($achat['code_achat_avicole'] ?? '-');
$fournisseurNom = htmlspecialchars($achat['fournisseur_nom'] ?? $achat['nom_fournisseur_avicole'] ?? 'Fournisseur Général');
$fournisseurTel = htmlspecialchars($achat['telephone_fournisseur_avicole'] ?? 'Non renseigné');
$fournisseurAdresse = htmlspecialchars($achat['adresse_fournisseur_avicole'] ?? 'Non renseignée');
$numFacture = htmlspecialchars($achat['numero_facture_fournisseur'] ?? '-');
$dateAchat = !empty($achat['date_achat']) ? date('d/m/Y H:i', strtotime($achat['date_achat'])) : '-';
$agentNom = htmlspecialchars($achat['agent_nom'] ?? 'Système');

$montantTotal = floatval($achat['montant_total'] ?? 0);
$montantPaye = floatval($achat['montant_paye'] ?? 0);
$resteAPayer = max(0, $montantTotal - $montantPaye);
$pourcentagePaye = ($montantTotal > 0) ? min(100, round(($montantPaye / $montantTotal) * 100, 1)) : 0;

$statutReglement = strtolower($achat['statut_reglement'] ?? 'impaye');
$statutAchat = strtolower($achat['statut_achat'] ?? 'en_cours');
$statutReception = strtolower($achat['statut_reception'] ?? 'en_attente');
$statutVerification = strtolower($achat['statut_verification'] ?? 'non_verifie');
$verifiePar = htmlspecialchars(!empty($achat['verifier_nom_complet']) ? $achat['verifier_nom_complet'] : ($achat['verifie_par'] ?? ''));
$dateVerification = !empty($achat['date_verification']) ? date('d/m/Y H:i', strtotime($achat['date_verification'])) : '';
$validePar = htmlspecialchars(!empty($achat['validator_nom_complet']) ? $achat['validator_nom_complet'] : ($achat['valide_par'] ?? ''));
$dateValidation = !empty($achat['date_validation']) ? date('d/m/Y H:i', strtotime($achat['date_validation'])) : '';
$notesReception = htmlspecialchars($achat['notes_reception'] ?? '');

$totQteGlobal = 0;
foreach ($details as $dItem) {
    $totQteGlobal += floatval($dItem['quantite'] ?? 0);
}
?>

<style>
/* CSS d'optimisation pour impression officielle (A4 / Bon d'achat) */
@media print {
  .sidebar, .navbar, .page-header-actions, .btn, .modal, footer {
    display: none !important;
  }
  .main-content {
    margin-left: 0 !important;
    width: 100% !important;
    padding: 0 !important;
  }
  .content-wrapper {
    padding: 0 !important;
  }
  .card {
    border: none !important;
    box-shadow: none !important;
    padding: 10px 0 !important;
  }
  .print-header {
    display: flex !important;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #0F172A;
    padding-bottom: 15px;
    margin-bottom: 20px;
  }
  .print-signatures {
    display: flex !important;
    justify-content: space-between;
    margin-top: 50px;
    page-break-inside: avoid;
  }
}
.print-header, .print-signatures {
  display: none;
}
</style>

<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">
      
      <!-- EN-TÊTE OFFICIEL EN MODE IMPRESSION -->
      <div class="print-header">
        <div>
          <h2 style="margin:0; font-size: 20px; font-weight:900; color:#0F172A;">OLIVE SERVICE - MODULE AVICOLE</h2>
          <p style="margin:2px 0 0 0; font-size:12px; color:#475569;">BON D'ACHAT PRODUITS FINIS & INTRANTS</p>
        </div>
        <div style="text-align: right;">
          <h3 style="margin:0; font-size:16px; font-weight:800; color:#1E3A5F;">Ref: <?= $codeAchat ?></h3>
          <p style="margin:2px 0 0 0; font-size:12px; color:#475569;">Date : <?= $dateAchat ?></p>
        </div>
      </div>

      <!-- BREADCRUMB NAVIGATION -->
      <nav aria-label="breadcrumb" class="mb-3 page-header-actions">
        <ol class="breadcrumb mb-0" style="font-size: 13px; font-weight: 600;">
          <li class="breadcrumb-item"><a href="<?= RACINE ?>" style="color: #64748B; text-decoration: none;">Accueil</a></li>
          <li class="breadcrumb-item"><a href="<?= RACINE ?>aviculture/achats" style="color: #64748B; text-decoration: none;">Achats Avicoles</a></li>
          <li class="breadcrumb-item active" aria-current="page" style="color: #0F172A; font-weight: 700;">Bon N° <?= $codeAchat ?></li>
        </ol>
      </nav>

      <!-- EN-TÊTE PAGE & TOOLBAR -->
      <div class="page-header page-header-actions" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <h1 style="font-size: 22px; font-weight: 900; color: #0F172A; margin: 0;">Bon d'Achat : <?= $codeAchat ?></h1>
            
            <!-- Badges de Statut global -->
            <?php if ($statutAchat === 'solde' || $statutAchat === 'soldé'): ?>
              <span class="badge" style="background:#DCFCE7; color:#15803D; border:1px solid #BBF7D0; padding:6px 14px; border-radius:20px; font-weight:800; font-size:12px; display:inline-flex; align-items:center; gap:5px;">
                <i data-lucide="check-check" style="width: 14px; height: 14px;"></i> Soldé
              </span>
            <?php elseif ($statutAchat === 'valide' || $statutAchat === 'recu'): ?>
              <span class="badge" style="background:#E0F2FE; color:#0369A1; border:1px solid #BAE6FD; padding:6px 14px; border-radius:20px; font-weight:800; font-size:12px; display:inline-flex; align-items:center; gap:5px;">
                <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> Validé
              </span>
            <?php else: ?>
              <span class="badge" style="background:#FEF3C7; color:#92400E; border:1px solid #FDE68A; padding:6px 14px; border-radius:20px; font-weight:800; font-size:12px; display:inline-flex; align-items:center; gap:5px;">
                <i data-lucide="clock" style="width: 14px; height: 14px;"></i> En cours
              </span>
            <?php endif; ?>

            <?php if ($statutReglement === 'paye'): ?>
              <span class="badge" style="background:#DCFCE7; color:#15803D; border:1px solid #BBF7D0; padding:6px 14px; border-radius:20px; font-weight:800; font-size:12px; display:inline-flex; align-items:center; gap:5px;">
                <i data-lucide="credit-card" style="width: 14px; height: 14px;"></i> Payé (100%)
              </span>
            <?php elseif ($statutReglement === 'partiel'): ?>
              <span class="badge" style="background:#FEF3C7; color:#92400E; border:1px solid #FDE68A; padding:6px 14px; border-radius:20px; font-weight:800; font-size:12px; display:inline-flex; align-items:center; gap:5px;">
                <i data-lucide="pie-chart" style="width: 14px; height: 14px;"></i> Règlement Partiel (<?= $pourcentagePaye ?>%)
              </span>
            <?php else: ?>
              <span class="badge" style="background:#FEE2E2; color:#991B1B; border:1px solid #FECDD3; padding:6px 14px; border-radius:20px; font-weight:800; font-size:12px; display:inline-flex; align-items:center; gap:5px;">
                <i data-lucide="alert-circle" style="width: 14px; height: 14px;"></i> Non Réglé (0%)
              </span>
            <?php endif; ?>
          </div>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">
            Facture FRS N° : <code style="font-weight: 800; color: #1E3A5F; font-size: 13px; background: #F1F5F9; padding: 2px 8px; border-radius: 4px;"><?= $numFacture ?></code> &bull; 
            Enregistré le : <strong><?= $dateAchat ?></strong>
          </p>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
          <a href="<?= RACINE ?>aviculture/achats" class="btn btn-outline-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 16px; text-decoration: none; font-size: 13px;">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Retour aux Achats
          </a>
          
          <button onclick="window.print();" class="btn btn-outline-primary" style="border-color: #1E3A5F; color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 16px; font-size: 13px;">
            <i data-lucide="printer" style="width: 16px; height: 16px;"></i> Imprimer Bon / Facture
          </button>

          <?php if ($resteAPayer > 0.01): ?>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalReglerFactureDetail" style="background: #059669; border-color: #059669; display: inline-flex; align-items: center; gap: 8px; font-weight: 800; border-radius: 8px; padding: 10px 18px; font-size: 13px; color: white; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);">
              <i data-lucide="credit-card" style="width: 16px; height: 16px;"></i> Facture / Règlement
            </button>
          <?php else: ?>
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalReglerFactureDetail" style="font-weight: 700; border-radius: 8px; padding: 10px 16px; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
              <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i> Facture Réglée
            </button>
          <?php endif; ?>
        </div>
      </div>

      <!-- BANNIÈRE 3 KPI FINANCIERS + BARRE DE PROGRESSION -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 24px;">
        
        <!-- KPI 1 : Montant Total Facturé -->
        <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 2px 4px rgba(0,0,0,0.03);">
          <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
              <span style="font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Montant Total Facturé</span>
              <div style="font-size: 24px; font-weight: 900; color: #0F172A; margin-top: 4px;"><?= number_format($montantTotal, 0, ',', ' ') ?> FCFA</div>
            </div>
            <div style="background: #EFF6FF; color: #2563EB; width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <i data-lucide="file-spreadsheet" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          <div style="margin-top: 12px; font-size: 12px; color: #64748B; display: flex; align-items: center; gap: 6px;">
            <i data-lucide="package" style="width: 14px; height: 14px; color: #3B82F6;"></i> Volume Total : <strong><?= number_format($totQteGlobal, 2, ',', ' ') ?> unités</strong>
          </div>
        </div>

        <!-- KPI 2 : Montant Réglé & Barre de Progression -->
        <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 2px 4px rgba(0,0,0,0.03);">
          <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
              <span style="font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Cumul Réglé (Versé)</span>
              <div style="font-size: 24px; font-weight: 900; color: #059669; margin-top: 4px;"><?= number_format($montantPaye, 0, ',', ' ') ?> FCFA</div>
            </div>
            <div style="background: #ECFDF5; color: #059669; width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <i data-lucide="check-circle-2" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          
          <!-- Barre de progression du règlement -->
          <div style="margin-top: 10px;">
            <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 4px;">
              <span>Taux de Réglage</span>
              <span><?= $pourcentagePaye ?>%</span>
            </div>
            <div class="progress" style="height: 7px; border-radius: 4px; background: #E2E8F0; overflow: hidden;">
              <div class="progress-bar" role="progressbar" style="width: <?= $pourcentagePaye ?>%; background: #059669;" aria-valuenow="<?= $pourcentagePaye ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
        </div>

        <!-- KPI 3 : Reste à Payer -->
        <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 2px 4px rgba(0,0,0,0.03);">
          <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
              <span style="font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Reste à Payer</span>
              <div style="font-size: 24px; font-weight: 900; color: <?= $resteAPayer <= 0.01 ? '#059669' : '#DC2626' ?>; margin-top: 4px;">
                <?= number_format($resteAPayer, 0, ',', ' ') ?> FCFA
              </div>
            </div>
            <div style="background: <?= $resteAPayer <= 0.01 ? '#ECFDF5' : '#FEF2F2' ?>; color: <?= $resteAPayer <= 0.01 ? '#059669' : '#DC2626' ?>; width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <i data-lucide="<?= $resteAPayer <= 0.01 ? 'shield-check' : 'alert-triangle' ?>" style="width: 22px; height: 22px;"></i>
            </div>
          </div>
          <div style="margin-top: 12px; font-size: 12px; font-weight: 700; color: <?= $resteAPayer <= 0.01 ? '#047857' : '#B91C1C' ?>;">
            <?php if ($resteAPayer <= 0.01): ?>
              <i class="fa fa-check-circle me-1"></i> Facture Intégralement Réglée
            <?php else: ?>
              <i class="fa fa-clock me-1"></i> Solde à régler en trésorerie
            <?php endif; ?>
          </div>
        </div>

      </div>

      <!-- FICHE SIGNALÉTIQUE & TRAÇABILITÉ (2 CARTE LAYOUT) -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 24px;">
        
        <!-- CARTE GAUCHE : FOURNISSEUR AVICOLE -->
        <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 22px 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
          <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
            <i data-lucide="building" style="width: 18px; height: 18px; color: #0284C7;"></i> Coordonnées du Fournisseur Avicole
          </h3>

          <div style="display: flex; flex-direction: column; gap: 14px;">
            <div style="display: flex; gap: 12px; align-items: flex-start;">
              <div style="background: #F0F9FF; color: #0284C7; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="user" style="width: 18px; height: 18px;"></i>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Nom / Raison Sociale</span>
                <div style="font-size: 15px; font-weight: 800; color: #0F172A; margin-top: 2px;"><?= $fournisseurNom ?></div>
              </div>
            </div>

            <div style="display: flex; gap: 12px; align-items: flex-start;">
              <div style="background: #ECFDF5; color: #10B981; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="phone" style="width: 18px; height: 18px;"></i>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Téléphone / Contact</span>
                <div style="font-size: 14px; font-weight: 700; color: #334155; margin-top: 2px;"><?= $fournisseurTel ?></div>
              </div>
            </div>

            <div style="display: flex; gap: 12px; align-items: flex-start;">
              <div style="background: #F3E8FF; color: #8B5CF6; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="map-pin" style="width: 18px; height: 18px;"></i>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Adresse Géographique</span>
                <div style="font-size: 13px; font-weight: 600; color: #475569; margin-top: 2px;"><?= $fournisseurAdresse ?></div>
              </div>
            </div>
          </div>
        </div>

        <!-- CARTE DROITE : AUDIT & TRAÇABILITÉ SAKAN / SYSTEM -->
        <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 22px 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
          <h3 style="font-size: 14px; font-weight: 800; color: #1E3A5F; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
            <i data-lucide="shield-check" style="width: 18px; height: 18px; color: #7C3AED;"></i> Traçabilité & Audit Système
          </h3>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
            <div style="display: flex; gap: 10px; align-items: flex-start;">
              <div style="background: #FEF3C7; color: #D97706; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink:0;">
                <i data-lucide="qr-code" style="width: 18px; height: 18px;"></i>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Code Système Bon</span>
                <div style="font-size: 13px; font-weight: 800; color: #0F172A; font-family: monospace; margin-top: 2px;"><?= $codeAchat ?></div>
              </div>
            </div>

            <div style="display: flex; gap: 10px; align-items: flex-start;">
              <div style="background: #EFF6FF; color: #2563EB; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink:0;">
                <i data-lucide="file-text" style="width: 18px; height: 18px;"></i>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Facture FRS N°</span>
                <div style="font-size: 13px; font-weight: 800; color: #0F172A; margin-top: 2px;"><?= $numFacture ?></div>
              </div>
            </div>

            <div style="display: flex; gap: 10px; align-items: flex-start;">
              <div style="background: #ECFDF5; color: #059669; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink:0;">
                <i data-lucide="user-check" style="width: 18px; height: 18px;"></i>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Opérateur Saisie</span>
                <div style="font-size: 13px; font-weight: 800; color: #0F172A; margin-top: 2px;"><?= $agentNom ?></div>
              </div>
            </div>

            <div style="display: flex; gap: 10px; align-items: flex-start;">
              <div style="background: #F1F5F9; color: #475569; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink:0;">
                <i data-lucide="clock" style="width: 18px; height: 18px;"></i>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Horodatage Saisie</span>
                <div style="font-size: 13px; font-weight: 800; color: #0F172A; margin-top: 2px;"><?= $dateAchat ?></div>
              </div>
            </div>

            <div style="display: flex; gap: 10px; align-items: flex-start;">
              <div style="background: #F3E8FF; color: #7C3AED; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink:0;">
                <i data-lucide="clipboard-check" style="width: 18px; height: 18px;"></i>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Vérification (Contrôle)</span>
                <div style="font-size: 12px; font-weight: 800; color: #0F172A; margin-top: 2px;">
                  <?php if (!empty($verifiePar)): ?>
                    <?= $verifiePar ?> <?= !empty($dateVerification) ? '<small style="color:#64748B; display:block;">('.$dateVerification.')</small>' : '' ?>
                  <?php else: ?>
                    <span style="color:#94A3B8; font-weight:600;">Non vérifié</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <div style="display: flex; gap: 10px; align-items: flex-start;">
              <div style="background: #DCFCE7; color: #166534; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink:0;">
                <i data-lucide="award" style="width: 18px; height: 18px;"></i>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Validation (Chef)</span>
                <div style="font-size: 12px; font-weight: 800; color: #0F172A; margin-top: 2px;">
                  <?php if (!empty($validePar)): ?>
                    <?= $validePar ?> <?= !empty($dateValidation) ? '<small style="color:#64748B; display:block;">('.$dateValidation.')</small>' : '' ?>
                  <?php else: ?>
                    <span style="color:#94A3B8; font-weight:600;">En attente de validation</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <div style="display: flex; gap: 10px; align-items: flex-start;">
              <div style="background: #E0F2FE; color: #0369A1; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink:0;">
                <i data-lucide="truck" style="width: 18px; height: 18px;"></i>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Statut Réception</span>
                <div style="margin-top: 2px;">
                  <?php if ($statutReception === 'recue'): ?>
                    <span style="background: #DCFCE7; color: #166534; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 6px;">Reçue (100%)</span>
                  <?php elseif ($statutReception === 'partiellement_recue'): ?>
                    <span style="background: #FEF3C7; color: #92400E; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 6px;">Partiellement Reçue</span>
                  <?php elseif ($statutReception === 'refusee'): ?>
                    <span style="background: #FEE2E2; color: #991B1B; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 6px;">Refusée</span>
                  <?php else: ?>
                    <span style="background: #F1F5F9; color: #475569; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 6px;">En attente</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <div style="display: flex; gap: 10px; align-items: flex-start;">
              <div style="background: #F5F3FF; color: #6D28D9; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink:0;">
                <i data-lucide="check-square" style="width: 18px; height: 18px;"></i>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Statut Contrôle</span>
                <div style="margin-top: 2px;">
                  <?php if ($statutVerification === 'verifie'): ?>
                    <span style="background: #F3E8FF; color: #6B21A8; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 6px;">Vérifié &amp; Conforme</span>
                  <?php elseif ($statutVerification === 'partiellement_verifie'): ?>
                    <span style="background: #FEF3C7; color: #92400E; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 6px;">Partiel</span>
                  <?php elseif ($statutVerification === 'refuse'): ?>
                    <span style="background: #FEE2E2; color: #991B1B; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 6px;">Refusé</span>
                  <?php else: ?>
                    <span style="background: #F1F5F9; color: #475569; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 6px;">Non vérifié</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- CARTE : ARTICLES & PRODUITS COMMANDÉS -->
      <div class="card mb-4" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); width: 100%; box-sizing: border-box;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 2px solid #EFF6FF; flex-wrap: wrap; gap: 12px;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <div style="background: #E0F2FE; color: #0284C7; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
              <i data-lucide="shopping-bag" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
              <h3 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
                Articles & Produits Commandés
                <span style="background: #E0F2FE; color: #0369A1; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px;">
                  <?= count($details) ?> ligne(s)
                </span>
              </h3>
              <span style="font-size: 12px; color: #64748B;">Détail exhaustif des articles et grilles de poids associées</span>
            </div>
          </div>

          <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <!-- BOUTON 1 : VÉRIFIER BON D'ACHAT -->
            <?php if ($canVerifierAchat): ?>
              <?php if ($statutVerification === 'verifie' || $statutReception === 'recue'): ?>
                <span class="badge" style="background: #F3E8FF; color: #6B21A8; border: 1px solid #E9D5FF; padding: 8px 14px; border-radius: 8px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                  <i data-lucide="shield-check" style="width: 16px; height: 16px;"></i> Bon Vérifié <?= !empty($dateVerification) ? '('.$dateVerification.')' : '' ?>
                </span>
              <?php elseif ($statutVerification === 'partiellement_verifie' || $statutReception === 'partiellement_recue'): ?>
                <span class="badge" style="background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; padding: 8px 14px; border-radius: 8px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                  <i data-lucide="pie-chart" style="width: 16px; height: 16px;"></i> Réception Partielle
                </span>
              <?php elseif ($statutVerification === 'refuse' || $statutReception === 'refusee'): ?>
                <span class="badge" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FECDD3; padding: 8px 14px; border-radius: 8px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                  <i data-lucide="x-circle" style="width: 16px; height: 16px;"></i> Réception Refusée
                </span>
              <?php else: ?>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalVerifierAchat" style="border-color: #6366F1; color: #4F46E5; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 8px 14px; font-size: 13px;">
                  <i data-lucide="shield-check" style="width: 16px; height: 16px;"></i> Vérifier Bon d'Achat
                </button>
              <?php endif; ?>
            <?php endif; ?>

            <!-- BOUTON 2 : VALIDER BON D'ACHAT -->
            <?php if ($canValiderAchat): ?>
              <?php if ($statutAchat === 'valide' || $statutAchat === 'solde' || $statutAchat === 'soldé'): ?>
                <span class="badge" style="background: #DCFCE7; color: #15803D; border: 1px solid #BBF7D0; padding: 8px 14px; border-radius: 8px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                  <i data-lucide="check-circle-2" style="width: 16px; height: 16px;"></i> Bon Validé & Stock Enregistré
                </span>
              <?php else: ?>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalValiderAchat" style="background: #059669; border-color: #059669; color: white; display: inline-flex; align-items: center; gap: 8px; font-weight: 800; border-radius: 8px; padding: 8px 16px; font-size: 13px; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);">
                  <i data-lucide="check-circle-2" style="width: 16px; height: 16px;"></i> Valider Bon d'Achat
                </button>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>

        <?php if (empty($details)): ?>
          <div style="text-align: center; padding: 40px 20px; background: #F8FAFC; border-radius: 10px; border: 1px dashed #CBD5E1;">
            <i data-lucide="inbox" style="width: 36px; height: 36px; color: #94A3B8; margin-bottom: 8px;"></i>
            <p style="color: #64748B; font-weight: 600; margin: 0;">Aucun article n'a été répertorié sur cette commande.</p>
          </div>
        <?php else: ?>
          <div style="width: 100%; overflow-x: auto; border-radius: 10px; border: 1px solid #E2E8F0;">
            <table class="table align-middle" style="width: 100%; border-collapse: collapse; font-size: 13px; margin: 0;">
              <thead>
                <tr style="background: #0F172A; color: #FFFFFF;">
                  <th style="padding: 12px 14px; width: 50px; text-align: center; font-weight: 700; border: none;">#</th>
                  <th style="padding: 12px 14px; text-align: left; font-weight: 700; border: none;">Désignation Produit / Intrant</th>
                  <th style="padding: 12px 14px; text-align: center; font-weight: 700; border: none;">Quantité & Unité</th>
                  <th style="padding: 12px 14px; text-align: right; font-weight: 700; border: none;">Prix Unit. (FCFA)</th>
                  <th style="padding: 12px 14px; text-align: right; font-weight: 700; border: none;">Montant Sous-Total</th>
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
                  $bgRow = ($i % 2 === 0) ? '#F8FAFC' : '#FFFFFF';
                ?>
                  <tr style="background: <?= $bgRow ?>; border-bottom: 1px solid #E2E8F0; transition: background 0.2s ease;">
                    <td style="padding: 12px 14px; text-align: center; font-weight: 700; color: #64748B;">
                      <?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>
                    </td>
                    <td style="padding: 12px 14px; font-weight: 700; color: #0F172A;">
                      <div style="display: flex; flex-direction: column; gap: 4px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                          <div style="background: #F1F5F9; padding: 6px; border-radius: 6px; color: #0284C7; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="package" style="width: 15px; height: 15px;"></i>
                          </div>
                          <span style="font-size: 14px; font-weight: 800; color: #0F172A;"><?= htmlspecialchars($d['libelle_article_intrant'] ?? 'Article Avicole') ?></span>
                        </div>
                        
                        <!-- Badge Grille de Poids si applicable -->
                        <?php if (!empty($d['libelle_categorie_poids']) || !empty($d['categorie_poids_code'])): ?>
                          <div style="margin-left: 29px;">
                            <span style="display: inline-flex; align-items: center; gap: 5px; background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 6px;">
                              <i data-lucide="scale" style="width: 13px; height: 13px;"></i> Grille : <?= htmlspecialchars($d['libelle_categorie_poids'] ?? $d['categorie_poids_code']) ?>
                              <?php if (isset($d['poids_min']) && $d['poids_min'] !== null): ?>
                                (<?= number_format($d['poids_min'], 2, ',', ' ') ?> - <?= number_format($d['poids_max'], 2, ',', ' ') ?> kg)
                              <?php endif; ?>
                            </span>
                          </div>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td style="padding: 12px 14px; text-align: center;">
                      <span style="background: #EFF6FF; color: #1E3A5F; border: 1px solid #DBEAFE; padding: 6px 14px; border-radius: 8px; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                        <?= number_format($qte, 2, ',', ' ') ?>
                        <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;"><?= htmlspecialchars($d['unite_mesure'] ?? 'Unité') ?></span>
                      </span>
                      <?php if (isset($d['quantite_recue']) && $d['quantite_recue'] !== null && floatval($d['quantite_recue']) != floatval($qte)): ?>
                        <div style="margin-top: 4px;">
                          <span style="background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px;">
                            Reçu : <?= number_format(floatval($d['quantite_recue']), 2, ',', ' ') ?>
                          </span>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td style="padding: 12px 14px; text-align: right; font-weight: 700; color: #475569; font-size: 13px;">
                      <?= number_format($pu, 0, ',', ' ') ?> FCFA
                    </td>
                    <td style="padding: 12px 14px; text-align: right; font-weight: 900; color: #0F172A; font-size: 14px;">
                      <?= number_format($subTot, 0, ',', ' ') ?> FCFA
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr style="background: #F1F5F9; border-top: 2px solid #CBD5E1;">
                  <th colspan="2" style="padding: 14px; text-align: right; font-weight: 900; color: #0F172A; font-size: 13px;">
                    TOTAUX CUMULÉS COMMANDE :
                  </th>
                  <th style="padding: 14px; text-align: center;">
                    <span style="background: #0F172A; color: #FFFFFF; padding: 6px 14px; border-radius: 8px; font-weight: 800; font-size: 13px; display: inline-block;">
                      <?= number_format($totQte, 2, ',', ' ') ?> unités
                    </span>
                  </th>
                  <th style="padding: 14px;"></th>
                  <th style="padding: 14px; text-align: right;">
                    <span style="color: #DC2626; font-size: 17px; font-weight: 900;">
                      <?= number_format($totMontant, 0, ',', ' ') ?> FCFA
                    </span>
                  </th>
                </tr>
              </tfoot>
            </table>
          </div>
        <?php endif; ?>
      </div>

      <!-- CARTE : HISTORIQUE DES RÈGLEMENTS (GRAND LIVRE `reglements_avicoles`) -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); width: 100%; box-sizing: border-box;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #EFF6FF; flex-wrap: wrap; gap: 12px;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <div style="background: #ECFDF5; color: #059669; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
              <i data-lucide="history" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
              <h3 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
                Historique du Grand Livre des Règlements
                <span style="background: #ECFDF5; color: #047857; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px;">
                  <?= count($payments) ?> versement(s)
                </span>
              </h3>
              <span style="font-size: 12px; color: #64748B;">Traçabilité comptable intégrale des acomptes et règlements enregistrés</span>
            </div>
          </div>

          <?php if ($canReglerFacture && $resteAPayer > 0.01): ?>
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalReglerFactureDetail" style="background: #059669; border-color: #059669; font-weight: 700; border-radius: 6px; padding: 6px 14px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
              <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Ajouter un Règlement
            </button>
          <?php endif; ?>
        </div>

        <?php if (empty($payments)): ?>
          <div style="text-align: center; padding: 30px 20px; background: #F8FAFC; border-radius: 10px; border: 1px dashed #CBD5E1;">
            <i data-lucide="credit-card" style="width: 32px; height: 32px; color: #94A3B8; margin-bottom: 6px;"></i>
            <p style="color: #64748B; font-weight: 600; margin: 0;">Aucun versement n'a encore été enregistré dans le grand livre pour cette facture.</p>
          </div>
        <?php else: ?>
          <div style="width: 100%; overflow-x: auto; border-radius: 10px; border: 1px solid #E2E8F0;">
            <table class="table align-middle" style="width: 100%; border-collapse: collapse; font-size: 13px; margin: 0;">
              <thead>
                <tr style="background: #F8FAFC; color: #334155;">
                  <th style="padding: 10px 14px; text-align: left; font-weight: 700;">Code Règlement</th>
                  <th style="padding: 10px 14px; text-align: left; font-weight: 700;">Date & Heure</th>
                  <th style="padding: 10px 14px; text-align: center; font-weight: 700;">Mode Règlement</th>
                  <th style="padding: 10px 14px; text-align: left; font-weight: 700;">Référence / TransID</th>
                  <th style="padding: 10px 14px; text-align: left; font-weight: 700;">Opérateur Saisie</th>
                  <th style="padding: 10px 14px; text-align: right; font-weight: 700;">Montant Versé</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($payments as $p): 
                  $mVersed = floatval($p['montant_verse'] ?? 0);
                  $modeLbl = ($p['mode_reglement'] === 'mobile_money') ? 'Mobile Money (Wave/OM)' : (($p['mode_reglement'] === 'virement') ? 'Virement Bq' : (($p['mode_reglement'] === 'cheque') ? 'Chèque Bq' : 'Espèces'));
                  $dt = !empty($p['date_reglement']) ? date('d/m/Y H:i', strtotime($p['date_reglement'])) : '-';
                  $opNom = trim(($p['nom_user'] ?? '') . ' ' . ($p['prenom_user'] ?? ''));
                  if (empty($opNom)) $opNom = htmlspecialchars($p['user_code'] ?? 'Agent Trésorerie');
                ?>
                  <tr style="border-bottom: 1px solid #E2E8F0;">
                    <td style="padding: 10px 14px; font-weight: 800; color: #0F172A; font-family: monospace;">
                      <span style="background: #F1F5F9; padding: 3px 8px; border-radius: 4px; color: #1E3A5F; border: 1px solid #CBD5E1;">
                        <?= htmlspecialchars($p['code_reglement'] ?? '-') ?>
                      </span>
                    </td>
                    <td style="padding: 10px 14px; color: #64748B; font-weight: 600;"><?= $dt ?></td>
                    <td style="padding: 10px 14px; text-align: center;">
                      <span style="background: #ECFDF5; color: #047857; padding: 3px 10px; border-radius: 6px; font-weight: 700; font-size: 11px; border: 1px solid #A7F3D0;">
                        <?= $modeLbl ?>
                      </span>
                    </td>
                    <td style="padding: 10px 14px; color: #475569; font-weight: 600;">
                      <?= htmlspecialchars($p['reference_reglement'] ?? 'Non spécifiée') ?>
                    </td>
                    <td style="padding: 10px 14px; color: #334155; font-weight: 700;">
                      <?= htmlspecialchars($opNom) ?>
                    </td>
                    <td style="padding: 10px 14px; text-align: right; font-weight: 900; color: #059669; font-size: 14px;">
                      + <?= number_format($mVersed, 0, ',', ' ') ?> FCFA
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

      <!-- PIED DE PAGE POUR IMPRESSION OFFICIELLE -->
      <div class="print-signatures">
        <div style="text-align: center; width: 45%;">
          <p style="font-weight: 800; font-size: 13px; margin-bottom: 50px;">Signature & Cachet du Fournisseur</p>
          <p style="border-top: 1px dashed #64748B; width: 80%; margin: 0 auto;"></p>
        </div>
        <div style="text-align: center; width: 45%;">
          <p style="font-weight: 800; font-size: 13px; margin-bottom: 50px;">Signature & Cachet Responsable OLIVE</p>
          <p style="border-top: 1px dashed #64748B; width: 80%; margin: 0 auto;"></p>
        </div>
      </div>

    </div>
  </main>
</div>

<!-- MODAL VÉRIFIER BON D'ACHAT (PHASE DE CONTRÔLE MAGASINIER) -->
<div class="modal fade" id="modalVerifierAchat" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: #4F46E5; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="shield-check" style="width: 20px; height: 20px; color: #A5B4FC;"></i> Contrôle &amp; Vérification Bon d'Achat - N° <?= $codeAchat ?>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formVerifierAchat">
        <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
        <input type="hidden" name="code_achat" value="<?= $codeAchat ?>">
        
        <div class="modal-body" style="padding: 24px;">
          <div style="background: #EEF2FF; border: 1px solid #C7D2FE; border-radius: 8px; padding: 14px; margin-bottom: 20px; color: #3730A3; font-size: 13px;">
            <i class="fa fa-info-circle me-1"></i> <strong>Contrôle Réception Magasin :</strong> Vérifiez la conformité des articles livrés par le fournisseur par rapport aux lignes de commande ci-dessous.
          </div>

          <!-- DÉCISION DU CONTRÔLEUR -->
          <div class="mb-4">
            <label style="font-weight: 800; font-size: 13px; color: #1E293B; margin-bottom: 8px; display: block;">Décision de Réception *</label>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
              <label style="border: 2px solid #CBD5E1; border-radius: 10px; padding: 12px; cursor: pointer; text-align: center; background: #F8FAFC;">
                <input type="radio" name="decision" value="recue" checked style="margin-right: 6px;" onchange="toggleQteInputs(false)">
                <span style="font-weight: 800; color: #059669; font-size: 13px;">Totalement Conforme (100%)</span>
              </label>
              <label style="border: 2px solid #CBD5E1; border-radius: 10px; padding: 12px; cursor: pointer; text-align: center; background: #F8FAFC;">
                <input type="radio" name="decision" value="partiellement_recue" style="margin-right: 6px;" onchange="toggleQteInputs(true)">
                <span style="font-weight: 800; color: #D97706; font-size: 13px;">Réception Partielle</span>
              </label>
              <label style="border: 2px solid #CBD5E1; border-radius: 10px; padding: 12px; cursor: pointer; text-align: center; background: #F8FAFC;">
                <input type="radio" name="decision" value="refusee" style="margin-right: 6px;" onchange="toggleQteInputs(false)">
                <span style="font-weight: 800; color: #DC2626; font-size: 13px;">Non Conforme / Refusé</span>
              </label>
            </div>
          </div>

          <!-- TABLEAU DES QUANTITÉS REÇUES -->
          <div class="table-responsive mb-3" style="border: 1px solid #E2E8F0; border-radius: 8px;">
            <table class="table align-middle" style="margin: 0; font-size: 13px;">
              <thead style="background: #F1F5F9; color: #334155; font-weight: 700;">
                <tr>
                  <th style="padding: 10px 12px;">Article / Intrant</th>
                  <th style="padding: 10px 12px; text-align: center;">Qté Commandée</th>
                  <th style="padding: 10px 12px; text-align: center;">Qté Réellement Reçue</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($details as $dItem): ?>
                  <tr>
                    <td style="padding: 10px 12px; font-weight: 700; color: #0F172A;">
                      <?= htmlspecialchars($dItem['libelle_article_intrant'] ?? 'Article') ?>
                      <?php if (!empty($dItem['libelle_categorie_poids'])): ?>
                        <small style="color: #64748B; display: block;">(Grille : <?= htmlspecialchars($dItem['libelle_categorie_poids']) ?>)</small>
                      <?php endif; ?>
                    </td>
                    <td style="padding: 10px 12px; text-align: center; font-weight: 800;">
                      <?= number_format($dItem['quantite'], 2, ',', ' ') ?> <?= htmlspecialchars($dItem['unite_mesure']) ?>
                    </td>
                    <td style="padding: 10px 12px; text-align: center;">
                      <input type="number" step="any" min="0" max="<?= $dItem['quantite'] ?>" name="quantites_recues[<?= $dItem['id_detail_achat'] ?>]" value="<?= floatval($dItem['quantite_recue'] ?? $dItem['quantite']) ?>" class="form-control form-control-sm input-qte-recue" disabled style="width: 110px; margin: 0 auto; text-align: center; font-weight: 800;">
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <!-- REMARQUES / OBSERVATIONS -->
          <div>
            <label style="font-weight: 700; font-size: 12px; color: #334155; margin-bottom: 4px; display: block;">Notes &amp; Observations du Contrôleur</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Mentionnez d'éventuelles réserves, sacs détériorés ou écarts constatés..." style="font-size: 13px;"><?= $notesReception ?></textarea>
          </div>
        </div>

        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-weight: 600; border-radius: 8px;">Annuler</button>
          <button type="submit" class="btn btn-primary" style="background: #4F46E5; border-color: #4F46E5; font-weight: 800; border-radius: 8px; padding: 8px 20px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
            <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i> Enregistrer le Contrôle
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL VALIDER BON D'ACHAT (PHASE D'APPROBATION FINALE CHEF D'EXPLOITATION) -->
<div class="modal fade" id="modalValiderAchat" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: #059669; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="check-circle-2" style="width: 20px; height: 20px; color: #A7F3D0;"></i> Validation Finale Bon d'Achat - N° <?= $codeAchat ?>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formValiderAchat">
        <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
        <input type="hidden" name="code_achat" value="<?= $codeAchat ?>">
        
        <div class="modal-body" style="padding: 24px; text-align: center;">
          <div style="background: #ECFDF5; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; color: #059669;">
            <i data-lucide="package-check" style="width: 34px; height: 34px;"></i>
          </div>
          <h4 style="font-size: 17px; font-weight: 800; color: #0F172A; margin: 0 0 8px 0;">Confirmer la Validation Officielle</h4>
          <p style="color: #475569; font-size: 13px; margin: 0 0 20px 0; line-height: 1.5;">
            Cette action approuve formellement le bon d'achat, intègre automatiquement les <strong><?= count($details) ?> article(s) en stock avicole</strong> et débloque l'autorisation de règlement en trésorerie.
          </p>
        </div>

        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-weight: 600; border-radius: 8px;">Annuler</button>
          <button type="submit" class="btn btn-success" style="background: #059669; border-color: #059669; font-weight: 800; border-radius: 8px; padding: 8px 20px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
            <i data-lucide="check-check" style="width: 16px; height: 16px;"></i> Valider &amp; Entrer en Stock
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL FACTURE & RÈGLEMENT -->
<div class="modal fade" id="modalReglerFactureDetail" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: #1E3A5F; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="file-text" style="width: 20px; height: 20px; color: #6EE7B7;"></i> Règlement Facture Achat - Bon N° <?= $codeAchat ?>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="padding: 24px;">
        
        <!-- BLOC RÉCAPITULATIF FINANCIER -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px;">
          <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 12px; border-radius: 8px; text-align: center;">
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Total Facture</span>
            <div style="font-size: 16px; font-weight: 800; color: #0F172A; margin-top: 2px;"><?= number_format($montantTotal, 0, ',', ' ') ?> FCFA</div>
          </div>
          <div style="background: #ECFDF5; border: 1px solid #A7F3D0; padding: 12px; border-radius: 8px; text-align: center;">
            <span style="font-size: 11px; font-weight: 700; color: #047857; text-transform: uppercase;">Total Versé</span>
            <div style="font-size: 16px; font-weight: 800; color: #059669; margin-top: 2px;"><?= number_format($montantPaye, 0, ',', ' ') ?> FCFA</div>
          </div>
          <div style="background: <?= $resteAPayer <= 0.01 ? '#EFF6FF' : '#FEF2F2' ?>; border: 1px solid <?= $resteAPayer <= 0.01 ? '#BFDBFE' : '#FECDD3' ?>; padding: 12px; border-radius: 8px; text-align: center;">
            <span style="font-size: 11px; font-weight: 700; color: <?= $resteAPayer <= 0.01 ? '#1E40AF' : '#991B1B' ?>; text-transform: uppercase;">Reste à Payer</span>
            <div style="font-size: 16px; font-weight: 900; color: <?= $resteAPayer <= 0.01 ? '#1E3A5F' : '#DC2626' ?>; margin-top: 2px;"><?= number_format($resteAPayer, 0, ',', ' ') ?> FCFA</div>
          </div>
        </div>

        <!-- BLOC FORMULAIRE OU STATUT -->
        <?php if ($resteAPayer <= 0.01): ?>
          <div style="background: #ECFDF5; border: 1px solid #6EE7B7; color: #065F46; border-radius: 8px; padding: 16px; font-weight: 700; text-align: center;">
            <i class="fa fa-check-circle me-1"></i> Facture Intégralement Réglée
          </div>
        <?php elseif (!$canReglerFacture): ?>
          <div style="background: #F8FAFC; border: 1px solid #CBD5E1; color: #475569; border-radius: 8px; padding: 16px; font-weight: 600; text-align: center;">
            <i class="fa fa-lock me-1"></i> Facture non soldée. Seuls les profils Comptabilité & Administration disposent des privilèges pour effectuer un règlement.
          </div>
        <?php else: ?>
          <form id="formReglerFactureDetail">
            <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
            <input type="hidden" name="code_achat" value="<?= $codeAchat ?>">
            
            <div class="row g-3 align-items-end">
              <div class="col-md-4">
                <label style="font-weight: 700; font-size: 12px; color: #334155; margin-bottom: 4px; display: block;">Montant Versé (FCFA) *</label>
                <div class="input-group">
                  <span class="input-group-text" style="background: #E2E8F0; font-weight: 700; color: #334155; font-size: 12px;">FCFA</span>
                  <input type="number" step="any" min="1" max="<?= $resteAPayer ?>" name="montant_verse" class="form-control" value="<?= $resteAPayer ?>" required style="font-weight: 800; color: #0F172A; font-size: 14px;">
                </div>
              </div>
              
              <div class="col-md-4">
                <label style="font-weight: 700; font-size: 12px; color: #334155; margin-bottom: 4px; display: block;">Mode de Règlement *</label>
                <select name="mode_reglement" class="form-select" style="font-weight: 600; font-size: 13px;">
                  <option value="especes">Espèces</option>
                  <option value="mobile_money">Mobile Money (Wave / OM)</option>
                  <option value="virement">Virement Bq</option>
                  <option value="cheque">Chèque Bq</option>
                </select>
              </div>

              <div class="col-md-4">
                <label style="font-weight: 700; font-size: 12px; color: #334155; margin-bottom: 4px; display: block;">N° Référence / TransID</label>
                <input type="text" name="reference_reglement" class="form-control" placeholder="Ex: WAVE-998822" style="font-size: 13px;">
              </div>
            </div>

            <div style="margin-top: 16px; text-align: right;">
              <button type="submit" class="btn btn-success" style="background: #059669; border-color: #059669; font-weight: 800; border-radius: 8px; padding: 8px 20px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i> Valider le Règlement
              </button>
            </div>
          </form>
        <?php endif; ?>

      </div>
      <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-weight: 600; border-radius: 8px;">Fermer</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    if (window.lucide) lucide.createIcons();

    // Configuration globale Toastr
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "4000"
        };
    }

    function notifyMsg(message, type = 'success') {
        if (typeof toastr !== 'undefined' && toastr[type]) {
            toastr[type](message);
        } else if (typeof showToast === 'function') {
            showToast(message, type);
        } else {
            alert(message);
        }
    }

    // Affichage des messages flash enregistrés avant un rechargement de page
    try {
        const flashMsg = sessionStorage.getItem('flash_toast_msg');
        const flashType = sessionStorage.getItem('flash_toast_type') || 'success';
        if (flashMsg) {
            notifyMsg(flashMsg, flashType);
            sessionStorage.removeItem('flash_toast_msg');
            sessionStorage.removeItem('flash_toast_type');
        }
    } catch(e) {}

    window.toggleQteInputs = function(enable) {
        $('.input-qte-recue').prop('disabled', !enable);
    };

    // Soumission du formulaire de vérification du bon d'achat
    $('#formVerifierAchat').on('submit', function(e) {
        e.preventDefault();
        const baseApi = (typeof RACINE !== 'undefined') ? RACINE : '/ovolias/';
        const formData = $(this).serialize();
        const $btn = $(this).find('button[type="submit"]');

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Traitement...');

        $.ajax({
            url: baseApi + 'aviculture/verifierAchat',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                if (res.status === 1 || res.status === 'success') {
                    const msg = res.message || 'Contrôle enregistré avec succès !';
                    try {
                        sessionStorage.setItem('flash_toast_msg', msg);
                        sessionStorage.setItem('flash_toast_type', 'success');
                    } catch(e) {}

                    notifyMsg(msg, 'success');
                    $('#modalVerifierAchat').modal('hide');

                    setTimeout(function() {
                        location.reload();
                    }, 1200);
                } else {
                    notifyMsg(res.message || 'Erreur lors de la vérification', 'error');
                    $btn.prop('disabled', false).html('<i data-lucide="check-circle" style="width: 16px; height: 16px;"></i> Enregistrer le Contrôle');
                    if (window.lucide) lucide.createIcons();
                }
            },
            error: function(xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Une erreur système est survenue.';
                notifyMsg(msg, 'error');
                $btn.prop('disabled', false).html('<i data-lucide="check-circle" style="width: 16px; height: 16px;"></i> Enregistrer le Contrôle');
                if (window.lucide) lucide.createIcons();
            }
        });
    });

    // Soumission du formulaire de validation finale du bon d'achat
    $('#formValiderAchat').on('submit', function(e) {
        e.preventDefault();
        const baseApi = (typeof RACINE !== 'undefined') ? RACINE : '/ovolias/';
        const formData = $(this).serialize();
        const $btn = $(this).find('button[type="submit"]');

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Validation...');

        $.ajax({
            url: baseApi + 'aviculture/validerAchat',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                if (res.status === 1 || res.status === 'success') {
                    const msg = res.message || 'Bon d\'achat validé avec succès !';
                    try {
                        sessionStorage.setItem('flash_toast_msg', msg);
                        sessionStorage.setItem('flash_toast_type', 'success');
                    } catch(e) {}

                    notifyMsg(msg, 'success');
                    $('#modalValiderAchat').modal('hide');

                    setTimeout(function() {
                        location.reload();
                    }, 1200);
                } else {
                    notifyMsg(res.message || 'Erreur lors de la validation', 'error');
                    $btn.prop('disabled', false).html('<i data-lucide="check-check" style="width: 16px; height: 16px;"></i> Valider & Entrer en Stock');
                    if (window.lucide) lucide.createIcons();
                }
            },
            error: function(xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Une erreur système est survenue.';
                notifyMsg(msg, 'error');
                $btn.prop('disabled', false).html('<i data-lucide="check-check" style="width: 16px; height: 16px;"></i> Valider & Entrer en Stock');
                if (window.lucide) lucide.createIcons();
            }
        });
    });

    // Soumission du formulaire de règlement depuis la page de détails
    $('#formReglerFactureDetail').on('submit', function(e) {
        e.preventDefault();
        const baseApi = (typeof RACINE !== 'undefined') ? RACINE : '/ovolias/';
        const formData = $(this).serialize();
        const $btn = $(this).find('button[type="submit"]');

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Traitement...');

        $.ajax({
            url: baseApi + 'aviculture/reglerAchat',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                if (res.status === 1 || res.status === 'success') {
                    const msg = res.message || 'Règlement enregistré avec succès !';
                    try {
                        sessionStorage.setItem('flash_toast_msg', msg);
                        sessionStorage.setItem('flash_toast_type', 'success');
                    } catch(e) {}

                    notifyMsg(msg, 'success');
                    $('#modalReglerFactureDetail').modal('hide');

                    setTimeout(function() {
                        location.reload();
                    }, 1200);
                } else {
                    notifyMsg(res.message || 'Erreur lors du règlement', 'error');
                    $btn.prop('disabled', false).html('<i data-lucide="check-circle" style="width: 16px; height: 16px;"></i> Valider le Règlement');
                    if (window.lucide) lucide.createIcons();
                }
            },
            error: function(xhr) {
                let msg = 'Une erreur système est survenue.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                notifyMsg(msg, 'error');
                $btn.prop('disabled', false).html('<i data-lucide="check-circle" style="width: 16px; height: 16px;"></i> Valider le Règlement');
                if (window.lucide) lucide.createIcons();
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
