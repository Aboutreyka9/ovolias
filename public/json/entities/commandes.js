$(document).ready(function() {
    const baseApi = (typeof LINK !== 'undefined') ? LINK : ((typeof RACINE !== 'undefined') ? RACINE : '/geicg/');

    if ($('#dataTable').length) {
        const columns = [
            { title: 'N°', data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
            { 
                data: 'code', 
                title: 'Code',
                render: function(data, type, row) {
                    return `<strong>#${data}</strong>`;
                }
            },
            {
                data: 'type',
                title: 'Type',
                render: function(data, type, row) {
                    if (data === 'colis') {
                        return `<span style="background: #FEF3C7; color: #92400E; padding: 4px 10px; border-radius: 12px; font-weight: 700; font-size: 11.5px;">${row.type_label || 'Collecte au Sac'}</span>`;
                    }
                    return `<span style="background: #EFF6FF; color: #1E40AF; padding: 4px 10px; border-radius: 12px; font-weight: 700; font-size: 11.5px;">Commande Détaillée</span>`;
                }
            },
            { 
                data: 'client', 
                title: 'Client',
                render: function(data, type, row) {
                    return `<div><strong>${data}</strong><br><small style="color:#64748B;">${row.client_tel || ''}</small></div>`;
                }
            },
            { data: 'pressing', title: 'Pressing' },
            {
                data: 'statut_suivi_label',
                title: 'Étape de suivi',
                render: function(data, type, row) {
                    const st = row.statut_suivi;
                    let bg = '#F1F5F9';
                    let col = '#475569';
                    if (st === 'livree') { bg = '#ECFDF5'; col = '#059669'; }
                    else if (st === 'en_traitement') { bg = '#EFF6FF'; col = '#2563EB'; }
                    else if (st === 'prete' || st === 'en_livraison') { bg = '#F0FDF4'; col = '#16A34A'; }
                    else if (st === 'prix_a_valider' || st === 'collecte_assignee') { bg = '#FEF3C7'; col = '#D97706'; }
                    else if (st === 'refusee' || st === 'annulee') { bg = '#FEF2F2'; col = '#DC2626'; }

                    return `<span style="background: ${bg}; color: ${col}; padding: 4px 10px; border-radius: 8px; font-weight: 700; font-size: 12px; display: inline-block;">${data || st}</span>`;
                }
            },
            {
                data: 'montant',
                title: 'Montant',
                className: 'text-right',
                render: function(data, type, row) {
                    if (row.type === 'colis' && (!data || data == 0)) {
                        return '<span style="color: #D97706; font-size: 12px; font-weight: 600;">Devis à fixer</span>';
                    }
                    return `<strong style="color: #059669;">${new Intl.NumberFormat('fr-FR').format(data)} FCFA</strong>`;
                }
            },
            { data: 'date', title: 'Date' },
            {
                data: null,
                title: 'Actions',
                className: 'text-center',
                render: function(data, type, row) {
                    const isActive = row.statut === 'actif';
                    return `
                        <div class="table-actions" style="display: flex; gap: 4px; justify-content: center;">
                            <a href="${baseApi}commande/details/${row.editId}" title="Détail & Actions" class="btn-action btn-action-secondary" style="color: #2563EB;"><i class="fa fa-eye"></i></a>
                            <a href="${baseApi}commande/edition/${row.editId}" title="Modifier" class="btn-action btn-action-primary"><i class="fa fa-edit"></i></a>
                            <button type="button" title="${isActive ? 'Annuler' : 'Activer'}"
                                data-id="${row.id}"
                                class="${isActive ? 'btn-action btn-action-warning changerStatus' : 'btn-action btn-action-success changerStatus'}">
                                <i class="fa ${isActive ? 'fa-toggle-on' : 'fa-toggle-off'}"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ];

        const table = initDataTable('dataTable', 'commande/apiList', columns);
        bindStatusToggle('dataTable', 'commande/changer', table);

        const commandesMobileConfig = {
            entity: 'commande',
            primary: [{ key: 'code', label: 'Code' }],
            secondary: [{ key: 'client', label: 'Client' }, { key: 'statut_suivi_label', label: 'Suivi' }, { key: 'montant', label: 'Montant' }],
            detailUrl: function(r) { return baseApi + 'commande/details/' + r.editId; },
            actions: [
                { id: 'voir', label: 'Détail 360°', icon: 'eye', href: function(r) { return baseApi + 'commande/details/' + r.editId; } },
                { id: 'modifier', label: 'Modifier', icon: 'edit', href: function(r) { return baseApi + 'commande/edition/' + r.editId; } },
            ],
            getActions: function(row) {
                var list = commandesMobileConfig.actions.map(function(a) { return Object.assign({}, a, { href: a.href(row) }); });
                var isActive = row.statut === 'actif';
                list.push({
                    id: isActive ? 'annuler' : 'activer',
                    label: isActive ? 'Désactiver' : 'Activer',
                    icon: isActive ? 'toggle-left' : 'toggle-right',
                    onClick: function(rowData) {
                        if (typeof showConfirm === 'function') {
                            showConfirm('Changer le statut de cette commande ?', function() {
                                $.post(baseApi + 'commande/changer', { id: rowData.id }, function(rep) {
                                    if (typeof showToast === 'function') showToast(rep.message || 'Statut mis à jour', rep.status ? 'success' : 'error');
                                    $('#dataTable').DataTable().ajax.reload();
                                }, 'json').fail(function() { if (typeof showToast === 'function') showToast('Erreur serveur', 'error'); });
                            });
                        }
                    }
                });
                return list;
            }
        };
        renderMobileCards('dataTable', commandesMobileConfig);
    }

    $('.formEditOrder').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('.btn_actions');
        if (typeof loading === 'function') loading(btn, true, '<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

        $.ajax({
            url: baseApi + 'commande/edit',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(rep) {
                if (typeof loading === 'function') loading(btn, false, '<i class="fa fa-save"></i> Sauvegarder');
                if (rep.status) {
                    if (typeof showToast === 'function') showToast(rep.message, 'success');
                    setTimeout(() => window.location.href = baseApi + 'commande/list', 700);
                } else {
                    if (typeof showToast === 'function') showToast(rep.message, 'error');
                }
            },
            error: function() {
                if (typeof loading === 'function') loading(btn, false, '<i class="fa fa-save"></i> Sauvegarder');
                if (typeof showToast === 'function') showToast('Erreur serveur', 'error');
            }
        });
    });
});
