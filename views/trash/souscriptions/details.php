<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$item = $item ?? [];
$client = $client ?? [];
$packSouscrit = $packSouscrit ?? [];
$cotisations = $cotisations ?? [];

$totalCotise = (float)($item['montant_total_cotise'] ?? 0);
$totalPrevu = (float)($item['montant_total_prevu'] ?? 0);
$tauxProgression = ($totalPrevu > 0) ? min(100, round(($totalCotise / $totalPrevu) * 100)) : 0;
$soldeRestant = max(0, $totalPrevu - $totalCotise);
$nomClient = trim($client['nom_client'] ?? '');
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">
      
      <!-- EN-TÊTE DE LA FICHE SOUSCRIPTION -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Contrat Souscription : <code style="font-weight: 800; color: #1E3A5F; font-size: 20px; background: #EFF6FF; padding: 3px 10px; border-radius: 6px;"><?= htmlspecialchars($item['code_souscription'] ?? '-') ?></code></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Souscrit par <strong><?= htmlspecialchars($nomClient) ?></strong> &bull; Pack : <strong><?= htmlspecialchars($packSouscrit['libelle_pack'] ?? '-') ?></strong></p>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="<?= RACINE ?>souscription/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour à la liste
          </a>
          <a href="<?= RACINE ?>souscription/edition/<?= $encryptedId ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
            <i data-lucide="edit" style="width: 18px; height: 18px;"></i> Modifier Contrat
          </a>
        </div>
      </div>

      <!-- CARTE 1 : SYNTHÈSE DU CONTRAT -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
          <i data-lucide="file-text" style="width: 18px; height: 18px;"></i> Caractéristiques du Contrat de Souscription
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Client Souscripteur</span>
            <div style="font-size: 17px; font-weight: 800; color: #0F172A; margin-top: 4px;"><?= htmlspecialchars($nomClient) ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Tél : <?= htmlspecialchars($client['telephone_client'] ?? '-') ?></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Pack Souscrit</span>
            <div style="font-size: 17px; font-weight: 800; color: #1E3A5F; margin-top: 4px;"><?= htmlspecialchars($packSouscrit['libelle_pack'] ?? '-') ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Cotisation : <?= number_format((float)($item['montant_cotisation_journaliere'] ?? 0), 0, ',', ' ') ?> FCFA / j</div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Progression Jours</span>
            <div style="font-size: 17px; font-weight: 800; color: #0F172A; margin-top: 4px;">
              <?= (int)($item['nombre_jour_cotise'] ?? 0) ?> / <?= (int)($item['nombre_jour_total'] ?? 0) ?> jours
            </div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Début : <?= !empty($item['date_debut_souscription']) ? date('d/m/Y', strtotime($item['date_debut_souscription'])) : '-' ?></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Statut Contrat</span>
            <div style="margin-top: 6px;">
              <?php if (($item['statut_souscription'] ?? '') === 'solde'): ?>
                <span class="badge" style="background:#DCFCE7; color:#15803D; padding:6px 14px; border-radius:10px; font-weight:800; font-size:12px;">Soldée</span>
              <?php else: ?>
                <span class="badge" style="background:#E0F2FE; color:#0369A1; padding:6px 14px; border-radius:10px; font-weight:800; font-size:12px;">En cours</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- CARTE 2 : BILAN FINANCIER & TABLEAU DES COTISATIONS TERRAIN -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 2px solid #EFF6FF;">
          <div>
            <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
              <i data-lucide="coins" style="width: 18px; height: 18px; color: #047857;"></i> Cotisations Terrain Encaissées
            </h3>
          </div>
          <a href="<?= RACINE ?>cotisation/formulaire" class="btn btn-sm btn-primary" style="background: #059669; border-color: #059669; font-weight: 700; border-radius: 6px; font-size: 12px; text-decoration: none;">
            + Encaisser une Cotisation
          </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px;">
          <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 10px; padding: 14px;">
            <span style="font-size: 11px; font-weight: 700; color: #1E3A5F; text-transform: uppercase;">Montant Prévu</span>
            <div style="font-size: 20px; font-weight: 800; color: #1E3A5F; margin-top: 4px;"><?= number_format($totalPrevu, 0, ',', ' ') ?> FCFA</div>
          </div>

          <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px; padding: 14px;">
            <span style="font-size: 11px; font-weight: 700; color: #15803D; text-transform: uppercase;">Total Cotisé (<?= $tauxProgression ?>%)</span>
            <div style="font-size: 20px; font-weight: 800; color: #15803D; margin-top: 4px;"><?= number_format($totalCotise, 0, ',', ' ') ?> FCFA</div>
          </div>

          <div style="background: <?= $soldeRestant > 0 ? '#FEF2F2' : '#F8FAFC' ?>; border: 1px solid <?= $soldeRestant > 0 ? '#FECACA' : '#E2E8F0' ?>; border-radius: 10px; padding: 14px;">
            <span style="font-size: 11px; font-weight: 700; color: <?= $soldeRestant > 0 ? '#DC2626' : '#64748B' ?>; text-transform: uppercase;">Reste à Cotiser</span>
            <div style="font-size: 20px; font-weight: 800; color: <?= $soldeRestant > 0 ? '#DC2626' : '#15803D' ?>; margin-top: 4px;">
              <?= $soldeRestant > 0 ? number_format($soldeRestant, 0, ',', ' ') . ' FCFA' : 'Contrat Soldé' ?>
            </div>
          </div>
        </div>

        <?php if (empty($cotisations)): ?>
          <p style="color: #94A3B8; text-align: center; padding: 20px 0; font-style: italic;">Aucune cotisation enregistrée sur cette souscription pour le moment.</p>
        <?php else: ?>
          <div style="width: 100%; overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
              <thead>
                <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                  <th style="padding: 10px 12px;">Réf. Cotisation</th>
                  <th style="padding: 10px 12px;">Date</th>
                  <th style="padding: 10px 12px;">Commercial</th>
                  <th style="padding: 10px 12px; text-align: center;">Jours Régularisés</th>
                  <th style="padding: 10px 12px; text-align: right;">Montant Encaissé</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($cotisations as $c): ?>
                  <tr style="border-bottom: 1px solid #F1F5F9;">
                    <td style="padding: 10px 12px; font-family: monospace; font-weight: 700; color: #1E3A5F;">
                      <?= htmlspecialchars($c['code_cautisation_client'] ?? '-') ?>
                    </td>
                    <td style="padding: 10px 12px; color: #334155;"><?= !empty($c['date_cautisation']) ? date('d/m/Y', strtotime($c['date_cautisation'])) : '-' ?></td>
                    <td style="padding: 10px 12px; color: #334155; font-weight: 600;"><?= htmlspecialchars(trim(($c['nom_user'] ?? '') . ' ' . ($c['prenom_user'] ?? ''))) ?></td>
                    <td style="padding: 10px 12px; text-align: center;"><span class="badge" style="background:#EFF6FF; color:#1E3A5F; padding:4px 8px; border-radius:6px; font-weight:700;">+<?= (int)($c['nombre_jour_paye'] ?? 1) ?> j</span></td>
                    <td style="padding: 10px 12px; text-align: right; font-weight: 800; color: #047857;"><?= number_format((float)($c['montant_cautisation_client'] ?? ($c['montant_cautisation'] ?? 0)), 0, ',', ' ') ?> FCFA</td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>
</div>
<script>$(document).ready(function() { if (window.lucide) lucide.createIcons(); });</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
