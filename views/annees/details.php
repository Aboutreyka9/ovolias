<?php
require_once __DIR__ . '/../../public/inc/header.php';
$item = isset($item) ? $item : [];
$stats = isset($stats) ? $stats : [];
$classes = isset($classes) ? $classes : [];
$semestres = isset($semestres) ? $semestres : [];
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">
      
      <!-- En-tête de page -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Année Académique : <?= htmlspecialchars($item['libelle_annee'] ?? 'Année') ?></h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Bilan académique, effectifs d'étudiants, classes et semestres</p>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="<?= RACINE ?>annee/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour à la liste
          </a>
          <a href="<?= RACINE ?>annee/edition/<?= $encryptedId ?>" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="edit" style="width: 18px; height: 18px;"></i> Modifier l'année
          </a>
        </div>
      </div>

      <!-- CARD 1 (COL-12) : INFORMATIONS GÉNÉRALES & INDICATEURS CLÉS -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; width: 100%; box-sizing: border-box;">
        <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
          <i data-lucide="calendar" style="width: 18px; height: 18px;"></i> Fiche Synthétique & Statistiques
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 20px;">
          <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 16px;">
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Libellé Année</span>
            <div style="font-size: 18px; font-weight: 800; color: #0F172A; margin-top: 4px;"><?= htmlspecialchars($item['libelle_annee'] ?? '-') ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Code : <code><?= htmlspecialchars($item['code_annee'] ?? '-') ?></code></div>
          </div>

          <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 10px; padding: 16px;">
            <span style="font-size: 11px; font-weight: 700; color: #1E3A5F; text-transform: uppercase;">Étudiants Inscrits</span>
            <div style="font-size: 24px; font-weight: 800; color: #1E3A5F; margin-top: 4px;"><?= number_format((int)($stats['total_etudiants'] ?? 0), 0, ',', ' ') ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Inscriptions actives</div>
          </div>

          <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px; padding: 16px;">
            <span style="font-size: 11px; font-weight: 700; color: #15803D; text-transform: uppercase;">Classes Ouvertes</span>
            <div style="font-size: 24px; font-weight: 800; color: #15803D; margin-top: 4px;"><?= (int)($stats['total_classes'] ?? 0) ?></div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Groupes pédagogiques</div>
          </div>

          <div style="background: #FAF5FF; border: 1px solid #E9D5FF; border-radius: 10px; padding: 16px;">
            <span style="font-size: 11px; font-weight: 700; color: #7E22CE; text-transform: uppercase;">Recouvrement Réalisé</span>
            <div style="font-size: 20px; font-weight: 800; color: #7E22CE; margin-top: 4px;"><?= number_format((float)($stats['total_recouvrement'] ?? 0), 0, ',', ' ') ?> F</div>
            <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Total paiements validés</div>
          </div>
        </div>

        <div style="display: flex; gap: 20px; flex-wrap: wrap; padding-top: 14px; border-top: 1px solid #F1F5F9; font-size: 13px;">
          <div><strong style="color: #64748B;">Date de Début :</strong> <span style="font-weight: 700; color: #0F172A;"><?= !empty($item['date_debut_annee']) ? date('d/m/Y', strtotime($item['date_debut_annee'])) : 'Non définie' ?></span></div>
          <div><strong style="color: #64748B;">Date de Fin :</strong> <span style="font-weight: 700; color: #0F172A;"><?= !empty($item['date_fin_annee']) ? date('d/m/Y', strtotime($item['date_fin_annee'])) : 'Non définie' ?></span></div>
          <div><strong style="color: #64748B;">Statut :</strong> 
            <?php if (($item['statut_annee'] ?? '') === 'actif'): ?>
              <span class="badge" style="background:#DCFCE7; color:#15803D; padding:3px 10px; border-radius:10px; font-weight:700;">Actif</span>
            <?php else: ?>
              <span class="badge" style="background:#FEE2E2; color:#B91C1C; padding:3px 10px; border-radius:10px; font-weight:700;">Clôturé</span>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- CARD 2 (COL-12) : LISTE DES CLASSES DE L'ANNÉE -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; width: 100%; box-sizing: border-box;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 2px solid #EFF6FF;">
          <div>
            <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
              <i data-lucide="layers" style="width: 18px; height: 18px; color: #1E3A5F;"></i> Classes & Groupes Pédagogiques (<?= count($classes) ?>)
            </h3>
          </div>
          <a href="<?= RACINE ?>classe/formulaire" class="btn btn-sm btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 6px; font-size: 12px;">
            + Ouvrir une classe
          </a>
        </div>

        <?php if (empty($classes)): ?>
          <p style="color: #94A3B8; text-align: center; padding: 20px 0; font-style: italic;">Aucune classe n'a été ouverte pour cette année académique.</p>
        <?php else: ?>
          <div style="width: 100%; overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
              <thead>
                <tr style="background: #F8FAFC; text-align: left; color: #64748B; font-size: 12px;">
                  <th style="padding: 10px;">Classe</th>
                  <th style="padding: 10px;">Filière</th>
                  <th style="padding: 10px;">Niveau</th>
                  <th style="padding: 10px; text-align: center;">Capacité</th>
                  <th style="padding: 10px; text-align: center;">Inscrits</th>
                  <th style="padding: 10px; text-align: center;">Statut</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($classes as $cl): ?>
                  <tr style="border-bottom: 1px solid #F1F5F9;">
                    <td style="padding: 10px; font-weight: 700; color: #0F172A;">
                      <a href="<?= RACINE ?>classe/details/<?= $this->validator->crypter($cl['id_classe']) ?>" style="color: #1E3A5F; text-decoration: underline;">
                        <?= htmlspecialchars($cl['libelle_classe']) ?>
                      </a>
                    </td>
                    <td style="padding: 10px; color: #334155;"><?= htmlspecialchars($cl['libelle_filiere'] ?? '-') ?></td>
                    <td style="padding: 10px; color: #334155;"><?= htmlspecialchars($cl['libelle_niveau'] ?? '-') ?></td>
                    <td style="padding: 10px; text-align: center; color: #64748B;"><?= (int)($cl['capacite_max_classe'] ?? 0) ?></td>
                    <td style="padding: 10px; text-align: center;">
                      <span style="background: #EFF6FF; color: #1E3A5F; font-weight: 800; padding: 2px 10px; border-radius: 8px; font-size: 12px;">
                        <?= (int)($cl['nb_eleves'] ?? 0) ?>
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

      <!-- CARD 3 (COL-12) : SEMESTRES ASSOCIÉS -->
      <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 2px solid #EFF6FF;">
          <div>
            <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
              <i data-lucide="clock" style="width: 18px; height: 18px; color: #1E3A5F;"></i> Découpage Semestriel (<?= count($semestres) ?>)
            </h3>
          </div>
          <a href="<?= RACINE ?>semestre/formulaire" class="btn btn-sm btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 700; border-radius: 6px; font-size: 12px;">
            + Ajouter un semestre
          </a>
        </div>

        <?php if (empty($semestres)): ?>
          <p style="color: #94A3B8; text-align: center; padding: 20px 0; font-style: italic;">Aucun semestre n'a encore été créé pour cette année.</p>
        <?php else: ?>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px;">
            <?php foreach ($semestres as $s): ?>
              <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 14px 16px;">
                <div style="font-size: 14px; font-weight: 800; color: #0F172A; margin-bottom: 4px;"><?= htmlspecialchars($s['libelle_semestre']) ?></div>
                <div style="font-size: 12px; color: #64748B;">Code : <code><?= htmlspecialchars($s['code_semestre']) ?></code></div>
                <div style="margin-top: 8px;">
                  <span class="badge" style="background:#DCFCE7; color:#15803D; padding:2px 8px; border-radius:8px; font-weight:700; font-size:11px;">Actif</span>
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
