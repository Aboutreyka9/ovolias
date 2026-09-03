$(document).ready(function() {
    if ($('#dataTable').length) {
        const columns = [
            { title: 'N°', data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
            { data: 'code', title: 'Code' },
            { data: 'commande_code', title: 'Commande' },
            {
                data: 'montant',
                title: 'Montant',
                render: function(data) { return data ? Number(data).toLocaleString('fr-FR') + ' FCFA' : '0 FCFA'; }
            },
            { data: 'mode', title: 'Mode' },
            {
                data: 'statut',
                title: 'Statut',
                render: function(data) {
                    const map = { 'en_attente': 'pending', 'valide': 'delivered', 'annule': 'cancelled' };
                    const cls = map[data] || 'pending';
                    return '<span class="badge-status ' + cls + '">' + data + '</span>';
                }
            },
            { data: 'created_at', title: 'Date' },
            {
                data: null,
                title: 'Actions',
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                        <div class="table-actions">
                            <a href="${LINK}paiement/details/${row.editId}" title="Voir" class="btn-action btn-action-secondary"><i class="fa fa-eye"></i></a>
                            <a href="${LINK}paiement/edition/${row.editId}" title="Modifier" class="btn-action btn-action-primary"><i class="fa fa-edit"></i></a>
                            <button type="button" title="Changer statut"
                                data-id="${row.id}"
                                class="btn-action btn-action-warning changerStatus">
                                <i class="fa fa-refresh"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ];

        const table = initDataTable('dataTable', 'paiement/apiList', columns);
        bindStatusToggle('dataTable', 'paiement/changer', table);

        const paiementsMobileConfig = {
        entity: 'paiement',
        primary: [{ key: 'code', label: 'Code' }],
        secondary: [{ key: 'commande_code', label: 'Commande' }, { key: 'montant', label: 'Montant' }],
        detailUrl: function(r) { return LINK + 'paiement/details/' + r.editId; },
        actions: [
            { id: 'voir', label: 'Voir', icon: 'eye', href: function(r) { return LINK + 'paiement/details/' + r.editId; } },
            { id: 'modifier', label: 'Modifier', icon: 'edit', href: function(r) { return LINK + 'paiement/edition/' + r.editId; } },
        ],
        getActions: function(row) {
            var list = paiementsMobileConfig.actions.map(function(a) { return Object.assign({}, a, { href: a.href(row) }); });
            list.push({
                id: 'statut',
                label: 'Changer statut',
                icon: 'refresh-cw',
                onClick: function(rowData) {
                    showConfirm('Changer le statut de ce paiement ?', function() {
                        $.post(LINK + 'paiement/changer', { id: rowData.id }, function(rep) {
                            showToast(rep.message || 'Statut changé', rep.status ? 'success' : 'error');
                            $('#dataTable').DataTable().ajax.reload();
                        }, 'json').fail(function() { showToast('Erreur serveur', 'error'); });
                    });
                }
            });
            return list;
        }
    };
    renderMobileCards('dataTable', paiementsMobileConfig);
    }

    $('.formEditPaiement').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('.btn_actions');
        loading(btn, true, '<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

        $.ajax({
            url: LINK + 'paiement/edit',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(rep) {
                loading(btn, false, '<i class="fa fa-save"></i> Sauvegarder');
                if (rep.status) {
                    showToast(rep.message, 'success');
                    setTimeout(() => window.location.href = LINK + 'paiement/list', 700);
                } else {
                    showToast(rep.message, 'error');
                }
            },
            error: function() {
                loading(btn, false, '<i class="fa fa-save"></i> Sauvegarder');
                showToast('Erreur serveur', 'error');
            }
        });
    });
});
