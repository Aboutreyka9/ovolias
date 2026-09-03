<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>

<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>

  <main class="main-content">
    <div class="content-header" style="margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
      <div>
        <h1 style="font-size: 24px; font-weight: 800; color: #1E293B; margin: 0; display: flex; align-items: center; gap: 10px;">
          <i data-lucide="sliders" style="width: 28px; height: 28px; color: #2563EB;"></i>
          Paramètres Globaux Système Lavex
        </h1>
        <p style="color: #64748B; font-size: 14px; margin: 4px 0 0;">
          Configuration centrale de la commission globale, des tarifs logistiques standards et des réglages plateforme (Réservé au Super Admin).
        </p>
      </div>
    </div>

    <?php if (isset($_SESSION['success_msg'])): ?>
      <div style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #047857; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
        <?= htmlspecialchars($_SESSION['success_msg']) ?>
      </div>
      <?php unset($_SESSION['success_msg']); ?>
    <?php endif; ?>

    <form action="<?= RACINE ?>setting/list" method="POST">
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">

        <!-- === BLOC 1: COMMISSION & FINANCES === -->
        <div class="card" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
          <h3 style="font-size: 16px; font-weight: 700; color: #1E293B; margin-top: 0; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #F1F5F9; padding-bottom: 12px;">
            <i data-lucide="percent" style="width: 20px; height: 20px; color: #2563EB;"></i>
            Commission & Modèle Économique
          </h3>

          <div class="form-field" style="margin-bottom: 18px;">
            <label for="commission_defaut_lavex" style="font-weight: 700; font-size: 13px; color: #1E293B; display: block; margin-bottom: 6px;">
              Taux de Commission Globale Lavex (%)
            </label>
            <div class="input-with-icon">
              <span class="input-icon"><?= Validator::icon('percent'); ?></span>
              <input type="number" class="form-control" id="commission_defaut_lavex" name="commission_defaut_lavex"
                     min="0" max="100" step="0.5" value="<?= htmlspecialchars($settings['commission_defaut_lavex'] ?? '0.00') ?>" required>
            </div>
            <small style="color: #64748B; font-size: 11.5px; display: block; margin-top: 6px;">
              Pourcentage prélevé par défaut par Lavex sur les ventes de tous les pressings partenaires (0.00% au lancement).
            </small>
          </div>
        </div>

        <!-- === BLOC 2: LOGISTIQUE STANDARDS === -->
        <div class="card" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
          <h3 style="font-size: 16px; font-weight: 700; color: #1E293B; margin-top: 0; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #F1F5F9; padding-bottom: 12px;">
            <i data-lucide="truck" style="width: 20px; height: 20px; color: #2563EB;"></i>
            Tarification Flotte Officielle Lavex
          </h3>

          <div class="form-field" style="margin-bottom: 18px;">
            <label for="frais_collecte_defaut_lavex" style="font-weight: 700; font-size: 13px; color: #1E293B; display: block; margin-bottom: 6px;">
              Frais de Collecte Standards (FCFA)
            </label>
            <div class="input-with-icon">
              <span class="input-icon"><?= Validator::icon('dollar-sign'); ?></span>
              <input type="number" class="form-control" id="frais_collecte_defaut_lavex" name="frais_collecte_defaut_lavex"
                     min="0" step="100" value="<?= htmlspecialchars($settings['frais_collecte_defaut_lavex'] ?? '1000.00') ?>" required>
            </div>
            <small style="color: #64748B; font-size: 11.5px; display: block; margin-top: 6px;">
              Tarif forfaitaire de collecte appliqué lorsque le pressing utilise la flotte Lavex.
            </small>
          </div>

          <div class="form-field" style="margin-bottom: 18px;">
            <label for="frais_livraison_defaut_lavex" style="font-weight: 700; font-size: 13px; color: #1E293B; display: block; margin-bottom: 6px;">
              Frais de Livraison Standards (FCFA)
            </label>
            <div class="input-with-icon">
              <span class="input-icon"><?= Validator::icon('dollar-sign'); ?></span>
              <input type="number" class="form-control" id="frais_livraison_defaut_lavex" name="frais_livraison_defaut_lavex"
                     min="0" step="100" value="<?= htmlspecialchars($settings['frais_livraison_defaut_lavex'] ?? '1000.00') ?>" required>
            </div>
            <small style="color: #64748B; font-size: 11.5px; display: block; margin-top: 6px;">
              Tarif forfaitaire de livraison appliqué lorsque le pressing utilise la flotte Lavex.
            </small>
          </div>

          <div class="form-field" style="margin-bottom: 18px;">
            <label for="delai_livraison_defaut_lavex" style="font-weight: 700; font-size: 13px; color: #1E293B; display: block; margin-bottom: 6px;">
              Temps / Délais Moyen de Livraison Lavex
            </label>
            <div class="input-with-icon">
              <span class="input-icon"><?= Validator::icon('clock'); ?></span>
              <input type="text" class="form-control" id="delai_livraison_defaut_lavex" name="delai_livraison_defaut_lavex"
                     placeholder="ex: 24h - 48h" value="<?= htmlspecialchars($settings['delai_livraison_defaut_lavex'] ?? '24h - 48h') ?>" required>
            </div>
            <small style="color: #64748B; font-size: 11.5px; display: block; margin-top: 6px;">
              Délais moyen de traitement et livraison garanti par la flotte officielle Lavex (ex: 24h - 48h).
            </small>
          </div>
        </div>

        <!-- === BLOC 3: INFORMATIONS DE LA PLATEFORME === -->
        <div class="card" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); grid-column: 1 / -1;">
          <h3 style="font-size: 16px; font-weight: 700; color: #1E293B; margin-top: 0; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #F1F5F9; padding-bottom: 12px;">
            <i data-lucide="info" style="width: 20px; height: 20px; color: #2563EB;"></i>
            Identité & Support Lavex
          </h3>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
            <div class="form-field">
              <label for="nom_plateforme" style="font-weight: 700; font-size: 13px; color: #1E293B; display: block; margin-bottom: 6px;">
                Nom de la Plateforme
              </label>
              <input type="text" class="form-control" id="nom_plateforme" name="nom_plateforme"
                     value="<?= htmlspecialchars($settings['nom_plateforme'] ?? 'Lavex') ?>" required>
            </div>

            <div class="form-field">
              <label for="email_support" style="font-weight: 700; font-size: 13px; color: #1E293B; display: block; margin-bottom: 6px;">
                Email Support Client
              </label>
              <input type="email" class="form-control" id="email_support" name="email_support"
                     value="<?= htmlspecialchars($settings['email_support'] ?? 'contact@lavex.ci') ?>" required>
            </div>
          </div>
        </div>

        <!-- === BLOC 4: NOTIFICATIONS PUSH ONESIGNAL === -->
        <div class="card" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); grid-column: 1 / -1;">
          <h3 style="font-size: 16px; font-weight: 700; color: #1E293B; margin-top: 0; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #F1F5F9; padding-bottom: 12px;">
            <i data-lucide="bell-ring" style="width: 20px; height: 20px; color: #2563EB;"></i>
            Clés de Notifications Push Temps Réel (OneSignal)
          </h3>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
            <div class="form-field">
              <label for="onesignal_app_id" style="font-weight: 700; font-size: 13px; color: #1E293B; display: block; margin-bottom: 6px;">
                OneSignal App ID
              </label>
              <input type="text" class="form-control" id="onesignal_app_id" name="onesignal_app_id"
                     placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" value="<?= htmlspecialchars($settings['onesignal_app_id'] ?? '') ?>">
              <small style="color: #64748B; font-size: 11.5px; display: block; margin-top: 6px;">
                Identifiant unique de votre projet OneSignal App.
              </small>
            </div>

            <div class="form-field">
              <label for="onesignal_rest_api_key" style="font-weight: 700; font-size: 13px; color: #1E293B; display: block; margin-bottom: 6px;">
                OneSignal REST API Key
              </label>
              <input type="password" class="form-control" id="onesignal_rest_api_key" name="onesignal_rest_api_key"
                     placeholder="Os_v2_app_xxxxxxxx..." value="<?= htmlspecialchars($settings['onesignal_rest_api_key'] ?? '') ?>">
              <small style="color: #64748B; font-size: 11.5px; display: block; margin-top: 6px;">
                Clé secrète REST API utilisée par le serveur pour expédier les notifications push en arrière-plan.
              </small>
            </div>
          </div>
        <!-- === BLOC 5: BANDEAU PROMOTIONNEL DÉFILANT (PROMO BAR) === -->
        <div class="card" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); grid-column: 1 / -1;">
          <h3 style="font-size: 16px; font-weight: 700; color: #1E293B; margin-top: 0; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #F1F5F9; padding-bottom: 12px;">
            <i data-lucide="megaphone" style="width: 20px; height: 20px; color: #2563EB;"></i>
            Bandeau Promotionnel Défilant (Marketplace Lavex)
          </h3>

          <div class="form-field">
            <label for="texte_promo_bar" style="font-weight: 700; font-size: 13px; color: #1E293B; display: block; margin-bottom: 6px;">
              Annonces Défilantes du Bandeau (séparer les messages par le symbole |)
            </label>
            <textarea class="form-control" id="texte_promo_bar" name="texte_promo_bar" rows="2" style="width:100%; border-radius:10px; font-weight:600; padding:10px 14px;" placeholder="Lavex - Pressing & Laverie à domicile | -20% sur votre première commande avec le code LAVEX20 | Service rapide & livraison offerte dès 15 000 FCFA"><?= htmlspecialchars($settings['texte_promo_bar'] ?? 'Lavex - Pressing & Laverie à domicile | -20% sur votre première commande avec le code LAVEX20 | Service rapide & livraison offerte dès 15 000 FCFA') ?></textarea>
            <small style="color: #64748B; font-size: 11.5px; display: block; margin-top: 6px;">
              Chaque message séparé par un symbole <strong>|</strong> sera animé et cliquable sur le bandeau supérieur de l'application client Lavex (détection automatique des codes promo et filtres de livraison).
            </small>
          </div>
        </div>

        <!-- === BLOC 6: GESTION DES CODES PROMO & RÉDUCTIONS === -->
        <div class="card" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); grid-column: 1 / -1;">
          <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #F1F5F9; padding-bottom: 12px; margin-bottom: 16px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #1E293B; margin: 0; display: flex; align-items: center; gap: 8px;">
              <i data-lucide="ticket" style="width: 20px; height: 20px; color: #2563EB;"></i>
              Codes Promo & Offres Spéciales
            </h3>
            <button type="button" onclick="document.getElementById('form-add-promo-card').style.display = document.getElementById('form-add-promo-card').style.display === 'none' ? 'block' : 'none';" class="btn btn-sm btn-outline-primary" style="padding: 6px 14px; font-weight: 700; border-radius: 8px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
              <i data-lucide="plus-circle" style="width: 14px; height: 14px;"></i>
              Ajouter un Code Promo
            </button>
          </div>

          <!-- Formulaire rapide création Code Promo (Collapsible) -->
          <div id="form-add-promo-card" style="display: none; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 18px; margin-bottom: 20px;">
            <h4 style="font-size: 14px; font-weight: 800; color: #1E293B; margin-top: 0; margin-bottom: 14px;">Nouveau Code Promo</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 14px;">
              <div>
                <label style="font-weight: 700; font-size: 12px; color: #1E293B; display: block; margin-bottom: 4px;">Code Promo *</label>
                <input type="text" form="form-promo-add-standalone" name="code_promo" class="form-control" placeholder="ex: LAVEX30" style="text-transform: uppercase; font-weight: 800;" required>
              </div>
              <div>
                <label style="font-weight: 700; font-size: 12px; color: #1E293B; display: block; margin-bottom: 4px;">Type Réduction *</label>
                <select form="form-promo-add-standalone" name="type_reduction" class="form-control" required>
                  <option value="pourcentage">Pourcentage (%)</option>
                  <option value="montant_fixe">Montant Fixe (FCFA)</option>
                </select>
              </div>
              <div>
                <label style="font-weight: 700; font-size: 12px; color: #1E293B; display: block; margin-bottom: 4px;">Valeur Réduction *</label>
                <input type="number" step="0.01" form="form-promo-add-standalone" name="valeur_reduction" class="form-control" placeholder="ex: 20 ou 1000" required>
              </div>
              <div>
                <label style="font-weight: 700; font-size: 12px; color: #1E293B; display: block; margin-bottom: 4px;">Panier Min. (FCFA)</label>
                <input type="number" step="100" form="form-promo-add-standalone" name="montant_minimum_commande" class="form-control" value="0">
              </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 14px;">
              <div>
                <label style="font-weight: 700; font-size: 12px; color: #1E293B; display: block; margin-bottom: 4px;">Titre / Description</label>
                <input type="text" form="form-promo-add-standalone" name="description_promo" class="form-control" placeholder="ex: Offre spéciale">
              </div>
              <div style="display: flex; align-items: center; padding-top: 20px;">
                <label style="font-weight: 700; font-size: 12px; color: #1E293B; display: flex; align-items: center; gap: 6px; cursor: pointer;">
                  <input type="checkbox" form="form-promo-add-standalone" name="premiere_commande_uniquement" value="1" style="width: 16px; height: 16px;">
                  1ère commande uniquement
                </label>
              </div>
            </div>

            <div style="text-align: right;">
              <button type="submit" form="form-promo-add-standalone" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; padding: 8px 18px; font-weight: 700; font-size: 12.5px; border-radius: 8px;">Enregistrer le Code Promo</button>
            </div>
          </div>

          <!-- Tableau des promotions -->
          <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
              <thead>
                <tr style="background: #F8FAFC; color: #64748B; font-size: 11.5px; font-weight: 700; text-transform: uppercase;">
                  <th style="padding: 10px 14px; border-bottom: 1px solid #E2E8F0; text-align: left;">Code Promo</th>
                  <th style="padding: 10px 14px; border-bottom: 1px solid #E2E8F0; text-align: left;">Type & Valeur</th>
                  <th style="padding: 10px 14px; border-bottom: 1px solid #E2E8F0; text-align: left;">Condition</th>
                  <th style="padding: 10px 14px; border-bottom: 1px solid #E2E8F0; text-align: center;">Utilisations</th>
                  <th style="padding: 10px 14px; border-bottom: 1px solid #E2E8F0; text-align: center;">Statut</th>
                  <th style="padding: 10px 14px; border-bottom: 1px solid #E2E8F0; text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($promotions)): ?>
                  <tr>
                    <td colspan="6" style="text-align: center; padding: 24px; color: #94A3B8; font-weight: 600; font-size: 13px;">
                      Aucun code promo configuré pour le moment.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($promotions as $p): ?>
                    <tr style="border-bottom: 1px solid #F1F5F9;">
                      <td style="padding: 12px 14px; font-weight: 800; color: #1E293B; font-size: 13px;">
                        <span style="background: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE; padding: 3px 8px; border-radius: 6px;">
                          <?= htmlspecialchars($p['code_promo']) ?>
                        </span>
                        <?php if (!empty($p['description_promo'])): ?>
                          <div style="font-size: 11px; color: #64748B; font-weight: 400; margin-top: 2px;">
                            <?= htmlspecialchars($p['description_promo']) ?>
                          </div>
                        <?php endif; ?>
                      </td>
                      <td style="padding: 12px 14px; font-weight: 700; font-size: 13px;">
                        <?php if ($p['type_reduction'] === 'pourcentage'): ?>
                          <span style="color: #059669; font-weight: 800;">-<?= number_format($p['valeur_reduction'], 0) ?>%</span>
                        <?php else: ?>
                          <span style="color: #2563EB; font-weight: 800;">-<?= number_format($p['valeur_reduction'], 0, '', ' ') ?> FCFA</span>
                        <?php endif; ?>
                      </td>
                      <td style="padding: 12px 14px; font-size: 12px; color: #475569;">
                        <?php if ($p['premiere_commande_uniquement']): ?>
                          <span style="background: #FEF3C7; color: #D97706; padding: 2px 6px; border-radius: 4px; font-size: 10.5px; font-weight: 700;">1ère Commande</span>
                        <?php endif; ?>
                        <?php if ($p['montant_minimum_commande'] > 0): ?>
                          Min: <?= number_format($p['montant_minimum_commande'], 0, '', ' ') ?> FCFA
                        <?php else: ?>
                          Sans min.
                        <?php endif; ?>
                      </td>
                      <td style="padding: 12px 14px; text-align: center; font-weight: 700; font-size: 13px;">
                        <?= (int)$p['total_utilisations'] ?> fois
                      </td>
                      <td style="padding: 12px 14px; text-align: center;">
                        <?php if ($p['statut_promo'] === 'actif'): ?>
                          <span style="background: #DCFCE7; color: #166534; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700;">Actif</span>
                        <?php else: ?>
                          <span style="background: #F1F5F9; color: #64748B; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700;">Inactif</span>
                        <?php endif; ?>
                      </td>
                      <td style="padding: 12px 14px; text-align: right;">
                        <a href="<?= RACINE ?>promotion/delete/<?= $p['id_promo'] ?>" onclick="return confirm('Supprimer ce code promo ?')" style="color: #DC2626; font-size: 12px; font-weight: 700; text-decoration: none;">
                          Supprimer
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <div style="margin-top: 24px; text-align: right;">
        <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; padding: 12px 24px; font-weight: 700; font-size: 14px; border-radius: 10px; display: inline-flex; align-items: center; gap: 8px;">
          <i data-lucide="save" style="width: 18px; height: 18px;"></i>
          Enregistrer les Paramètres Globaux
        </button>
      </div>
    </form>

    <form id="form-promo-add-standalone" action="<?= RACINE ?>setting/list" method="POST" style="display:none;">
      <input type="hidden" name="action_type" value="add_promo">
    </form>
  </main>
</div>

<?php require_once __DIR__ . '/../../public/inc/footer.php'; ?>
