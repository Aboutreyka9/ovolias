console.log(LINK);
function handleLogin() {
    $('.formConnexion').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        // console.log('[LOGIN] form data:', formData);
        $.ajax({
            url: LINK + 'user/connexion',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                loading('.btnConnexion', true, 'Connexion en cours...');
            },
            success: function(rep) {
                loading('.btnConnexion', false, 'Se connecter');
                if (typeof rep === 'string') {
                    try { rep = JSON.parse(rep); } catch(e) {}
                }
                if (rep && (rep.status === 1 || rep.status === true)) {
                    showToast(rep.message || 'Bienvenue sur GEICG Admin !', 'success');
                    setTimeout(function() {
                        window.location.href = LINK;
                    }, 1000);
                } else {
                    showToast((rep && rep.message) ? rep.message : 'Identifiants incorrects', 'error');
                }
            },
            error: function(xhr, status, error) {
                loading('.btnConnexion', false, 'Se connecter');
                let msg = 'Identifiants ou connexion invalide';
                try {
                    const resp = xhr.responseJSON || JSON.parse(xhr.responseText);
                    if (resp && resp.message) msg = resp.message;
                } catch(e) {}
                showToast(msg, 'error');
            }
        });
    });
}

$(document).ready(function() {
    handleLogin();

    const toggleBtn = document.getElementById('togglePassword');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (!passwordInput || !eyeIcon) return;

            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeIcon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
            if (window.lucide) lucide.createIcons();

            this.setAttribute('aria-label', isPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
        });
    }
    const showPasswordCheckbox = document.getElementById('show-password');
    if (showPasswordCheckbox) {
        showPasswordCheckbox.addEventListener('change', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput) {
                const show = this.checked;
                passwordInput.type = show ? 'text' : 'password';
                if (eyeIcon) {
                    eyeIcon.setAttribute('data-lucide', show ? 'eye-off' : 'eye');
                    if (window.lucide) lucide.createIcons();
                }
            }
        });
    }
});
