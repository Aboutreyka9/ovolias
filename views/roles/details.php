<?php
require_once __DIR__ . '/../../public/inc/header.php';
$role = isset($role) ? $role : [];
$permissions = isset($permissions) ? $permissions : [];
$users = isset($users) ? $users : [];
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">
      
      <!-- En-tête de page -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Fiche Rôle : <?= htmlspecialchars($role['libelle_role'] ?? 'Rôle') ?></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Configuration des privilèges et liste des utilisateurs titulaires</p>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="<?= RACINE ?>role/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour à la liste
          </a>
          <a href="<?= RACINE ?>role/edition/<?= $encryptedId ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="edit" style="width: 18px; height: 18px;"></i> Configurer ce rôle
          </a>
        </div>
      </div>

      <!-- CARD 1 (COL-12 PLEINE LARGEUR) : INFORMATIONS GÉNÉRALES DU RÔLE -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
          <i data-lucide="shield" style="width: 18px; height: 18px;"></i> Informations Générales du Rôle
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; align-items: center;">
          
          <!-- Libellé du Rôle & Département -->
          <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; min-width: 48px; border-radius: 10px; background: #EFF6FF; color: #1E3A5F; display: flex; align-items: center; justify-content: center;">
              <i data-lucide="award" style="width: 26px; height: 26px;"></i>
            </div>
            <div>
              <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 2px;">Libellé Rôle</span>
              <h2 style="font-size: 18px; font-weight: 800; color: #0F172A; margin: 0; line-height: 1.2;">
                <?= htmlspecialchars($role['libelle_role'] ?? '-') ?>
              </h2>
              <span style="font-size: 12px; color: #64748B; font-weight: 600;">
                Groupe : <?= htmlspecialchars($role['groupe'] ?? ($role['module'] ?? 'Direction & IT')) ?>
              </span>
            </div>
          </div>

          <!-- Code Système -->
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">Code Système</span>
            <code style="font-size: 14px; font-weight: 800; color: #1E3A5F; background: #F1F5F9; padding: 4px 10px; border-radius: 6px; display: inline-block;">
              <?= htmlspecialchars($role['code_role'] ?? '-') ?>
            </code>
          </div>

          <!-- Description -->
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">Description du Rôle</span>
            <div style="font-size: 13px; color: #334155; line-height: 1.4;">
              <?= htmlspecialchars($role['description'] ?? 'Accès complet sur les modules du rôle.') ?>
            </div>
          </div>

          <!-- Statut -->
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">Statut d'Activité</span>
            <div>
              <?php if (($role['statut_role'] ?? '') === 'actif'): ?>
                <span class="badge" style="background:#DCFCE7; color:#15803D; padding:5px 14px; border-radius:10px; font-weight:700; font-size:12px; display: inline-block;">Actif</span>
              <?php else: ?>
                <span class="badge" style="background:#FEE2E2; color:#B91C1C; padding:5px 14px; border-radius:10px; font-weight:700; font-size:12px; display: inline-block;">Inactif</span>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </div>

      <!-- CARD 2 (COL-12 PLEINE LARGEUR) : PERMISSIONS AUTORISÉES -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; width: 100%; box-sizing: border-box;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #EFF6FF;">
          <div>
            <h3 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
              <i data-lucide="key" style="width: 18px; height: 18px; color: #1E3A5F;"></i> Permissions & Privilèges Autorisés
            </h3>
            <p style="color: #64748B; font-size: 12px; margin: 4px 0 0 0;">Liste des droits accordés aux utilisateurs ayant ce rôle</p>
          </div>
          <span style="font-size: 12px; font-weight: 700; color: #15803D; background: #DCFCE7; padding: 4px 12px; border-radius: 10px; border: 1px solid #86EFAC;">
            <?= count($permissions) ?> permissions actives
          </span>
        </div>

        <?php if (empty($permissions)): ?>
          <div style="text-align: center; padding: 40px 20px; color: #94A3B8;">
            <i data-lucide="shield-off" style="width: 42px; height: 42px; margin-bottom: 8px; opacity: 0.5;"></i>
            <p style="font-size: 13px; margin: 0;">Aucune permission spécifique n'a été rattachée à ce rôle.</p>
          </div>
        <?php else: ?>
          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px;">
            <?php foreach ($permissions as $p): ?>
              <div style="background: #F8FAFC; border: 1px solid #E2E8F0; color: #1E3A5F; font-weight: 600; font-size: 13px; padding: 10px 14px; border-radius: 8px; display: flex; align-items: center; gap: 10px; transition: all 0.15s ease;">
                <div style="width: 22px; height: 22px; min-width: 22px; border-radius: 50%; background: #DCFCE7; color: #15803D; display: flex; align-items: center; justify-content: center;">
                  <i data-lucide="check" style="width: 14px; height: 14px;"></i>
                </div>
                <div>
                  <div style="font-weight: 700; color: #0F172A; line-height: 1.3;"><?= htmlspecialchars($p['libelle_permission']) ?></div>
                  <div style="font-size: 11px; color: #64748B; font-family: monospace;"><?= htmlspecialchars($p['code_permission']) ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- CARD 3 (COL-12 PLEINE LARGEUR) : UTILISATEURS ASSIGNÉS À CE RÔLE -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #EFF6FF;">
          <div>
            <h3 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
              <i data-lucide="users" style="width: 18px; height: 18px; color: #1E3A5F;"></i> Utilisateurs Titulaires de ce Rôle
            </h3>
            <p style="color: #64748B; font-size: 12px; margin: 4px 0 0 0;">Membres du personnel disposant actuellement de ce niveau d'accès</p>
          </div>
          <span style="font-size: 12px; font-weight: 700; color: #1E3A5F; background: #EFF6FF; padding: 4px 12px; border-radius: 10px; border: 1px solid #BFDBFE;">
            <?= count($users) ?> utilisateur<?= count($users) > 1 ? 's' : '' ?>
          </span>
        </div>

        <?php if (empty($users)): ?>
          <div style="text-align: center; padding: 40px 20px; color: #94A3B8;">
            <i data-lucide="user-x" style="width: 42px; height: 42px; margin-bottom: 8px; opacity: 0.5;"></i>
            <p style="font-size: 13px; margin: 0;">Aucun utilisateur n'est actuellement titulaire de ce rôle.</p>
          </div>
        <?php else: ?>
          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px;">
            <?php foreach ($users as $u): ?>
              <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 14px 16px; display: flex; align-items: center; gap: 12px; transition: all 0.15s ease;">
                <div style="width: 42px; height: 42px; min-width: 42px; border-radius: 50%; background: #EFF6FF; color: #1E3A5F; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px; border: 1px solid #BFDBFE;">
                  <?= strtoupper(substr($u['nom_user'] ?? 'U', 0, 1) . substr($u['prenom_user'] ?? '', 0, 1)) ?>
                </div>
                <div style="flex: 1; overflow: hidden;">
                  <div style="font-size: 14px; font-weight: 700; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= htmlspecialchars(($u['nom_user'] ?? '') . ' ' . ($u['prenom_user'] ?? '')) ?>
                  </div>
                  <div style="font-size: 12px; color: #1E3A5F; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= htmlspecialchars($u['email_user'] ?? '') ?>
                  </div>
                  <?php if (!empty($u['telephone_user'])): ?>
                    <div style="font-size: 11px; color: #64748B;">
                      <?= htmlspecialchars($u['telephone_user']) ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>
</div>
<script>$(document).ready(function() { if (window.lucide) lucide.createIcons(); });</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
