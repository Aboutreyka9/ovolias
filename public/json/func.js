function loading(selector, status, message) {
    const el = typeof selector === 'string' ? $(selector) : $(selector);
    if (!el || !el.length) return;

    if (!document.getElementById('globalSpinnerStyle')) {
        const style = document.createElement('style');
        style.id = 'globalSpinnerStyle';
        style.innerHTML = `
            @keyframes lvxSpin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            .lvx-spinner-icon { animation: lvxSpin 0.75s linear infinite; vertical-align: -2px; margin-right: 6px; display: inline-block; }
            .btn-is-loading { opacity: 0.75 !important; cursor: not-allowed !important; pointer-events: none !important; }
        `;
        document.head.appendChild(style);
    }

    const spinnerSvg = '<svg class="lvx-spinner-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/></svg>';

    if (status) {
        const cleanMsg = (message || 'Patientez...').replace(/<i class="[^"]*"><\/i>/g, '').trim();
        const htmlContent = spinnerSvg + '<span>' + cleanMsg + '</span>';
        if (el.find('.btn-text').length) {
            el.find('.btn-text').html(htmlContent);
        } else {
            el.html(htmlContent);
        }
        el.prop('disabled', true).addClass('btn-is-loading');
    } else {
        const resetMsg = message || el.data('original-html') || 'Valider';
        if (el.find('.btn-text').length) {
            el.find('.btn-text').html(resetMsg);
        } else {
            el.html(resetMsg);
        }
        el.prop('disabled', false).removeClass('btn-is-loading');
    }
}

function showToast(message, type = 'success', title = null) {
    if (!message) return;

    if (!document.getElementById('toastProStyles')) {
        const styleEl = document.createElement('style');
        styleEl.id = 'toastProStyles';
        styleEl.innerHTML = `
            @keyframes toastProSlideDown {
                from { opacity: 0; transform: translateY(-24px) scale(0.94); }
                to { opacity: 1; transform: translateY(0) scale(1); }
            }
        `;
        document.head.appendChild(styleEl);
    }

    let container = document.querySelector('.js-toast-container-pro');
    if (!container) {
        container = document.createElement('div');
        container.className = 'js-toast-container-pro';
        container.style.cssText = 'position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999999; display: flex; flex-direction: column; gap: 10px; max-width: 480px; width: 92%; pointer-events: none;';
        document.body.appendChild(container);
    }

    const duplicate = [...container.querySelectorAll('.toast__message')]
        .find(el => el.textContent.trim() === message.trim());
    if (duplicate) return;

    const theme = {
        success: { bg: '#ECFDF5', border: '#6EE7B7', color: '#065F46', title: 'Succès !', icon: '<svg width="20" height="20" fill="none" stroke="#10B981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' },
        error: { bg: '#FEF2F2', border: '#FCA5A5', color: '#991B1B', title: 'Attention', icon: '<svg width="20" height="20" fill="none" stroke="#EF4444" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>' },
        warning: { bg: '#FFFBEB', border: '#FCD34D', color: '#92400E', title: 'Avertissement', icon: '<svg width="20" height="20" fill="none" stroke="#F59E0B" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' },
        info: { bg: '#EFF6FF', border: '#93C5FD', color: '#1E40AF', title: 'Information', icon: '<svg width="20" height="20" fill="none" stroke="#3B82F6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' }
    }[type] || { bg: '#FFFFFF', border: '#E2E8F0', color: '#1E293B', title: 'Notification', icon: '' };

    const toast = document.createElement('div');
    toast.className = `toast-pro toast-pro--${type}`;
    toast.style.cssText = `background: ${theme.bg}; border: 1.5px solid ${theme.border}; border-radius: 14px; padding: 14px 18px; box-shadow: 0 16px 32px rgba(15,23,42,0.14), 0 4px 10px rgba(0,0,0,0.05); display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; pointer-events: auto; animation: toastProSlideDown 0.3s cubic-bezier(0.16, 1, 0.3, 1); width: 100%; transition: all 0.25s ease;`;

    const displayTitle = title || theme.title;

    toast.innerHTML = `
        <div style="font-size: 20px; line-height: 1; flex-shrink: 0; margin-top: 2px;">${theme.icon}</div>
        <div style="flex: 1; display: flex; flex-direction: column; gap: 2px;">
            <div style="font-size: 13px; font-weight: 800; color: ${theme.color}; text-transform: uppercase; letter-spacing: 0.4px;">${displayTitle}</div>
            <div class="toast__message" style="font-size: 13.5px; font-weight: 600; color: #1E293B; line-height: 1.45;">${message}</div>
        </div>
        <button type="button" class="toast__close" aria-label="Fermer" style="background: none; border: none; font-size: 20px; color: ${theme.color}; cursor: pointer; padding: 0 4px; line-height: 1; opacity: 0.75;">&times;</button>
    `;

    container.appendChild(toast);

    const removeToast = () => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-16px)';
        setTimeout(() => { toast.remove(); }, 250);
    };

    toast.querySelector('.toast__close').addEventListener('click', removeToast);
    setTimeout(removeToast, type === 'error' ? 6500 : 4000);
}

function showConfirm(message, callback, title = 'Confirmation', confirmText = 'Confirmer', isDanger = false) {
    // Supprimer tout ancien modal de confirmation ouvert
    $('.custom-confirm-overlay').remove();

    const overlay = document.createElement('div');
    overlay.className = 'custom-confirm-overlay';
    overlay.style.cssText = 'position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); z-index: 999999; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 16px; animation: fadeIn 0.15s ease;';

    const btnColor = isDanger ? 'background: #DC2626; color: #FFFFFF;' : 'background: #2563EB; color: #FFFFFF;';

    overlay.innerHTML = `
        <div style="background: #FFFFFF; border-radius: 16px; width: 100%; max-width: 420px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden; animation: popIn 0.15s ease;">
            <div style="padding: 18px 20px; background: #F8FAFC; border-bottom: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between;">
                <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: #1E293B; display: flex; align-items: center; gap: 8px;">
                    <i class="fa ${isDanger ? 'fa-exclamation-triangle' : 'fa-question-circle'}" style="color: ${isDanger ? '#DC2626' : '#2563EB'};"></i> ${title}
                </h3>
                <button type="button" class="btn-close-confirm" style="background: none; border: none; font-size: 18px; color: #94A3B8; cursor: pointer; line-height: 1;">&times;</button>
            </div>
            <div style="padding: 20px; color: #334155; font-size: 14px; line-height: 1.5;">
                ${message}
            </div>
            <div style="padding: 14px 20px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-sm btn-secondary btn-cancel-confirm" style="padding: 8px 14px; border-radius: 8px; font-weight: 600; cursor: pointer;">Annuler</button>
                <button type="button" class="btn btn-sm btn-action-ok" style="padding: 8px 16px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer; ${btnColor}">${confirmText}</button>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    const closeOverlay = () => {
        overlay.style.opacity = '0';
        overlay.style.transition = 'opacity 0.15s ease';
        setTimeout(() => { overlay.remove(); }, 150);
    };

    overlay.querySelector('.btn-close-confirm').addEventListener('click', closeOverlay);
    overlay.querySelector('.btn-cancel-confirm').addEventListener('click', closeOverlay);
    overlay.querySelector('.btn-action-ok').addEventListener('click', () => {
        closeOverlay();
        if (typeof callback === 'function') callback();
    });

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeOverlay();
    });
}
