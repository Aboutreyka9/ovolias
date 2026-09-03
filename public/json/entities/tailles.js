$(document).ready(function() {
    const columns = [
        { title: 'N°', data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
        { data: 'code', title: 'Code' },
        { data: 'nom', title: 'Libellé' },
        { data: 'ordre', title: 'Ordre' },
        {
            data: 'statut',
            title: 'Statut',
            render: function(data) {
                const cls = data === 'actif' ? 'delivered' : 'cancelled';
                return '<span class="badge-status ' + cls + '">' + data + '</span>';
            }
        },
        {
            data: null,
            title: 'Actions',
            className: 'text-center',
            render: function(data, type, row) {
                const isActive = row.statut === 'actif';
                return `
                    <div class="table-actions">
                        <a href="${LINK}taille/details/${row.editId}" title="Voir" class="btn-action btn-action-secondary"><i class="fa fa-eye"></i></a>
                        <a href="${LINK}taille/edition/${row.editId}" title="Modifier" class="btn-action btn-action-primary"><i class="fa fa-edit"></i></a>
                        <button type="button" title="${isActive ? 'Désactiver' : 'Activer'}"
                            data-id="${row.id}"
                            class="${isActive ? 'btn-action btn-action-warning changerStatus' : 'btn-action btn-action-success changerStatus'}">
                            <i class="fa ${isActive ? 'fa-toggle-on' : 'fa-toggle-off'}"></i>
                        </button>
                    </div>
                `;
            }
        }
    ];

    const table = initDataTable('dataTable', 'taille/apiList', columns);
    bindStatusToggle('dataTable', 'taille/changer', table);

    $('.formEditTaille').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('.btn_actions');
        loading(btn, true, '<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

        $.ajax({
            url: LINK + 'taille/edit',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(rep) {
                loading(btn, false, '<i class="fa fa-save"></i> Sauvegarder');
                if (rep.status) {
                    showToast(rep.message, 'success');
                    setTimeout(() => window.location.href = LINK + 'taille/list', 700);
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
