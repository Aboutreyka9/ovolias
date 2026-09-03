$(document).ready(function() {
    // 1. Initialisation de la table des rôles
    if ($('#dataTable').length) {
        const columns = [
            { 
                title: 'N°', 
                data: null, 
                render: function(data, type, row, meta) { return meta.row + 1; } 
            },
            { 
                data: 'code', 
                title: 'Code',
                render: function(data) {
                    return '<span class="code-badge">' + (data || '') + '</span>';
                }
            },
            { 
                data: 'libelle', 
                title: 'Libellé Rôle',
                render: function(data) {
                    return '<strong style="color: #1E293B;">' + (data || '') + '</strong>';
                }
            },
            { 
                data: 'description', 
                title: 'Description',
                render: function(data) {
                    return '<span style="color: #64748B; font-size: 13px;">' + (data || '-') + '</span>';
                }
            },
            { 
                data: 'permissions_count', 
                title: 'Permissions',
                render: function(data) {
                    const count = parseInt(data || 0);
                    return '<span style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; font-weight: 700; font-size: 12px; padding: 4px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 5px;"><i class="fa fa-key"></i> ' + count + ' permission' + (count > 1 ? 's' : '') + '</span>';
                }
            },
            {
                data: 'statut',
                title: 'Statut',
                render: function(data) {
                    const isActif = (data === 'actif');
                    const cls = isActif ? 'delivered' : 'cancelled';
                    const label = isActif ? 'Actif' : 'Inactif';
                    return '<span class="badge-status ' + cls + '">' + label + '</span>';
                }
            },
            {
                data: null,
                title: 'Actions',
                className: 'text-center',
                render: function(data, type, row) {
                    const baseApi = (typeof LINK !== 'undefined') ? LINK : ((typeof RACINE !== 'undefined') ? RACINE : '/geicg/');
                    const isActive = (row.statut === 'actif');
                    return `
                        <div class="table-actions">
                            <a href="${baseApi}role/edition/${row.editId}" title="Configurer le rôle et ses permissions" class="btn-action btn-action-primary">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" title="${isActive ? 'Désactiver' : 'Activer'}"
                                data-id="${row.id}"
                                class="${isActive ? 'btn-action btn-action-warning changerStatus' : 'btn-action btn-action-success changerStatus'}">
                                <i class="fa ${isActive ? 'fa-pause' : 'fa-play'}"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ];

        const table = initDataTable('dataTable', 'role/apiList', columns);
        bindStatusToggle('dataTable', 'role/changer', table);

        const rolesMobileConfig = {
            entity: 'role',
            primary: [{ key: 'code', label: 'Code' }, { key: 'libelle', label: 'Libellé' }],
            secondary: [{ key: 'permissions_count', label: 'Permissions' }, { key: 'statut', label: 'Statut' }],
            detailUrl: function(r) { return (typeof LINK !== 'undefined' ? LINK : '/geicg/') + 'role/edition/' + r.editId; },
            actions: [
                { id: 'modifier', label: 'Modifier', icon: 'edit', href: function(r) { return (typeof LINK !== 'undefined' ? LINK : '/geicg/') + 'role/edition/' + r.editId; } }
            ]
        };
        renderMobileCards('dataTable', rolesMobileConfig);
    }

    // 2. Gestion du formulaire d'édition de rôle
    $('#formEditRole').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('.btn_actions');
        const baseApi = (typeof LINK !== 'undefined') ? LINK : ((typeof RACINE !== 'undefined') ? RACINE : '/geicg/');

        if (typeof loading === 'function') loading(btn, true, 'Sauvegarde...');

        $.ajax({
            url: baseApi + 'role/edit',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(rep) {
                if (typeof loading === 'function') loading(btn, false);
                if (rep.status) {
                    if (typeof showToast === 'function') showToast(rep.message || 'Rôle enregistré avec succès !', 'success');
                } else {
                    if (typeof showToast === 'function') showToast(rep.message || 'Erreur lors de l\'enregistrement', 'error');
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

    // 3. Gestion du formulaire des permissions du rôle
    $('#formPermissions').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('.btn_actions');
        const baseApi = (typeof LINK !== 'undefined') ? LINK : ((typeof RACINE !== 'undefined') ? RACINE : '/geicg/');

        if (typeof loading === 'function') loading(btn, true, 'Mise à jour des permissions...');

        $.ajax({
            url: baseApi + 'role/permissions',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(rep) {
                if (typeof loading === 'function') loading(btn, false);
                if (rep.status) {
                    if (typeof showToast === 'function') showToast(rep.message || 'Permissions mises à jour avec succès !', 'success');
                } else {
                    if (typeof showToast === 'function') showToast(rep.message || 'Erreur lors de la mise à jour', 'error');
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

    // Initialiser les compteurs de permissions par groupe
    updateAllGroupCounters();
});

// ==========================================
// FONCTIONS GESTION DES CHECKBOXES & ACCORDÉON
// ==========================================

function updateGroupCounter(groupId) {
    const group = $('#group-' + groupId);
    if (!group.length) return;
    const total = group.find('input[type="checkbox"]').length;
    const checked = group.find('input[type="checkbox"]:checked').length;
    const badge = $('#badge-' + groupId);
    badge.text(checked + ' / ' + total + ' active' + (checked > 1 ? 's' : ''));
    if (checked === total && total > 0) {
        badge.css({ background: '#ECFDF5', color: '#059669', border: '1px solid #A7F3D0' });
    } else if (checked > 0) {
        badge.css({ background: '#EFF6FF', color: '#2563EB', border: '1px solid #BFDBFE' });
    } else {
        badge.css({ background: '#F1F5F9', color: '#64748B', border: '1px solid #E2E8F0' });
    }
}

function updateAllGroupCounters() {
    $('.permission-group-box').each(function() {
        const id = $(this).attr('data-group-id');
        if (id) updateGroupCounter(id);
    });
}

function toggleGroupAccordion(groupId) {
    const body = $('#body-' + groupId);
    const chevron = $('#chevron-' + groupId);
    if (body.is(':visible')) {
        body.slideUp(180);
        chevron.css('transform', 'rotate(0deg)');
    } else {
        body.slideDown(180);
        chevron.css('transform', 'rotate(180deg)');
    }
}

function checkAllGlobal() {
    $('#formPermissions input[type="checkbox"]').prop('checked', true);
    updateAllGroupCounters();
    if (typeof showToast === 'function') showToast('Toutes les permissions ont été cochées', 'info');
}

function uncheckAllGlobal() {
    $('#formPermissions input[type="checkbox"]').prop('checked', false);
    updateAllGroupCounters();
    if (typeof showToast === 'function') showToast('Toutes les permissions ont été décochées', 'info');
}

function expandAllGroups() {
    $('.permission-group-body').slideDown(180);
    $('.toggle-chevron').css('transform', 'rotate(180deg)');
}

function collapseAllGroups() {
    $('.permission-group-body').slideUp(180);
    $('.toggle-chevron').css('transform', 'rotate(0deg)');
}

function checkGroup(groupId, e) {
    if (e) e.stopPropagation();
    $('#group-' + groupId + ' input[type="checkbox"]').prop('checked', true);
    updateGroupCounter(groupId);
}

function uncheckGroup(groupId, e) {
    if (e) e.stopPropagation();
    $('#group-' + groupId + ' input[type="checkbox"]').prop('checked', false);
    updateGroupCounter(groupId);
}

$(document).on('change', '#formPermissions input[type="checkbox"]', function() {
    const groupBox = $(this).closest('.permission-group-box');
    const groupId = groupBox.attr('data-group-id');
    if (groupId) updateGroupCounter(groupId);
});
