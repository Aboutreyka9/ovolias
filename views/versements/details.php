<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$item = $item ?? [];
$commercial = $commercial ?? [];
$zone = $zone ?? [];
$nomCommercial = trim(($commercial['nom_user'] ?? '') . ' ' . ($commercial['prenom_user'] ?? ''));
$montant = (float)($item['montant_versement'] ?? 0);
$statut = $item['statut_versement'] ?? 'En attente';
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">
      
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Bordereau Versement : <code style="font-weight: 800; color: #1E3A5F; font-size: 20px; background: #EFF6FF; padding: 3px 10px; border-radius: 6px;"><?= htmlspecialchars($item['code_versement_commercial'] ?? '-') ?></code></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Dépôt de fonds effectué du <strong><?= htmlspecialchars($item['periode_versement_debut'] ?? '-') ?></strong> au <strong><?= htmlspecialchars($item['periode_versement_fin'] ?? '-') ?></strong></p>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="<?= RACINE ?>versement/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Retour aux versements
          </a>
          <a href="<?= RACINE ?>versement/edition/<?= $encryptedId ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
            <i data-lucide="edit" style="width: 16px; height: 16px;"></i> Modifier
          </a>
        </div>
      </div>

      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 20px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
          <i data-lucide="arrow-down-left" style="width: 18px; height: 18px;"></i> Information sur le Dépôt de Fonds
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Agent Commercial</span>
            <div style="font-size: 17px; font-weight: 800; color: #0F172A; margin-top: 4px;"><?= htmlspecialchars($nomCommercial ?: 'Non renseigné') ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Code : <code><?= htmlspecialchars($item['commercial_code'] ?? '-') ?></code></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Zone d'Activité</span>
            <div style="font-size: 17px; font-weight: 800; color: #0F172A; margin-top: 4px;"><?= htmlspecialchars($zone['libelle_zone'] ?? ($item['zone_code'] ?? '-')) ?></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Montant Versé</span>
            <div style="font-size: 22px; font-weight: 800; color: #15803D; margin-top: 4px;"><?= number_format($montant, 0, ',', ' ') ?> FCFA</div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Référence</span>
            <div style="font-size: 16px; font-weight: 800; color: #1E3A5F; margin-top: 4px;"><?= htmlspecialchars($item['reference_versement'] ?? 'Aucune') ?></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Statut Validation</span>
            <div style="margin-top: 6px;">
              <?php 
                $badge = 'bg-warning text-dark';
                $libelle = 'En attente';
                if ($statut === 'valide') { $badge = 'bg-success'; $libelle = 'Validé en Caisse'; }
                elseif ($statut === 'ennule' || $statut === 'annule') { $badge = 'bg-danger'; $libelle = 'Annulé'; }
              ?>
              <span class="badge <?= $badge ?>" style="padding: 6px 14px; border-radius: 10px; font-weight: 800; font-size: 12px;"><?= htmlspecialchars($libelle) ?></span>
            </div>
          </div>
        </div>

        <?php if (!empty($item['commentaire_validation'])): ?>
        <div style="margin-top: 20px; padding: 12px 16px; background: #F8FAFC; border-left: 4px solid #1E3A5F; border-radius: 4px; font-size: 13px; color: #334155;">
          <strong>Commentaire de validation :</strong> <?= nl2br(htmlspecialchars($item['commentaire_validation'])) ?>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </main>
</div>
<script>$(document).ready(function() { if (window.lucide) lucide.createIcons(); });</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
