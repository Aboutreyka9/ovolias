<?php 
require_once __DIR__ . '/../../public/inc/header.php'; 
$user = isset($user) ? $user : ($_SESSION[USERS_AUTH] ?? []);
?>

<div class="app-layout">
  <?php require_once __DIR__ . '/../../public/inc/sidbar.php'; ?>
  <main class="main-content">
    <?php require_once __DIR__ . '/../../public/inc/nav.php'; ?>

    <div class="content-wrapper">
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
          <h1 style="font-size: 24px; font-weight: 800; color: #1E293B; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="lock" style="color: #2563EB;"></i> Modifier mon mot de passe
          </h1>
          <p class="page-subtitle" style="color: #64748B; margin: 4px 0 0 0;">Sécurisez votre compte avec un mot de passe robuste</p>
        </div>
        <a href="<?= RACINE ?>user/profil" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700;">
          <i class="fa fa-arrow-left"></i> Retour au profil
        </a>
      </div>

      <div class="card" style="border-radius: 16px; border: 1px solid #E2E8F0; padding: 28px; max-width: 540px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); background: #FFFFFF;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #E2E8F0;">
          <div style="width: 44px; height: 44px; border-radius: 12px; background: #EFF6FF; display: flex; align-items: center; justify-content: center; color: #2563EB; font-size: 20px;">
            <i class="fa fa-shield-alt"></i>
          </div>
          <div>
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #1E293B;">Sécurité du compte</h3>
            <p style="margin: 2px 0 0 0; font-size: 13px; color: #64748B;">Compte : <strong><?= htmlspecialchars($user['nom'] ?? ($user['nom_user'] ?? 'Utilisateur')) ?></strong></p>
          </div>
        </div>

        <form id="formEditPassword">
          <?= Validator::csrfField() ?>

          <div class="form-group" style="margin-bottom: 20px;">
            <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px;">Mot de passe actuel *</label>
            <div style="position: relative;">
              <input type="password" class="form-control" id="old_password" name="old_password" required placeholder="Votre mot de passe actuel" style="padding-right: 42px;">
              <button type="button" class="btn-toggle-pwd" data-target="old_password" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94A3B8; cursor: pointer; padding: 4px;">
                <i class="fa fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="form-group" style="margin-bottom: 20px;">
            <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px;">Nouveau mot de passe *</label>
            <div style="position: relative;">
              <input type="password" class="form-control" id="new_password" name="new_password" required placeholder="Nouveau mot de passe" style="padding-right: 42px;">
              <button type="button" class="btn-toggle-pwd" data-target="new_password" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94A3B8; cursor: pointer; padding: 4px;">
                <i class="fa fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="form-group" style="margin-bottom: 24px;">
            <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px;">Confirmer le nouveau mot de passe *</label>
            <div style="position: relative;">
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Répétez le nouveau mot de passe" style="padding-right: 42px;">
              <button type="button" class="btn-toggle-pwd" data-target="confirm_password" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94A3B8; cursor: pointer; padding: 4px;">
                <i class="fa fa-eye"></i>
              </button>
            </div>
          </div>

          <div style="background: #F8FAFC; border-radius: 10px; padding: 14px 16px; margin-bottom: 24px; border: 1px solid #E2E8F0;">
            <div style="font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
              <i class="fa fa-info-circle" style="color: #2563EB;"></i> Conseils de sécurité
            </div>
            <ul style="margin: 0; padding-left: 18px; font-size: 12px; color: #64748B; line-height: 1.6;">
              <li>Utilisez au moins 6 caractères variés.</li>
              <li>Évitez d'utiliser le même mot de passe que sur d'autres sites.</li>
            </ul>
          </div>

          <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="<?= RACINE ?>user/profil" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary btn_actions" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; background: #1E3A5F; border-color: #1E3A5F;">
              <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i> Mettre à jour mon mot de passe
            </button>
          </div>
        </form>
      </div>

    </div>
  </main>
</div>

<script>
$(document).ready(function() {
    const baseApi = (typeof LINK !== 'undefined') ? LINK : ((typeof RACINE !== 'undefined') ? RACINE : '/admin-lavex/');

    // Toggle afficher/masquer mot de passe
    $(document).on('click', '.btn-toggle-pwd', function() {
        const targetId = $(this).data('target');
        const input = $('#' + targetId);
        const icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Soumission du formulaire
    $('#formEditPassword').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('.btn_actions');
        const newPwd = $('#new_password').val();
        const confirmPwd = $('#confirm_password').val();

        if (newPwd !== confirmPwd) {
            if (typeof showToast === 'function') showToast('Les nouveaux mots de passe ne correspondent pas !', 'error');
            return;
        }

        if (typeof loading === 'function') loading(btn, true, 'Mise à jour en cours...');

        $.ajax({
            url: baseApi + 'user/editPassword',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(rep) {
                if (typeof loading === 'function') loading(btn, false);
                if (rep.status) {
                    if (typeof showToast === 'function') showToast(rep.message || 'Mot de passe modifié avec succès !', 'success');
                    form[0].reset();
                    setTimeout(function() {
                        window.location.href = baseApi + 'user/profil';
                    }, 1000);
                } else {
                    if (typeof showToast === 'function') showToast(rep.message || 'Erreur lors de la modification', 'error');
                }
            },
            error: function(xhr) {
                if (typeof loading === 'function') loading(btn, false);
                let msg = 'Erreur serveur';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                if (typeof showToast === 'function') showToast(msg, 'error');
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../public/inc/footer.php'; ?>
