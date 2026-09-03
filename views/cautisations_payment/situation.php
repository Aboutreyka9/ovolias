<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>
<?php 
$souscription = $souscription ?? [];

$nomClient = trim($souscription['nom_client'] ?? '');
$telephone = $souscription['telephone_client'] ?? '-';
$genre = $souscription['sexe_client'] ?? '-';
$residence = $souscription['lieu_residence_client'] ?? '-';
$codeClient = $souscription['code_client'] ?? '-';
$email = $sousscription['email_client'] ?? '-';
$profession = $souscription['profession_client'] ?? '-';

$montantTotal = (float)($souscription['montant_total_a_payer'] ?? 0);
$montantPaye = (float)($souscription['montant_total_paye'] ?? 0);
$montantRestant = max(0, $montantTotal - $montantPaye);

$joursTotal = (int)($souscription['nombre_jours_total'] ?? 0);
$joursPayes = (int)($souscription['nombre_jours_payes'] ?? 0);
$joursRestants = max(0, $joursTotal - $joursPayes);

$prixCotisationJournaliere = (float)($souscription['prix_cotisation_pack'] ?? 0);

$codeSouscription = $souscription['code_souscription'] ?? '';
$libelleSession = $souscription['libelle_session'] ?? '-';
$statutSouscription = $souscription['statut_souscription'] ?? '-';
?>
<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>
    <div class="content-wrapper" style="padding: 24px; width: 100%; box-sizing: border-box;">

      <!-- EN-TÊTE -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">
            Paiement Cautisation
            <code style="font-weight: 800; color: #1E3A5F; font-size: 20px; background: #EFF6FF; padding: 3px 10px; border-radius: 6px;"><?= htmlspecialchars($codeSouscription) ?></code>
          </h1>
          <p style="color: #64748B; font-size: 13px; margin: 4px 0 0 0;">
            Client : <strong><?= htmlspecialchars($nomClient) ?></strong>
            &bull; Session : <strong><?= htmlspecialchars($libelleSession) ?></strong>
          </p>
        </div>
        <div style="display: flex; gap: 12px;">
          <a href="<?= RACINE ?>cautisation-payment/search-form" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 8px; padding: 10px 18px; text-decoration: none; color: #475569; border: 1px solid #CBD5E1;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Retour Recherche
          </a>
        </div>
      </div>

      <div class="row" style="display: flex; gap: 20px; flex-wrap: wrap;">
        <!-- COLONNE GAUCHE: Informations du client -->
        <div style="flex: 1; min-width: 280px;">
          <!-- Carte infos client -->
          <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 20px;">
            <h3 style="font-size: 15px; font-weight: 800; color: #1E3A5F; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EFF6FF; padding-bottom: 10px;">
              <i data-lucide="user" style="width: 18px; height: 18px;"></i> Informations du Client
            </h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Nom complet</span>
                <div style="font-size: 17px; font-weight: 800; color: #0F172A; margin-top: 4px;"><?= htmlspecialchars($nomClient) ?></div>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Téléphone</span>
                <div style="font-size: 16px; font-weight: 600; color: #0F172A; margin-top: 4px;">
                  <a href="tel:<?= htmlspecialchars($telephone) ?>" style="color: #1E3A5F;"><?= htmlspecialchars($telephone) ?></a>
                </div>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Genre</span>
                <div style="font-size: 16px; font-weight: 600; color: #0F172A; margin-top: 4px;"><?= htmlspecialchars($genre) ?></div>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Email</span>
                <div style="font-size: 14px; font-weight: 500; color: #334155; margin-top: 4px;"><a href="mailto:<?= htmlspecialchars($email) ?>" style="color: #1E3A5F;"><?= htmlspecialchars($email) ?></a></div>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Code client</span>
                <div style="font-size: 15px; font-weight: 700; color: #1E3A5F; margin-top: 4px;">
                  <code style="background: #F1F5F9; padding: 3px 8px; border-radius: 4px;"><?= htmlspecialchars($codeClient) ?></code>
                </div>
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Profession</span>
                <div style="font-size: 14px; font-weight: 500; color: #334155; margin-top: 4px;"><?= htmlspecialchars($profession) ?></div>
              </div>
              <div style="grid-column: 1 / -1;">
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Lieu de résidence</span>
                <div style="font-size: 14px; font-weight: 500; color: #334155; margin-top: 4px;"><?= htmlspecialchars($residence) ?></div>
              </div>
            </div>
          </div>

          <!-- Carte situation financière -->
          <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="font-size: 15px; font-weight: 800; color: #059669; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #DCFCE7; padding-bottom: 10px;">
              <i data-lucide="coins" style="width: 18px; height: 18px;"></i> Situation Financière Cautisation
            </h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px;">
              <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 10px; padding: 14px;">
                <span style="font-size: 11px; font-weight: 700; color: #1E3A5F; text-transform: uppercase;">Montant total</span>
                <div style="font-size: 20px; font-weight: 800; color: #1E3A5F; margin-top: 4px;"><?= number_format($montantTotal, 0, ',', ' ') ?> FCFA</div>
              </div>
              <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px; padding: 14px;">
                <span style="font-size: 11px; font-weight: 700; color: #15803D; text-transform: uppercase;">Payé</span>
                <div style="font-size: 20px; font-weight: 800; color: #15803D; margin-top: 4px;"><?= number_format($montantPaye, 0, ',', ' ') ?> FCFA</div>
              </div>
              <div style="background: #FEF2F2; border: 1px solid #FECACA; border-radius: 10px; padding: 14px;">
                <span style="font-size: 11px; font-weight: 700; color: #DC2626; text-transform: uppercase;">Restant</span>
                <div style="font-size: 20px; font-weight: 800; color: #DC2626; margin-top: 4px;"><?= number_format($montantRestant, 0, ',', ' ') ?> FCFA</div>
              </div>
              <div style="background: #EEF2FF; border: 1px solid #C7D2FE; border-radius: 10px; padding: 14px;">
                <span style="font-size: 11px; font-weight: 700; color: #1E3A5F; text-transform: uppercase;">Jour (cotisation)</span>
                <div style="font-size: 20px; font-weight: 800; color: #1E3A5F; margin-top: 4px;"><?= number_format($prixCotisationJournaliere, 0, ',', ' ') ?> FCFA</div>
              </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
              <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 16px; text-align: center;">
                <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; display: block; margin-bottom: 4px;">jours total</span>
                <div style="font-size: 24px; font-weight: 800; color: #0F172A;"><?= $joursTotal ?></div>
              </div>
              <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px; padding: 16px; text-align: center;">
                <span style="font-size: 11px; font-weight: 700; color: #15803D; text-transform: uppercase; display: block; margin-bottom: 4px;">jours payés</span>
                <div style="font-size: 24px; font-weight: 800; color: #15803D;"><?= $joursPayes ?></div>
              </div>
              <div style="background: #FEF2F2; border: 1px solid #FECACA; border-radius: 10px; padding: 16px; text-align: center;">
                <span style="font-size: 11px; font-weight: 700; color: #DC2626; text-transform: uppercase; display: block; margin-bottom: 4px;">jours restants</span>
                <div style="font-size: 24px; font-weight: 800; color: #DC2626;"><?= $joursRestants ?></div>
              </div>
            </div>
          </div>
        </div>

        <!-- COLONNE DROITE: Historique des cautisations -->
        <div style="flex: 1; min-width: 280px;">
          <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px 28px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); height: 100%; display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid #EFF6FF;">
              <h3 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="clipboard-list" style="width: 18px; height: 18px; color: #059669;"></i> Liste des cautisations
              </h3>
              <?php if ($montantRestant > 0 && $joursRestants > 0): ?>
                <button class="btn btn-sm" id="paymentBtn" onclick="openPaymentModal()" style="background: #059669; color: white; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 700; font-size: 13px; cursor: pointer;">
                  <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Faire paiement
                </button>
              <?php else: ?>
                <span class="badge" style="background: #DCFCE7; color: #15803D; padding: 8px 14px; border-radius: 8px; font-weight: 700; font-size: 12px;">
                  <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> Soldée
                </span>
              <?php endif; ?>
            </div>

            <div style="overflow-x: auto; flex: 1;">
              <table class="table" style="width: 100%; border-collapse: collapse; font-size: 13px; min-width: 480px;">
                <thead>
                  <tr style="background: #F8FAFC;">
                    <th style="padding: 10px 12px; text-align: left; color: #64748B; font-weight: 700; text-transform: uppercase; font-size: 11px;">Date</th>
                    <th style="padding: 10px 12px; text-align: right; color: #64748B; font-weight: 700; text-transform: uppercase; font-size: 11px;">Montant</th>
                    <th style="padding: 10px 12px; text-align: center; color: #64748B; font-weight: 700; text-transform: uppercase; font-size: 11px;">Jours</th>
                    <th style="padding: 10px 12px; text-align: left; color: #64748B; font-weight: 700; text-transform: uppercase; font-size: 11px;">Mode</th>
                    <th style="padding: 10px 12px; text-align: center; color: #64748B; font-weight: 700; text-transform: uppercase; font-size: 11px;">Statut</th>
                  </tr>
                </thead>
                <tbody id="historyBody">
                  <tr><td colspan="5" class="text-center py-4 text-muted" style="font-size: 13px;">Chargement...</td></tr>
                </tbody>
              </table>
            </div>
        </div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Modal: Formulaire de Paiement -->
