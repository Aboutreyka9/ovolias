<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$item = $item ?? [];
$annee = $annee ?? [];
$packs = $packs ?? [];
$souscriptions = $souscriptions ?? [];
$stats = $stats ?? [];
$libelleSession = $item['libelle_session'] ?? 'Session d\'Activité';
$totalCotise = (float)($stats['total_cotise'] ?? 0);
$totalPrevu = (float)($stats['total_prevu'] ?? 0);
$tauxProg = ($totalPrevu > 0) ? min(100, round(($totalCotise / $totalPrevu) * 100)) : 0;
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">
      
      <!-- EN-TÊTE DE LA FICHE SESSION -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Fiche Session : <?= htmlspecialchars($libelleSession) ?></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Code Session : <code style="font-weight: 800; color: #1E3A5F; font-size: 13px; background: #EFF6FF; padding: 2px 8px; border-radius: 4px;"><?= htmlspecialchars($item['code_session'] ?? '-') ?></code> &bull; Année Académique : <strong><?= htmlspecialchars($annee['libelle_annee'] ?? '-') ?></strong></p>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="<?= RACINE ?>session/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour aux sessions
          </a>
          <a href="<?= RACINE ?>session/edition/<?= $encryptedId ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
            <i data-lucide="edit" style="width: 18px; height: 18px;"></i> Modifier la Session
          </a>
        </div>
      </div>

      <!-- CARTE 1 : PARAMÈTRES ET SYNTHÈSE DE SESSION -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
          <i data-lucide="clock" style="width: 18px; height: 18px;"></i> Caractéristiques & Synthèse de la Session
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Durée Totale Prévue</span>
            <div style="font-size: 20px; font-weight: 800; color: #0F172A; margin-top: 4px;"><?= (int)($item['nombre_jour_session'] ?? 170) ?> Jours</div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Du <?= !empty($item['date_debut_session']) ? date('d/m/Y', strtotime($item['date_debut_session'])) : '-' ?> au <?= !empty($item['date_fin_session']) ? date('d/m/Y', strtotime($item['date_fin_session'])) : '-' ?></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Souscriptions Souscrites</span>
            <div style="font-size: 20px; font-weight: 800; color: #1E3A5F; margin-top: 4px;"><?= (int)($stats['total_souscriptions'] ?? 0) ?> Contrats</div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Packs rattachés : <?= count($packs) ?></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Total Cotisé à ce jour</span>
            <div style="font-size: 20px; font-weight: 800; color: #047857; margin-top: 4px;"><?= number_format($totalCotise, 0, ',', ' ') ?> FCFA</div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Taux de collecte : <strong style="color: #047857;"><?= $tauxProg ?>%</strong></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Statut d'Ouverture</span>
            <div style="margin-top: 6px;">
              <?php if (($item['statut_session'] ?? '') === 'actif'): ?>
                <span class="badge" style="background:#DCFCE7; color:#15803D; padding:6px 14px; border-radius:10px; font-weight:800; font-size:12px;">Session Active</span>
              <?php else: ?>
                <span class="badge" style="background:#FEF2F2; color:#B91C1C; padding:6px 14px; border-radius:10px; font-weight:800; font-size:12px;">Session Fermée</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- CARTE 2 : PACKS DISPONIBLES DANS CETTE SESSION -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 12px;">
          <i data-lucide="boxes" style="width: 18px; height: 18px; color: #1E3A5F;"></i> Offerings / Packs Associés à cette Session
        </h3>

        <?php if (empty($packs)): ?>
          <p style="color: #94A3B8; text-align: center; padding: 20px 0; font-style: italic;">Aucun pack directement configuré pour cette session pour le moment.</p>
        <?php else: ?>
          <div style="width: 100%; overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
              <thead>
                <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                  <th style="padding: 10px 12px;">Libellé Pack</th>
                  <th style="padding: 10px 12px; text-align: right;">Cotisation / jour</th>
                  <th style="padding: 10px 12px; text-align: center;">Jours Totaux</th>
                  <th style="padding: 10px 12px; text-align: right;">Objectif Total</th>
                  <th style="padding: 10px 12px; text-align: center;">Statut</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($packs as $p): ?>
                  <?php 
                    $cotis = (float)($p['prix_cotisation_pack'] ?? 0);
                    $jrs = (int)($p['nombre_jour_pack'] ?? 0);
                    $obj = $cotis * $jrs;
                  ?>
                  <tr style="border-bottom: 1px solid #F1F5F9;">
                    <td style="padding: 10px 12px; font-weight: 700; color: #0F172A;"><?= htmlspecialchars($p['libelle_pack']) ?></td>
                    <td style="padding: 10px 12px; text-align: right; color: #047857; font-weight: 700;"><?= number_format($cotis, 0, ',', ' ') ?> FCFA</td>
                    <td style="padding: 10px 12px; text-align: center;"><span class="badge" style="background:#EFF6FF; color:#1E3A5F; padding:4px 8px; border-radius:6px; font-weight:700;"><?= $jrs ?> jours</span></td>
                    <td style="padding: 10px 12px; text-align: right; font-weight: 800; color: #1E3A5F;"><?= number_format($obj, 0, ',', ' ') ?> FCFA</td>
                    <td style="padding: 10px 12px; text-align: center;">
                      <span class="badge" style="background:<?= ($p['statut_pack'] ?? '') === 'actif' ? '#DCFCE7' : '#F1F5F9' ?>; color:<?= ($p['statut_pack'] ?? '') === 'actif' ? '#15803D' : '#64748B' ?>; padding:4px 8px; border-radius:6px; font-weight:700;">
                        <?= ucfirst($p['statut_pack'] ?? 'actif') ?>
                      </span>
                    </td>
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
