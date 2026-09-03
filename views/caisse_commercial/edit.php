<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">
      
      <!-- EN-TÊTE DE PAGE -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="wallet" style="color: #1E3A5F; width: 26px; height: 26px;"></i>
            <span>Ma Caisse Journalière & Clôture</span>
          </h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">Tableau de bord de caisse commercial, bilan d'activité et clôture automatique</p>
        </div>
        <div style="display: flex; gap: 10px;">
          <a href="<?= RACINE ?>caisse_commercial/list" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px;">
            <i data-lucide="history" style="width: 18px; height: 18px;"></i> Historique des Caisses
          </a>
        </div>
      </div>

      <!-- LOADER EN ATTENTE -->
      <div id="caisse-loader" style="text-align: center; padding: 50px; background: #FFFFFF; border-radius: 12px; border: 1px solid #E2E8F0;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
          <span class="visually-hidden">Chargement...</span>
        </div>
        <p style="margin-top: 14px; font-weight: 600; color: #64748B;">Chargement du statut de votre caisse...</p>
      </div>

      <!-- CAS 1 : CAISSE ACTUELLEMENT OUVERTE -->
      <div id="section-caisse-ouverte" style="display: none;">
        
        <!-- BANNER STATUT OUVERT -->
        <div class="card" style="background: linear-gradient(135deg, #1E3A5F 0%, #0F172A 100%); color: #FFFFFF; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 14px rgba(30,58,95,0.25);">
          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
              <span class="badge" style="background: #DCFCE7; color: #166534; font-weight: 800; font-size: 12px; padding: 6px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;">
                🟢 CAISSE OUVERTE AUJOURD'HUI
              </span>
              <h2 style="font-size: 20px; font-weight: 800; margin: 10px 0 2px 0;" id="open_session_code">CAISSE-000</h2>
              <div style="font-size: 13px; opacity: 0.85;">
                Ouverte le <strong id="open_session_date">-</strong> à <strong id="open_session_heure">-</strong>
              </div>
            </div>
            <div style="display: flex; gap: 12px;">
              <button type="button" class="btn btn-light btn-open-modal-details" style="font-weight: 700; border-radius: 8px; padding: 10px 18px; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="eye" style="width: 18px; height: 18px; color: #1E3A5F;"></i> Voir Détails des Cotisations
              </button>
            </div>
          </div>
        </div>

        <!-- CARDS DE RÉSUMÉ AUTOMATIQUE -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 24px;">
          <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Recette Totale Collectée</div>
            <div style="font-size: 24px; font-weight: 800; color: #15803D; margin-top: 6px;" id="open_total_general">0 FCFA</div>
            <div style="font-size: 12px; color: #94A3B8; margin-top: 4px;">Calculé automatiquement</div>
          </div>

          <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Nombre de Cotisations</div>
            <div style="font-size: 24px; font-weight: 800; color: #1E3A5F; margin-top: 6px;" id="open_nb_cotisations">0</div>
            <div style="font-size: 12px; color: #94A3B8; margin-top: 4px;">Transactions enregistrées</div>
          </div>

          <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 20px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Fond Initial Déclaré</div>
            <div style="font-size: 24px; font-weight: 800; color: #0F172A; margin-top: 6px;" id="open_fond_initial">0 FCFA</div>
            <div style="font-size: 12px; color: #94A3B8; margin-top: 4px;">Saisi à l'ouverture</div>
          </div>

          <div class="card" style="background: #F8FAFC; border-radius: 12px; padding: 18px; border: 1px solid #E2E8F0;">
            <div style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">Répartition par mode</div>
            <div style="margin-top: 6px; font-size: 13px; font-weight: 700; color: #334155;">
              💵 Espèces : <span id="open_especes" style="color: #0F172A;">0 FCFA</span><br>
              📱 Mobile Money : <span id="open_mobile" style="color: #0F172A;">0 FCFA</span><br>
              🏦 Chèques/Vir. : <span id="open_cheques" style="color: #0F172A;">0 FCFA</span>
            </div>
          </div>
        </div>

        <!-- FORMULAIRE DE CLÔTURE DIRECTE -->
        <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
          <h3 style="font-size: 17px; font-weight: 800; color: #0F172A; margin: 0 0 16px 0;">Procéder à la Clôture de Caisse du Jour</h3>
          
          <form id="form-cloturer-caisse">
            <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">
            
            <div class="form-group" style="margin-bottom: 20px;">
              <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Note / Observations de clôture (Optionnel)</label>
              <textarea class="form-control" name="observations" style="width: 100%; box-sizing: border-box; padding: 12px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1;" placeholder="Ex: Clôture de caisse effectuée sans écart. Monnaie restante au guichet." rows="3"></textarea>
            </div>

            <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; border-top: 1px solid #E2E8F0; padding-top: 20px;">
              <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; font-weight: 800; border-radius: 8px; padding: 12px 28px; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="lock" style="width: 18px; height: 18px;"></i> Clôturer Ma Caisse du Jour
              </button>
              <button type="button" class="btn btn-secondary btn-open-modal-details" style="font-weight: 700; border-radius: 8px; padding: 12px 20px; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="list" style="width: 18px; height: 18px;"></i> Revoir la Liste des Encaissements
              </button>
            </div>
          </form>
        </div>

      </div>

      <!-- CAS 2 : PAS DE CAISSE OUVERTE (AFFICHER BILAN PRÉCÉDENT & OUVERTURE DU JOUR) -->
      <div id="section-caisse-fermee" style="display: none;">
        
        <!-- BANNER STATUT FERMÉ -->
        <div class="card" style="background: #FEF3C7; border: 1.5px solid #FCD34D; border-radius: 12px; padding: 22px; margin-bottom: 24px;">
          <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div>
              <span class="badge" style="background: #B45309; color: #FFFFFF; font-weight: 800; font-size: 12px; padding: 6px 12px; border-radius: 20px; text-transform: uppercase;">
                🔴 PAS DE CAISSE OUVERTE AUJOURD'HUI
              </span>
              <h2 style="font-size: 18px; font-weight: 800; color: #78350F; margin: 10px 0 2px 0;">Votre caisse journalière n'est pas encore activée</h2>
              <div style="font-size: 13px; color: #92400E;">
                Ouvrez votre caisse ci-dessous pour pouvoir collecter des cotisations sur le terrain.
              </div>
            </div>
          </div>
        </div>

        <!-- BILAN DE LA DERNIÈRE SESSION -->
        <div id="card-dernier-bilan" class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: none;">
          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 14px;">
            <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">
              📋 Bilan de votre dernière caisse clôturée
            </h3>
            <button type="button" class="btn btn-sm btn-outline-primary btn-open-modal-details" style="font-weight: 700; border-radius: 8px; padding: 6px 14px; display: inline-flex; align-items: center; gap: 6px;">
              <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Voir Détails des Cotisations
            </button>
          </div>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; background: #F8FAFC; padding: 16px; border-radius: 10px;">
            <div>
              <div style="font-size: 11px; color: #64748B; font-weight: 700;">CODE CLÔTURE</div>
              <div style="font-size: 15px; font-weight: 800; color: #1E3A5F;" id="last_code_lbl">-</div>
            </div>
            <div>
              <div style="font-size: 11px; color: #64748B; font-weight: 700;">DATE DE CAISSE</div>
              <div style="font-size: 15px; font-weight: 800; color: #0F172A;" id="last_date_lbl">-</div>
            </div>
            <div>
              <div style="font-size: 11px; color: #64748B; font-weight: 700;">RECETTE TOTALE</div>
              <div style="font-size: 15px; font-weight: 800; color: #15803D;" id="last_total_lbl">0 FCFA</div>
            </div>
            <div>
              <div style="font-size: 11px; color: #64748B; font-weight: 700;">STATUT VALIDATION FINANCE</div>
              <div style="font-size: 14px; font-weight: 800; margin-top: 2px;" id="last_statut_lbl">-</div>
            </div>
          </div>
        </div>

        <!-- FORMULAIRE D'OUVERTURE RAPIDE -->
        <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
          <h3 style="font-size: 17px; font-weight: 800; color: #0F172A; margin: 0 0 18px 0; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="unlock" style="color: #047857; width: 22px; height: 22px;"></i>
            <span>Effectuer l'Ouverture de Caisse du Jour</span>
          </h3>

          <form id="form-ouvrir-caisse">
            <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 20px;">
              <div>
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Fond de caisse initial (FCFA)</label>
                <input type="number" name="fond_initial" class="form-control" style="width: 100%; box-sizing: border-box; padding: 12px; font-size: 15px; font-weight: 800; border-radius: 8px; border: 1px solid #CBD5E1;" value="0" placeholder="Ex: 0 ou 10000" required>
              </div>
              <div>
                <label style="display: block; font-weight: 700; font-size: 13px; color: #334155; margin-bottom: 6px;">Observations de démarrage</label>
                <input type="text" name="observations" class="form-control" style="width: 100%; box-sizing: border-box; padding: 12px; font-size: 14px; border-radius: 8px; border: 1px solid #CBD5E1;" placeholder="Ex: Prise de poste à 08h00">
              </div>
            </div>

            <div style="border-top: 1px solid #E2E8F0; padding-top: 20px;">
              <button type="submit" class="btn btn-success" style="background: #047857; border-color: #047857; font-weight: 800; border-radius: 8px; padding: 12px 28px; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="unlock" style="width: 18px; height: 18px;"></i> Activer & Ouvrir Ma Caisse du Jour
              </button>
            </div>
          </form>
        </div>

      </div>

    </div>
  </main>