<div class="modal-overlay" id="paymentModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 16px;">
  <div style="background: #FFFFFF; border-radius: 16px; width: 100%; max-width: 900px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden; max-height: 90vh; display: flex; flex-direction: column;">
    <div class="modal-header" style="background: #1E3A5F; color: white; border: none; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
      <h5 class="modal-title" style="font-size: 18px; font-weight: 700; margin: 0; color: white; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="wallet" style="width: 20px; height: 20px;"></i> Formulaire de Paiement
      </h5>
      <button type="button" onclick="closePaymentModal()" style="background: none; border: none; color: white; opacity: 0.8; font-size: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 4px;">
        <i data-lucide="x" style="width: 20px; height: 20px; color: white;"></i>
      </button>
    </div>

    <div class="modal-body p-0" style="overflow-y: auto; flex: 1;">
      <!-- Partie supérieure: Récapitulatif -->
      <div style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0; padding: 20px 28px;">
        <h6 style="font-size: 14px; font-weight: 700; color: #1E3A5F; margin: 0 0 12px 0; display: flex; align-items: center; gap: 6px;">
          <i data-lucide="file-text" style="width: 16px; height: 16px;"></i> Récapitulatif de la situation
        </h6>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
          <div>
            <span style="font-size: 11px; font-weight: 600; color: #64748B; text-transform: uppercase;">Client</span>
            <div style="font-size: 16px; font-weight: 700; color: #0F172A; margin-top: 4px;"><?= htmlspecialchars($nomClient) ?></div>
          </div>
          <div>
            <span style="font-size: 11px; font-weight: 600; color: #64748B; text-transform: uppercase;">Code Souscription</span>
            <div style="font-size: 15px; font-weight: 700; color: #1E3A5F; margin-top: 4px;">
              <code style="background: #EFF6FF; padding: 3px 8px; border-radius: 4px;"><?= htmlspecialchars($codeSouscription) ?></code>
            </div>
          </div>
          <div>
            <span style="font-size: 11px; font-weight: 600; color: #64748B; text-transform: uppercase;">Session</span>
            <div style="font-size: 15px; font-weight: 600; color: #334155; margin-top: 4px;"><?= htmlspecialchars($libelleSession) ?></div>
          </div>
          <div>
            <span style="font-size: 11px; font-weight: 600; color: #64748B; text-transform: uppercase;">Montant total</span>
            <div style="font-size: 18px; font-weight: 800; color: #1E3A5F; margin-top: 4px;" id="recap_montant_total"><?= number_format($montantTotal, 0, ',', ' ') ?> FCFA</div>
          </div>
          <div>
            <span style="font-size: 11px; font-weight: 600; color: #64748B; text-transform: uppercase;">Déjà payé</span>
            <div style="font-size: 18px; font-weight: 800; color: #15803D; margin-top: 4px;" id="recap_montant_paye"><?= number_format($montantPaye, 0, ',', ' ') ?> FCFA</div>
          </div>
          <div>
            <span style="font-size: 11px; font-weight: 600; color: #64748B; text-transform: uppercase;">Restant à payer</span>
            <div style="font-size: 18px; font-weight: 800; color: #DC2626; margin-top: 4px;" id="recap_montant_restant"><?= number_format($montantRestant, 0, ',', ' ') ?> FCFA</div>
          </div>
          <div>
            <span style="font-size: 11px; font-weight: 600; color: #64748B; text-transform: uppercase;">Cotisation / jour</span>
            <div style="font-size: 18px; font-weight: 800; color: #059669; margin-top: 4px;" id="recap_prix_jour"><?= number_format($prixCotisationJournaliere, 0, ',', ' ') ?> FCFA</div>
          </div>
          <div>
            <span style="font-size: 11px; font-weight: 600; color: #64748B; text-transform: uppercase;">Jours totaux</span>
            <div style="font-size: 18px; font-weight: 800; color: #0F172A; margin-top: 4px;" id="recap_jours_total"><?= $joursTotal ?></div>
          </div>
          <div>
            <span style="font-size: 11px; font-weight: 600; color: #64748B; text-transform: uppercase;">Jours restants</span>
            <div style="font-size: 18px; font-weight: 800; color: #DC2626; margin-top: 4px;" id="recap_jours_restants"><?= $joursRestants ?></div>
          </div>
        </div>
      </div>

      <!-- Partie inférieure: Formulaire -->
      <div style="padding: 24px 28px;">
        <h6 style="font-size: 14px; font-weight: 700; color: #1E3A5F; margin: 0 0 16px 0; display: flex; align-items: center; gap: 6px;">
          <i data-lucide="edit-3" style="width: 16px; height: 16px;"></i> Informations de paiement
        </h6>

        <div class="row g-4" style="margin-bottom: 16px; display: flex; gap: 16px; flex-wrap: wrap;">
          <div style="flex: 1; min-width: 240px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #1E3A5F; font-size: 13px;">Montant de la cotisation par jour</label>
            <input type="text" id="dailyCotisation" class="form-control" readonly style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 8px; padding: 10px 14px; font-size: 15px; font-weight: 700; color: #059669;" value="<?= number_format($prixCotisationJournaliere, 0, ',', ' ') ?> FCFA">
          </div>
          <div style="flex: 1; min-width: 240px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #1E3A5F; font-size: 13px;">Mode de paiement</label>
            <select class="form-select" id="paymentMode" style="border-radius: 8px; border: 1px solid #CBD5E1; padding: 10px 14px; font-size: 14px; width: 100%;">
              <option value="especes">Espèces</option>
              <option value="mobile_money">Mobile Money</option>
              <option value="cheque">Chèque</option>
              <option value="virement">Virement bancaire</option>
            </select>
          </div>
        </div>

        <div style="margin-bottom: 16px;">
          <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #1E3A5F; font-size: 13px;">Type de paiement</label>
          <div style="display: flex; gap: 20px; margin-bottom: 4px;">
            <label style="font-size: 13px; color: #334155; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
              <input type="radio" name="type_paiement" id="typeMontant" value="montant" checked style="accent-color: #1E3A5F;">
              Par saisie du montant
            </label>
            <label style="font-size: 13px; color: #334155; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
              <input type="radio" name="type_paiement" id="typeJours" value="jours" style="accent-color: #1E3A5F;">
              Par saisie du nombre de jours
            </label>
          </div>
        </div>

        <div style="margin-bottom: 20px; display: flex; gap: 16px; flex-wrap: wrap;">
          <div style="flex: 1; min-width: 240px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #1E3A5F; font-size: 13px;">Montant à verser</label>
            <div style="display: flex;">
              <input type="number" id="montantInput" class="form-control" style="border-radius: 8px 0 0 8px; border: 1px solid #CBD5E1; padding: 10px 14px; font-size: 15px; font-weight: 700; color: #0F172A; width: 100%;" value="<?= (float)$prixCotisationJournaliere ?>" min="0" step="<?= (float)$prixCotisationJournaliere ?>">
              <span style="background: #F8FAFC; border: 1px solid #CBD5E1; border-left: none; border-radius: 0 8px 8px 0; font-weight: 700; color: #64748B; padding: 0 14px; display: flex; align-items: center;">FCFA</span>
            </div>
            <small style="color: #94A3B8; font-size: 11px; display: block; margin-top: 4px;">Doit être un multiple de la cotisation journalière</small>
          </div>
          <div style="flex: 1; min-width: 240px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #1E3A5F; font-size: 13px;">Nombre de jours</label>
            <input type="number" id="joursInput" class="form-control" style="border-radius: 8px; border: 1px solid #CBD5E1; padding: 10px 14px; font-size: 15px; font-weight: 700; color: #0F172A;" min="1" value="1">
          </div>
        </div>

        <div style="margin-bottom: 24px;">
          <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #1E3A5F; font-size: 13px;">Date du prochain rendez-vous</label>
          <input type="text" id="nextAppointment" class="form-control" readonly style="background: #EEF2FF; border: 1px solid #C7D2FE; border-radius: 8px; padding: 10px 14px; font-size: 15px; font-weight: 700; color: #1E3A5F;">
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 16px; border-top: 1px solid #E2E8F0;">
          <button type="button" class="btn" onclick="closePaymentModal()" style="background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1; border-radius: 8px; padding: 10px 24px; font-weight: 700; cursor: pointer;">
            Annuler
          </button>
          <button type="button" class="btn" id="savePaymentBtn" style="background: #1E3A5F; color: white; border: none; border-radius: 8px; padding: 10px 28px; font-weight: 700; cursor: pointer;">
            <i data-lucide="save" style="width: 18px; height: 18px;"></i> Valider le paiement
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const PRIX_COTISATION = <?= $prixCotisationJournaliere ?>;
const MONTANT_RESTANT = <?= $montantRestant ?>;
const JOURS_RESTANTS = <?= $joursRestants ?>;
const CODE_SOUSCRIPTION = '<?= htmlspecialchars($codeSouscription) ?>';

function openPaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.style.display = 'flex';
        if (window.lucide) window.lucide.createIcons();
    }
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

document.getElementById('paymentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});

function formatCurrency(amount) {
    return Number(amount).toLocaleString('fr-FR') + ' FCFA';
}

function calculateNextDate(jours) {
    const now = new Date();
    const next = new Date(now);
    next.setDate(next.getDate() + jours);
    const d = String(next.getDate()).padStart(2, '0');
    const m = String(next.getMonth() + 1).padStart(2, '0');
    const y = next.getFullYear();
    return d + '/' + m + '/' + y;
}

function loadHistory() {
    const tbody = document.getElementById('historyBody');
    if (!tbody) return;

    fetch('<?= RACINE ?>cautisation-payment/history', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'code_souscription=' + encodeURIComponent(CODE_SOUSCRIPTION)
    })
    .then(r => r.json())
    .then(data => {
        if (!data.data || data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4" style="font-style: italic; color: #94A3B8;">Aucune caution enregistrée pour le moment.</td></tr>';
            return;
        }
        tbody.innerHTML = data.data.map(c => {
            let modeLabel = c.mode_paiement || 'especes';
            const modeIcons = {'especes':'💶', 'mobile_money':'📱', 'cheque':'📝', 'virement':'🏦'};
            const modeClean = modeLabel.replace('_', ' ');
            const modeDisplay = (modeIcons[modeLabel] || '💰') + ' ' + modeClean.charAt(0).toUpperCase() + modeClean.slice(1);
            
            const rawStatut = String(c.statut || 'valide').trim().toLowerCase();
            let statutLabel = 'Validé';
            let statutStyle = 'background:#DCFCE7; color:#15803D';
            
            if (rawStatut === 'en attente') {
                statutLabel = 'En attente';
                statutStyle = 'background:#FEF3C7; color:#92400E';
            } else if (rawStatut === 'annule' || rawStatut === 'ennule') {
                statutLabel = 'Annulé';
                statutStyle = 'background:#FEE2E2; color:#DC2626';
            } else if (rawStatut === 'valide' || rawStatut === 'validé') {
                statutLabel = 'Validé';
                statutStyle = 'background:#DCFCE7; color:#15803D';
            } else {
                statutLabel = c.statut || 'Validé';
            }

            return '<tr>' +
                '<td style="padding: 10px 12px; color: #334155; font-weight: 600;">' + (c.date_paiement || '-') + '</td>' +
                '<td style="padding: 10px 12px; text-align: right; font-weight: 700; color: #15803D;">' + formatCurrency(c.montant) + '</td>' +
                '<td style="padding: 10px 12px; text-align: center;"><span style="background:#EFF6FF; color:#1E3A5F; padding:4px 10px; border-radius:6px; font-weight:700; font-size:13px;">' + (c.nombre_jours || 0) + 'j</span></td>' +
                '<td style="padding: 10px 12px; color: #334155;">' + modeDisplay + '</td>' +
                '<td style="padding: 10px 12px; text-align: center;"><span class="badge" style="' + statutStyle + '; padding:6px 12px; border-radius:6px; font-weight:700; font-size:11px; display:inline-block;">' + statutLabel + '</span></td>' +
            '</tr>';
        }).join('');
    })
    .catch(() => {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4" style="color: #94A3B8;">Erreur de chargement</td></tr>';
    });
}

