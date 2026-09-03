<?php
require_once __DIR__ . '/../../public/inc/header.php';
$stats = isset($stats) ? $stats : ['total' => 0, 'non_lues' => 0, 'lues' => 0, 'commandes' => 0];
$clients = isset($clients) ? $clients : [];
?>

<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>

    <div class="content-wrapper">
      <!-- HEADER DE LA PAGE -->
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 24px; font-weight: 800; color: #1E293B; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="bell" style="color: #2563EB;"></i> Centre de Notifications
          </h1>
          <p class="page-subtitle" style="color: #64748B; margin: 4px 0 0 0;">Historique des alertes In-App et push envoyées aux clients et partenaires</p>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
          <button type="button" class="btn btn-secondary" onclick="markAllNotificationsRead()" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
            <i class="fa fa-check-double"></i> Tout marquer comme lu
          </button>
          <?php if (empty($isLivreur)): ?>
          <button type="button" class="btn btn-primary" onclick="openSendModal()" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700; background: #1E3A5F; border-color: #1E3A5F;">
            <i data-lucide="send" style="width: 16px; height: 16px;"></i> Envoyer une notification
          </button>
          <?php endif; ?>
        </div>
      </div>

      <!-- CARTES KPI NOTIFICATIONS -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <!-- KPI 1 : TOTAL -->
        <div style="background: #FFFFFF; border-radius: 12px; padding: 18px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between;">
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">Total Notifications</span>
            <h2 id="kpi-total" style="font-size: 22px; font-weight: 800; color: #1E293B; margin: 4px 0 0 0;"><?= $stats['total'] ?? 0 ?></h2>
          </div>
          <div style="width: 44px; height: 44px; border-radius: 10px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa fa-bell"></i>
          </div>
        </div>

        <!-- KPI 2 : NON LUES -->
        <div style="background: #FFFFFF; border-radius: 12px; padding: 18px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between;">
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #B45309; text-transform: uppercase;">Non lues</span>
            <h2 id="kpi-nonlues" style="font-size: 22px; font-weight: 800; color: #D97706; margin: 4px 0 0 0;"><?= $stats['non_lues'] ?? 0 ?></h2>
          </div>
          <div style="width: 44px; height: 44px; border-radius: 10px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa fa-envelope"></i>
          </div>
        </div>

        <!-- KPI 3 : LUES -->
        <div style="background: #FFFFFF; border-radius: 12px; padding: 18px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between;">
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #047857; text-transform: uppercase;">Lues & Validées</span>
            <h2 id="kpi-lues" style="font-size: 22px; font-weight: 800; color: #059669; margin: 4px 0 0 0;"><?= $stats['lues'] ?? 0 ?></h2>
          </div>
          <div style="width: 44px; height: 44px; border-radius: 10px; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa fa-check-circle"></i>
          </div>
        </div>

        <!-- KPI 4 : COMMANDES -->
        <div style="background: #FFFFFF; border-radius: 12px; padding: 18px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between;">
          <div>
            <span style="font-size: 11px; font-weight: 700; color: #7C3AED; text-transform: uppercase;">Commandes</span>
            <h2 id="kpi-commandes" style="font-size: 22px; font-weight: 800; color: #7C3AED; margin: 4px 0 0 0;"><?= $stats['commandes'] ?? 0 ?></h2>
          </div>
          <div style="width: 44px; height: 44px; border-radius: 10px; background: #F3E8FF; color: #7C3AED; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa fa-shopping-bag"></i>
          </div>
        </div>
      </div>

      <!-- TABLEAU DES NOTIFICATIONS -->
      <div class="card" style="border-radius: 14px; border: 1px solid #E2E8F0; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div class="mobile-list-container"></div>
        <div class="table-responsive-mobile">
          <table class="table" id="dataTable" style="width: 100%;">
            <thead>
              <tr>
                <th>N°</th>
                <th>Type</th>
                <th>Destinataire</th>
                <th>Titre & Message</th>
                <th>Réf. Associée</th>
                <th>Date & Heure</th>
                <th>État</th>
                <th style="text-align: center;">Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>

    </div>
  </main>
</div>

<!-- MODAL D'ENVOI DE NOTIFICATION -->
<div id="modalSendNotification" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 16px;">
  <div style="background: #FFFFFF; border-radius: 16px; width: 100%; max-width: 520px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden; animation: fadeIn 0.15s ease;">
    
    <div style="padding: 18px 22px; background: #F8FAFC; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center;">
      <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #1E293B; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="send" style="color: #2563EB; width: 18px; height: 18px;"></i> Diffuser une Notification
      </h3>
      <button type="button" onclick="closeSendModal()" style="background: none; border: none; font-size: 18px; color: #94A3B8; cursor: pointer; line-height: 1;">
        <i class="fa fa-times"></i>
      </button>
    </div>

    <form id="formSendNotification" style="padding: 22px;">
      <?= Validator::csrfField() ?>

      <!-- Choix du destinataire -->
      <div class="form-group" style="margin-bottom: 16px;">
        <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">Destinataire *</label>
        <select class="form-control" name="client_code" style="width: 100%;">
          <option value="ALL">Tous les clients (Diffusion globale)</option>
          <?php foreach ($clients as $c): ?>
            <option value="<?= htmlspecialchars($c['code_client'] ?? '') ?>">
              Client: <?= htmlspecialchars($c['nom_client'] ?? '') ?> (<?= htmlspecialchars($c['telephone_client'] ?? '') ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Type de notification -->
      <div class="form-group" style="margin-bottom: 16px;">
        <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">Catégorie d'alerte *</label>
        <select class="form-control" name="type_notification" style="width: 100%;">
          <option value="alerte">Annonce / Alerte Générale</option>
          <option value="promotion">Offre Promotionnelle / Remise</option>
          <option value="systeme">Notification Système / Maintenance</option>
          <option value="commande.info">Information Commande</option>
        </select>
      </div>

      <!-- Titre -->
      <div class="form-group" style="margin-bottom: 16px;">
        <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">Titre de la notification *</label>
        <input type="text" class="form-control" name="titre_notification" placeholder="Ex: Offre Spéciale Pressing Riviera" required>
      </div>

      <!-- Message -->
      <div class="form-group" style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">Message / Corps de l'alerte *</label>
        <textarea class="form-control" name="message_notification" rows="3" placeholder="Écrivez votre message ici..." required></textarea>
      </div>

      <!-- Actions -->
      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn btn-secondary" onclick="closeSendModal()">Annuler</button>
        <button type="submit" class="btn btn-primary" style="background: #1E3A5F; border-color: #1E3A5F; display: inline-flex; align-items: center; gap: 6px; font-weight: 700;">
          <i data-lucide="send" style="width: 16px; height: 16px;"></i> Envoyer maintenant
        </button>
      </div>
    </form>

  </div>
</div>

<script src="<?= RACINE ?>public/json/mobile-list.js"></script>
<script src="<?= RACINE ?>public/json/entities/notifications.js?v=2"></script>
<?php require_once __DIR__ . '/../../public/inc/footer.php'; ?>