</div>

<!-- MODAL DETAILS DES COTISATIONS (STRUCTURE SYSTÈME GEICG) -->
<div class="modal-overlay" id="modalCotisationsDetails">
  <div class="modal" style="max-width: 900px; width: 92%; background: #FFFFFF; border-radius: 12px; box-shadow: 0 20px 45px rgba(0,0,0,0.25); overflow: hidden;">
    
    <div class="modal-header" style="background: #1E3A5F; color: #FFFFFF; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center;">
      <h3 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; color: #FFFFFF; display: flex; align-items: center; gap: 10px;">
        <i data-lucide="list-checks" style="width: 22px; height: 22px; color: #60A5FA;"></i>
        <span>Procès-Verbal des Cotisations de la Caisse</span>
      </h3>
      <button type="button" class="modal-close" id="modalCotisationsClose" style="background: none; border: none; color: #FFFFFF; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 4px; border-radius: 6px; opacity: 0.85; transition: opacity 0.2s;" title="Fermer">
        <i data-lucide="x" style="width: 22px; height: 22px;"></i>
      </button>
    </div>

    <div class="modal-body" style="padding: 24px; max-height: 72vh; overflow-y: auto;">
      <div style="overflow-x: auto; width: 100%; border: 1px solid #E2E8F0; border-radius: 8px;">
        <table class="table" style="width: 100%; min-width: 700px; border-collapse: collapse; font-size: 13px; margin: 0;">
          <thead>
            <tr style="background: #F8FAFC; color: #475569; text-align: left;">
              <th style="padding: 12px 14px; border-bottom: 2px solid #E2E8F0; font-weight: 700;">Code Souscription</th>
              <th style="padding: 12px 14px; border-bottom: 2px solid #E2E8F0; font-weight: 700;">Client</th>
              <th style="padding: 12px 14px; border-bottom: 2px solid #E2E8F0; font-weight: 700;">Montant</th>
              <th style="padding: 12px 14px; border-bottom: 2px solid #E2E8F0; font-weight: 700;">Mode</th>
              <th style="padding: 12px 14px; border-bottom: 2px solid #E2E8F0; font-weight: 700;">Date Cotisation</th>
              <th style="padding: 12px 14px; border-bottom: 2px solid #E2E8F0; font-weight: 700;">Prochain RDV</th>
              <th style="padding: 12px 14px; border-bottom: 2px solid #E2E8F0; font-weight: 700;">Statut</th>
            </tr>
          </thead>
          <tbody id="tbl_cotisations_body">
            <tr>
              <td colspan="7" style="text-align: center; color: #94A3B8; padding: 30px; font-weight: 600;">
                Aucune cotisation enregistrée pour l'instant dans cette caisse.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="modal-footer" style="background: #F8FAFC; padding: 14px 24px; border-top: 1px solid #E2E8F0; display: flex; justify-content: flex-end;">
      <button type="button" class="btn btn-secondary" id="btn-close-modal-cotisations" style="font-weight: 700; border-radius: 8px; padding: 10px 22px; background: #64748B; color: #FFFFFF; border: none;">Fermer</button>
    </div>

  </div>
</div>

<script src="<?= RACINE ?>public/assets/js/modules/caisse_commercial.js?v=<?= time() ?>"></script>
<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
