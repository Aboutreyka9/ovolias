<?php
require_once __DIR__ . '/../../public/inc/header.php';
$item = isset($item) ? $item : [];
$depenses = isset($depenses) ? $depenses : [];
$totalDepenses = $totalDepenses ?? 0;
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">
      
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Fiche Ligne / Catégorie de Dépense</h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Catégorie budgétaire : <strong><?= htmlspecialchars($item['libelle_type_depense'] ?? '-') ?></strong></p>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="<?= RACINE ?>type_depense/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour aux catégories
          </a>
          <a href="<?= RACINE ?>type_depense/edition/<?= $encryptedId ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="edit" style="width: 18px; height: 18px;"></i> Modifier la catégorie
          </a>
        </div>
      </div>

      <!-- CARD 1 (COL-12) : DÉTAILS DE LA CATÉGORIE -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
          <i data-lucide="tag" style="width: 18px; height: 18px;"></i> Spécifications du Type de Charge
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
          <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 16px;">
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Intitulé du Poste</span>
            <div style="font-size: 18px; font-weight: 800; color: #0F172A; margin-top: 4px;"><?= htmlspecialchars($item['libelle_type_depense'] ?? '-') ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Code : <code><?= htmlspecialchars($item['code_type_depense'] ?? '-') ?></code></div>
          </div>

          <div style="background: #FEF2F2; border: 1px solid #FECACA; border-radius: 10px; padding: 16px;">
            <span style="font-size: 11px; font-weight: 700; color: #991B1B; text-transform: uppercase;">Cumul Engagé</span>
            <div style="font-size: 22px; font-weight: 800; color: #DC2626; margin-top: 4px;"><?= number_format($totalDepenses, 0, ',', ' ') ?> FCFA</div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;"><?= count($depenses) ?> dépense(s) validée(s)</div>
          </div>

          <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px; padding: 16px;">
            <span style="font-size: 11px; font-weight: 700; color: #15803D; text-transform: uppercase;">Statut</span>
            <div style="margin-top: 6px;">
              <?php if (($item['statut_type_depense'] ?? '') === 'actif'): ?>
                <span class="badge" style="background:#DCFCE7; color:#15803D; padding:4px 12px; border-radius:10px; font-weight:700; font-size:12px;">Actif</span>
              <?php else: ?>
                <span class="badge" style="background:#FEE2E2; color:#B91C1C; padding:4px 12px; border-radius:10px; font-weight:700; font-size:12px;">Inactif</span>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <?php if (!empty($item['description_type_depense'])): ?>
          <div style="padding-top: 16px; border-top: 1px solid #F1F5F9; margin-top: 16px; font-size: 13px; color: #64748B;">
            <strong>Description / Affectation :</strong> <?= htmlspecialchars($item['description_type_depense']) ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- CARD 2 (COL-12) : HISTORIQUE DES DÉPENSES LIÉES -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
          <i data-lucide="receipt" style="width: 18px; height: 18px; color: #1E3A5F;"></i> Dépenses Enregistrées sur cette Ligne (<?= count($depenses) ?>)
        </h3>

        <?php if (empty($depenses)): ?>
          <p style="color: #94A3B8; text-align: center; padding: 30px 0; font-style: italic;">Aucune dépense imputée à ce jour sur cette catégorie.</p>
        <?php else: ?>
          <div style="width: 100%; overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
              <thead>
                <tr style="background: #F8FAFC; text-align: left; color: #64748B; font-size: 12px;">
                  <th style="padding: 10px;">Réf Dépense</th>
                  <th style="padding: 10px;">Date</th>
                  <th style="padding: 10px;">Bénéficiaire / Motif</th>
                  <th style="padding: 10px; text-align: right;">Montant Décaissement</th>
                  <th style="padding: 10px; text-align: center;">Statut</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($depenses as $d): ?>
                  <tr style="border-bottom: 1px solid #F1F5F9;">
                    <td style="padding: 10px; font-family: monospace; font-weight: 700; color: #1E3A5F;">
                      <a href="<?= RACINE ?>depense/details/<?= $this->validator->crypter($d['id_depense']) ?>" style="color: #1E3A5F; text-decoration: underline;">
                        <?= htmlspecialchars($d['code_depense'] ?? ('DEP-' . $d['id_depense'])) ?>
                      </a>
                    </td>
                    <td style="padding: 10px; color: #334155;"><?= !empty($d['date_depense']) ? date('d/m/Y', strtotime($d['date_depense'])) : '-' ?></td>
                    <td style="padding: 10px; color: #0F172A; font-weight: 600;"><?= htmlspecialchars($d['beneficiaire_depense'] ?? ($d['motif_depense'] ?? '-')) ?></td>
                    <td style="padding: 10px; text-align: right; font-weight: 800; color: #DC2626;">
                      <?= number_format((float)($d['montant_depense'] ?? ($d['montant'] ?? 0)), 0, ',', ' ') ?> FCFA
                    </td>
                    <td style="padding: 10px; text-align: center;">
                      <span class="badge" style="background:#DCFCE7; color:#15803D; padding:2px 8px; border-radius:8px; font-weight:700; font-size:11px;">Validé</span>
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
