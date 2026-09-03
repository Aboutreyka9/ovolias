<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$item = $item ?? [];
$souscriptions = $souscriptions ?? [];
$nomComplet = trim(($item['nom_client'] ?? '') . ' ' . ($item['prenom_client'] ?? ''));
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">
      
      <!-- EN-TÊTE DE LA FICHE CLIENT -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Fiche Client : <?= htmlspecialchars($nomComplet) ?></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Code Client : <code style="font-weight: 800; color: #1E3A5F; font-size: 13px; background: #F1F5F9; padding: 2px 8px; border-radius: 4px;"><?= htmlspecialchars($item['code_client'] ?? '-') ?></code> &bull; Zone : <strong><?= htmlspecialchars($item['libelle_zone'] ?? '-') ?></strong></p>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="<?= RACINE ?>client/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour à la liste
          </a>
          <a href="<?= RACINE ?>client/edition/<?= $encryptedId ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none;">
            <i data-lucide="edit" style="width: 18px; height: 18px;"></i> Modifier le Profil
          </a>
        </div>
      </div>

      <!-- CARTE 1 : FICHE SIGNALÉTIQUE CLIENT -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
          <i data-lucide="user" style="width: 18px; height: 18px;"></i> Profil & Informations de Contact
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Nom & Prénom(s)</span>
            <div style="font-size: 17px; font-weight: 800; color: #0F172A; margin-top: 4px;"><?= htmlspecialchars($nomComplet) ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">CNI : <?= htmlspecialchars($item['cni_client'] ?? '-') ?></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Coordonnées Téléphoniques</span>
            <div style="font-size: 17px; font-weight: 800; color: #047857; margin-top: 4px;"><?= htmlspecialchars($item['telephone_client'] ?? '-') ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Email : <?= htmlspecialchars($item['email_client'] ?? '-') ?></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Localisation & Zone</span>
            <div style="font-size: 16px; font-weight: 800; color: #1E3A5F; margin-top: 4px;"><?= htmlspecialchars($item['libelle_zone'] ?? '-') ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Quartier : <?= htmlspecialchars($item['quartier_client'] ?? 'Non renseigné') ?></div>
          </div>

          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Statut du Compte</span>
            <div style="margin-top: 6px;">
              <?php if (($item['statut_client'] ?? '') === 'actif'): ?>
                <span class="badge" style="background:#DCFCE7; color:#15803D; padding:6px 14px; border-radius:10px; font-weight:800; font-size:12px;">Client Actif</span>
              <?php else: ?>
                <span class="badge" style="background:#FEE2E2; color:#B91C1C; padding:6px 14px; border-radius:10px; font-weight:800; font-size:12px;">Inactif / Suspendu</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- CARTE 2 : HISTORIQUE DES SOUSCRIPTIONS PACKS -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 2px solid #EFF6FF;">
          <div>
            <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
              <i data-lucide="package" style="width: 18px; height: 18px; color: #1E3A5F;"></i> Souscriptions & Cotisations de ce Client
            </h3>
          </div>
          <a href="<?= RACINE ?>souscription/formulaire" class="btn btn-sm btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 6px; font-size: 12px; text-decoration: none;">
            + Nouvelle Souscription
          </a>
        </div>

        <?php if (empty($souscriptions)): ?>
          <p style="color: #94A3B8; text-align: center; padding: 20px 0; font-style: italic;">Aucune souscription de pack enregistrée pour le moment.</p>
        <?php else: ?>
          <div style="width: 100%; overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
              <thead>
                <tr style="background: #F8FAFC; text-align: left; color: #64748B;">
                  <th style="padding: 10px 12px;">Code</th>
                  <th style="padding: 10px 12px;">Pack Souscrit</th>
                  <th style="padding: 10px 12px; text-align: right;">Cotisation / Jour</th>
                  <th style="padding: 10px 12px; text-align: center;">Jours Cotisés</th>
                  <th style="padding: 10px 12px; text-align: center;">Statut</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($souscriptions as $s): ?>
                  <tr style="border-bottom: 1px solid #F1F5F9;">
                    <td style="padding: 10px 12px; font-family: monospace; font-weight: 700; color: #1E3A5F;">
                      <a href="<?= RACINE ?>souscription/details/<?= $this->validator->crypter($s['id_souscription']) ?>" style="color: #1E3A5F; text-decoration: underline;">
                        <?= htmlspecialchars($s['code_souscription']) ?>
                      </a>
                    </td>
                    <td style="padding: 10px 12px; font-weight: 700; color: #0F172A;"><?= htmlspecialchars($s['libelle_pack'] ?? 'Pack Produit') ?></td>
                    <td style="padding: 10px 12px; text-align: right; font-weight: 800; color: #047857;"><?= number_format((float)($s['montant_cotisation_journaliere'] ?? 0), 0, ',', ' ') ?> FCFA</td>
                    <td style="padding: 10px 12px; text-align: center; font-weight: 700; color: #1E3A5F;">
                      <?= (int)($s['nombre_jour_cotise'] ?? 0) ?> / <?= (int)($s['nombre_jour_total'] ?? 0) ?> j
                    </td>
                    <td style="padding: 10px 12px; text-align: center;">
                      <?php if (($s['statut_souscription'] ?? '') === 'soldee'): ?>
                        <span class="badge" style="background:#DCFCE7; color:#15803D; padding:4px 10px; border-radius:8px; font-weight:800;">Soldée</span>
                      <?php else: ?>
                        <span class="badge" style="background:#E0F2FE; color:#0369A1; padding:4px 10px; border-radius:8px; font-weight:800;">En cours</span>
                      <?php endif; ?>
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
