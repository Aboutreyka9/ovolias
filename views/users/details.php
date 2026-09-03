<?php
require_once __DIR__ . '/../../public/inc/header.php';
$user = isset($user) ? $user : [];
$role = isset($role) ? $role : [];
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px;">
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Fiche Utilisateur : <?= htmlspecialchars(($user['nom_user'] ?? '') . ' ' . ($user['prenom_user'] ?? '')) ?></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Consultation du compte d'accès, permissions et rôle attribué</p>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="<?= RACINE ?>user/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour à la liste
          </a>
          <a href="<?= RACINE ?>user/edition/<?= $encryptedId ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="edit" style="width: 18px; height: 18px;"></i> Modifier l'utilisateur
          </a>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px; width: 100%; box-sizing: border-box;">
        <!-- Identité Card -->
        <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); height: fit-content;">
          <div style="text-align: center; padding-bottom: 20px; border-bottom: 1px solid #F1F5F9;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: #EFF6FF; color: #1E3A5F; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px auto; font-size: 28px; font-weight: 800;">
              <?= strtoupper(substr($user['nom_user'] ?? 'U', 0, 1) . substr($user['prenom_user'] ?? 'S', 0, 1)) ?>
            </div>
            <h2 style="font-size: 18px; font-weight: 800; color: #0F172A; margin: 0;"><?= htmlspecialchars(($user['nom_user'] ?? '') . ' ' . ($user['prenom_user'] ?? '')) ?></h2>
            <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;"><?= htmlspecialchars($role['libelle_role'] ?? 'Rôle non attribué') ?></p>
            <div style="margin-top: 10px;">
              <?php if (($user['statut_user'] ?? '') === 'actif'): ?>
                <span class="badge" style="background:#DCFCE7; color:#15803D; padding:4px 12px; border-radius:12px; font-weight:700; font-size:12px;">Compte Actif</span>
              <?php else: ?>
                <span class="badge" style="background:#FEE2E2; color:#B91C1C; padding:4px 12px; border-radius:12px; font-weight:700; font-size:12px;">Compte Inactif</span>
              <?php endif; ?>
            </div>
          </div>
          <div style="padding-top: 16px;">
            <div style="margin-bottom: 12px;">
              <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Code Utilisateur</span>
              <div style="font-size: 14px; font-weight: 700; color: #1E3A5F; font-family: monospace;"><?= htmlspecialchars($user['code_user'] ?? '-') ?></div>
            </div>
            <div style="margin-bottom: 12px;">
              <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Email de connexion</span>
              <div style="font-size: 14px; font-weight: 600; color: #0F172A;"><?= htmlspecialchars($user['email_user'] ?? 'Non renseigné') ?></div>
            </div>
            <div style="margin-bottom: 12px;">
              <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Téléphone</span>
              <div style="font-size: 14px; font-weight: 600; color: #0F172A;"><?= htmlspecialchars($user['telephone_user'] ?? 'Non renseigné') ?></div>
            </div>
            <div style="margin-bottom: 12px;">
              <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Zone d'Affectation</span>
              <div style="font-size: 14px; font-weight: 700; color: #1E3A5F;"><?= htmlspecialchars($user['libelle_zone'] ?? 'Globale') ?></div>
            </div>
            <div>
              <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Dernière Connexion</span>
              <div style="font-size: 13px; color: #64748B;"><?= !empty($user['last_connexion']) ? date('d/m/Y H:i', strtotime($user['last_connexion'])) : 'Jamais connecté' ?></div>
            </div>
          </div>
        </div>

        <!-- Rôle & Privilèges Card -->
        <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
          <h3 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #F1F5F9; padding-bottom: 12px;">
            <i data-lucide="shield-check" style="width: 20px; height: 20px; color: #1E3A5F;"></i> Rôles Attribués & Privilèges RBAC
          </h3>

          <div style="margin-bottom: 24px;">
            <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase; margin-bottom: 8px;">Rôles Système Détenus</div>
            
            <?php 
              $displayRoles = !empty($userRoles) ? $userRoles : (!empty($role) ? [$role] : []);
            ?>

            <?php if (!empty($displayRoles)): ?>
              <div style="display: flex; flex-direction: column; gap: 12px;">
                <?php foreach($displayRoles as $ur): ?>
                  <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-left: 4px solid var(--primary-color, #18385F); border-radius: 8px; padding: 14px 16px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                      <div style="font-size: 15px; font-weight: 800; color: #1E3A5F; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="award" style="width: 16px; height: 16px; color: var(--primary-color);"></i>
                        <?= htmlspecialchars($ur['libelle_role'] ?? 'Rôle non défini') ?>
                      </div>
                      <span style="background: #E2E8F0; color: #475569; font-weight: 700; font-size: 11px; padding: 2px 8px; border-radius: 6px;">
                        <?= htmlspecialchars($ur['groupe'] ?? ($ur['module'] ?? 'Standard')) ?>
                      </span>
                    </div>
                    <?php if (!empty($ur['description'])): ?>
                      <p style="color: #64748B; font-size: 12.5px; margin: 6px 0 0 24px;"><?= htmlspecialchars($ur['description']) ?></p>
                    <?php endif; ?>

                    <div style="margin-top: 10px; padding-top: 8px; border-top: 1px dashed #E2E8F0; display: flex; flex-wrap: wrap; gap: 16px;">
                      <span style="font-size: 12px; font-weight: 700; color: <?= !empty($ur['create_permission']) ? '#15803D' : '#94A3B8' ?>; display: inline-flex; align-items: center; gap: 4px;">
                        <i data-lucide="<?= !empty($ur['create_permission']) ? 'check' : 'x' ?>" style="width: 14px; height: 14px;"></i> Créer
                      </span>
                      <span style="font-size: 12px; font-weight: 700; color: <?= !empty($ur['edit_permission']) ? '#0284C7' : '#94A3B8' ?>; display: inline-flex; align-items: center; gap: 4px;">
                        <i data-lucide="<?= !empty($ur['edit_permission']) ? 'check' : 'x' ?>" style="width: 14px; height: 14px;"></i> Modifier
                      </span>
                      <span style="font-size: 12px; font-weight: 700; color: <?= (!isset($ur['show_permission']) || $ur['show_permission'] == 1) ? '#475569' : '#94A3B8' ?>; display: inline-flex; align-items: center; gap: 4px;">
                        <i data-lucide="<?= (!isset($ur['show_permission']) || $ur['show_permission'] == 1) ? 'check' : 'x' ?>" style="width: 14px; height: 14px;"></i> Consulter
                      </span>
                      <span style="font-size: 12px; font-weight: 700; color: <?= !empty($ur['delete_permission']) ? '#DC2626' : '#94A3B8' ?>; display: inline-flex; align-items: center; gap: 4px;">
                        <i data-lucide="<?= !empty($ur['delete_permission']) ? 'check' : 'x' ?>" style="width: 14px; height: 14px;"></i> Supprimer
                      </span>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div style="color: #94A3B8; font-style: italic;">Aucun rôle attribué pour le moment.</div>
            <?php endif; ?>
          </div>

          <div style="background: #F8FAFC; border-radius: 10px; padding: 20px; border: 1px solid #E2E8F0;">
            <h4 style="font-size: 13px; font-weight: 800; color: #0F172A; margin: 0 0 16px 0;">Matrice des Actions Autorisées :</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
              
              <div style="display: flex; align-items: center; gap: 10px;">
                <i data-lucide="<?= (isset($role['create_permission']) && $role['create_permission'] == 1) ? 'check-circle-2' : 'x-circle' ?>" style="width: 20px; height: 20px; color: <?= (isset($role['create_permission']) && $role['create_permission'] == 1) ? '#15803D' : '#DC2626' ?>;"></i>
                <div>
                  <div style="font-weight: 700; font-size: 13px; color: #0F172A;">Création (Ajout)</div>
                  <div style="font-size: 11px; color: #64748B;"><?= (isset($role['create_permission']) && $role['create_permission'] == 1) ? 'Autorisé' : 'Refusé' ?></div>
                </div>
              </div>

              <div style="display: flex; align-items: center; gap: 10px;">
                <i data-lucide="<?= (isset($role['edit_permission']) && $role['edit_permission'] == 1) ? 'check-circle-2' : 'x-circle' ?>" style="width: 20px; height: 20px; color: <?= (isset($role['edit_permission']) && $role['edit_permission'] == 1) ? '#0284C7' : '#DC2626' ?>;"></i>
                <div>
                  <div style="font-weight: 700; font-size: 13px; color: #0F172A;">Modification (Édition)</div>
                  <div style="font-size: 11px; color: #64748B;"><?= (isset($role['edit_permission']) && $role['edit_permission'] == 1) ? 'Autorisé' : 'Refusé' ?></div>
                </div>
              </div>

              <div style="display: flex; align-items: center; gap: 10px;">
                <i data-lucide="<?= (!isset($role['show_permission']) || $role['show_permission'] == 1) ? 'check-circle-2' : 'x-circle' ?>" style="width: 20px; height: 20px; color: (!isset($role['show_permission']) || $role['show_permission'] == 1) ? '#15803D' : '#DC2626';"></i>
                <div>
                  <div style="font-weight: 700; font-size: 13px; color: #0F172A;">Consultation (Lecture)</div>
                  <div style="font-size: 11px; color: #64748B;"><?= (!isset($role['show_permission']) || $role['show_permission'] == 1) ? 'Autorisé' : 'Refusé' ?></div>
                </div>
              </div>

              <div style="display: flex; align-items: center; gap: 10px;">
                <i data-lucide="<?= (isset($role['delete_permission']) && $role['delete_permission'] == 1) ? 'check-circle-2' : 'x-circle' ?>" style="width: 20px; height: 20px; color: <?= (isset($role['delete_permission']) && $role['delete_permission'] == 1) ? '#15803D' : '#DC2626' ?>;"></i>
                <div>
                  <div style="font-weight: 700; font-size: 13px; color: #0F172A;">Suppression</div>
                  <div style="font-size: 11px; color: #64748B;"><?= (isset($role['delete_permission']) && $role['delete_permission'] == 1) ? 'Autorisé' : 'Refusé' ?></div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
<script>$(document).ready(function() { if (window.lucide) lucide.createIcons(); });</script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
