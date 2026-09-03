<?php
require_once __DIR__ . '/../../public/inc/header.php';
$item = isset($item) ? $item : [];
$users = isset($users) ? $users : [];
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">
      
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Fiche Fonction : <?= htmlspecialchars($item['libelle_fonction'] ?? 'Fonction') ?></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Poste et responsabilité administrative ou académique</p>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="<?= RACINE ?>fonction/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour aux fonctions
          </a>
          <a href="<?= RACINE ?>fonction/edition/<?= $encryptedId ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="edit" style="width: 18px; height: 18px;"></i> Modifier la fonction
          </a>
        </div>
      </div>

      <!-- CARD 1 (COL-12) : DÉTAILS DE LA FONCTION -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
          <i data-lucide="briefcase" style="width: 18px; height: 18px;"></i> Informations sur le Poste
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
          <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 16px;">
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Intitulé du Poste</span>
            <div style="font-size: 18px; font-weight: 800; color: #0F172A; margin-top: 4px;"><?= htmlspecialchars($item['libelle_fonction'] ?? '-') ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Code : <code><?= htmlspecialchars($item['code_fonction'] ?? '-') ?></code></div>
          </div>

          <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 10px; padding: 16px;">
            <span style="font-size: 11px; font-weight: 700; color: #1E3A5F; text-transform: uppercase;">Effectif Assigné</span>
            <div style="font-size: 22px; font-weight: 800; color: #1E3A5F; margin-top: 4px;"><?= count($users) ?> agent(s)</div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Personnel actif occupant ce poste</div>
          </div>

          <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px; padding: 16px;">
            <span style="font-size: 11px; font-weight: 700; color: #15803D; text-transform: uppercase;">Statut</span>
            <div style="margin-top: 6px;">
              <?php if (($item['statut_fonction'] ?? '') === 'actif'): ?>
                <span class="badge" style="background:#DCFCE7; color:#15803D; padding:4px 12px; border-radius:10px; font-weight:700; font-size:12px;">Actif</span>
              <?php else: ?>
                <span class="badge" style="background:#FEE2E2; color:#B91C1C; padding:4px 12px; border-radius:10px; font-weight:700; font-size:12px;">Inactif</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- CARD 2 (COL-12) : COLLABORATEURS TITULAIRES DE CE POSTE -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
          <i data-lucide="users" style="width: 18px; height: 18px; color: #1E3A5F;"></i> Membres du Personnel Assignés (<?= count($users) ?>)
        </h3>

        <?php if (empty($users)): ?>
          <p style="color: #94A3B8; text-align: center; padding: 30px 0; font-style: italic;">Aucun collaborateur n'est assigné à cette fonction pour le moment.</p>
        <?php else: ?>
          <div style="width: 100%; overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
              <thead>
                <tr style="background: #F8FAFC; text-align: left; color: #64748B; font-size: 12px;">
                  <th style="padding: 10px;">Nom & Prénom(s)</th>
                  <th style="padding: 10px;">Email / Login</th>
                  <th style="padding: 10px;">Téléphone</th>
                  <th style="padding: 10px;">Rôle Système</th>
                  <th style="padding: 10px; text-align: center;">Statut</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $u): ?>
                  <tr style="border-bottom: 1px solid #F1F5F9;">
                    <td style="padding: 10px; font-weight: 700; color: #0F172A;">
                      <a href="<?= RACINE ?>user/details/<?= $this->validator->crypter($u['id_user']) ?>" style="color: #1E3A5F; text-decoration: underline;">
                        <?= htmlspecialchars(($u['nom_user'] ?? '') . ' ' . ($u['prenom_user'] ?? '')) ?>
                      </a>
                    </td>
                    <td style="padding: 10px; color: #334155;"><?= htmlspecialchars($u['email_user'] ?? '-') ?></td>
                    <td style="padding: 10px; color: #334155;"><?= htmlspecialchars($u['telephone_user'] ?? '-') ?></td>
                    <td style="padding: 10px;">
                      <span class="badge" style="background:#EFF6FF; color:#1E3A5F; padding:2px 8px; border-radius:6px; font-size:11px; font-weight:700;">
                        <?= htmlspecialchars($u['libelle_role'] ?? 'Utilisateur') ?>
                      </span>
                    </td>
                    <td style="padding: 10px; text-align: center;">
                      <span class="badge" style="background:#DCFCE7; color:#15803D; padding:2px 8px; border-radius:8px; font-weight:700; font-size:11px;">Actif</span>
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