const montantInput = document.getElementById('montantInput');
const joursInput = document.getElementById('joursInput');
const nextAppointment = document.getElementById('nextAppointment');

function updateCalculations() {
    const typeRadio = document.querySelector('input[name="type_paiement"]:checked');
    const type = typeRadio ? typeRadio.value : 'montant';
    let montant, jours;

    if (type === 'montant') {
        montant = parseFloat(montantInput.value) || 0;
        jours = PRIX_COTISATION > 0 ? Math.floor(montant / PRIX_COTISATION) : 0;
        joursInput.value = jours;
    } else {
        jours = parseInt(joursInput.value) || 1;
        montant = jours * PRIX_COTISATION;
        montantInput.value = montant;
    }
    nextAppointment.value = calculateNextDate(jours);
}

montantInput.addEventListener('input', updateCalculations);
joursInput.addEventListener('input', updateCalculations);
document.getElementById('typeMontant').addEventListener('change', updateCalculations);
document.getElementById('typeJours').addEventListener('change', updateCalculations);

document.getElementById('savePaymentBtn').addEventListener('click', function() {
    const montant = parseFloat(montantInput.value) || 0;
    const jours = parseInt(joursInput.value) || 0;
    const mode = document.getElementById('paymentMode').value;
    const typeRadio = document.querySelector('input[name="type_paiement"]:checked');
    const type = typeRadio ? typeRadio.value : 'montant';
// toastr.warning('Le montant doit être supérieur à 0.'); return;
    if (montant <= 0) {
        if (window.toastr) toastr.warning('Le montant doit être supérieur à 0.');
        else alert('Le montant doit être supérieur à 0.');
        return;
    }
    if (jours <= 0) {
        if (window.toastr) toastr.warning('Le nombre de jours doit être supérieur à 0.');
        else alert('Le nombre de jours doit être supérieur à 0.');
        return;
    }
    if (PRIX_COTISATION > 0 && montant % PRIX_COTISATION > 0.01) {
        if (window.toastr) toastr.warning('Le montant doit être un multiple de ' + formatCurrency(PRIX_COTISATION) + '.');
        else alert('Le montant doit être un multiple de ' + formatCurrency(PRIX_COTISATION) + '.');
        return;
    }
    if (montant > MONTANT_RESTANT) {
        if (window.toastr) toastr.warning('Le montant dépasse le montant restant à payer (' + formatCurrency(MONTANT_RESTANT) + ').');
        else alert('Le montant dépasse le montant restant à payer (' + formatCurrency(MONTANT_RESTANT) + ').');
        return;
    }
    if (jours > JOURS_RESTANTS) {
        if (window.toastr) toastr.warning('Le nombre de jours dépasse le nombre de jours restants (' + JOURS_RESTANTS + ' jours).');
        else alert('Le nombre de jours dépasse le nombre de jours restants (' + JOURS_RESTANTS + ' jours).');
        return;
    }

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader-2" class="spinner-border spinner-border-sm"></i> Enregistrement...';

    const formData = new URLSearchParams();
    formData.append('code_souscription', CODE_SOUSCRIPTION);
    formData.append('montant', montant);
    formData.append('nombre_jours', jours);
    formData.append('mode_paiement', mode);
    formData.append('type_paiement', type);

    fetch('<?= RACINE ?>cautisation-payment/savepayment', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString()
    })
    .then(r => r.json())
    .then(result => {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="save" style="width: 18px; height: 18px;"></i> Valider le paiement';

        if (result.status === 1) {
            closePaymentModal();
            loadHistory();
            updateCalculations();
            if (window.toastr) {
                toastr.success('Paiement enregistré avec succès ! Code : ' + result.code_cautisation + ' | Prochain RDV : ' + result.prochain_rdv);
            } else {
                alert('✅ Paiement enregistré avec succès !\n\nCode : ' + result.code_cautisation + '\nProchain RDV : ' + result.prochain_rdv);
            }
            setTimeout(function() { location.reload(); }, 1500);
        } else {
            if (window.toastr) toastr.error(result.message || 'Erreur lors de l\'enregistrement');
            else alert('❌ ' + result.message);
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="save" style="width: 18px; height: 18px;"></i> Valider le paiement';
        if (window.toastr) toastr.error('Erreur : ' + err.message);
        else alert('❌ Erreur : ' + err.message);
    });
});

loadHistory();
updateCalculations();
</script>

<?php require_once __DIR__ . '/../../public/inc/footer-link.php'; ?>
