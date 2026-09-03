$(document).ready(function() {
    if ($('#dataTable').length) {
        const isLivreur = (window.isLivreurUser === true);

        const columns = [
            { title: 'N°', data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
            { data: 'code', title: 'Code' },
            { data: 'commande', title: 'Commande' },
            { data: 'livreur', title: 'Livreur' },
            {
                data: 'type',
                title: 'Type de Mission',
                render: function(data) {
                    const isCollecte = (data || '').toLowerCase() === 'collecte';
                    if (isCollecte) {
                        return '<span style="background:#FEF3C7; color:#B45309; border:1px solid #FCD34D; border-radius:6px; padding:4px 8px; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:5px;"><i class="fa fa-box"></i> Collecte</span>';
                    } else {
                        return '<span style="background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE; border-radius:6px; padding:4px 8px; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:5px;"><i class="fa fa-truck"></i> Livraison</span>';
                    }
                }
            },
            { data: 'adresse', title: 'Adresse' },
            {
                data: 'statut',
                title: 'Statut',
                render: function(data) {
                    const map = { 'en_attente': 'warning', 'en_cours': 'info', 'terminee': 'delivered', 'annulee': 'cancelled' };
                    const cls = map[data] || 'cancelled';
                    const labels = { 'en_attente': 'En attente', 'en_cours': 'En cours', 'terminee': 'Terminée', 'annulee': 'Annulée' };
                    return '<span class="badge-status ' + cls + '">' + (labels[data] || data) + '</span>';
                }
            },
            {
                data: null,
                title: 'Actions',
                className: 'text-center',
                render: function(data, type, row) {
                    const phoneBtn = row.telephone ? `<a href="tel:${row.telephone}" title="Appeler le client" class="btn-action btn-action-success" style="background:#059669; color:#FFF;"><i class="fa fa-phone"></i></a>` : '';
                    const editBtn = isLivreur ? '' : `<a href="${LINK}mission/edition/${row.editId}" title="Modifier" class="btn-action btn-action-secondary"><i class="fa fa-edit"></i></a>`;
                    return `
                        <div class="table-actions" style="display:flex; justify-content:center; gap:6px; flex-wrap:wrap;">
                            <a href="${LINK}mission/carte?mission=${row.code}" title="Lancer le GPS & Guidage Trajet Live" class="btn-action btn-action-primary" style="background:#1E3A5F; color:#FFF;"><i class="fa fa-location-arrow"></i></a>
                            ${phoneBtn}
                            ${editBtn}
                        </div>
                    `;
                }
            }
        ];

        const table = initDataTable('dataTable', 'mission/apiList', columns);

        const mobileActions = [
            { id: 'gps', label: 'Lancer le GPS & Guidage Live', icon: 'navigation', href: function(r) { return LINK + 'mission/carte?mission=' + r.code; } }
        ];

        if (!isLivreur) {
            mobileActions.push({ id: 'modifier', label: 'Modifier', icon: 'edit', href: function(r) { return LINK + 'mission/edition/' + r.editId; } });
        }

        const missionsMobileConfig = {
            entity: 'mission',
            primary: [{ key: 'code', label: 'Mission' }],
            secondary: [{ key: 'commande', label: 'Commande' }, { key: 'type', label: 'Type' }, { key: 'adresse', label: 'Adresse' }],
            detailUrl: isLivreur ? function(r) { return LINK + 'mission/carte?mission=' + r.code; } : function(r) { return LINK + 'mission/edition/' + r.editId; },
            actions: mobileActions,
            getActions: function(row) {
                return mobileActions.map(function(a) { return Object.assign({}, a, { href: a.href(row) }); });
            }
        };
        renderMobileCards('dataTable', missionsMobileConfig);
    }

    $('.formEditMission').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('.btn_actions');
        const idMission = form.find('#id_mission').val();
        const isAdd = !idMission;
        const url = isAdd ? LINK + 'mission/add' : LINK + 'mission/edit';

        loading(btn, true, '<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(rep) {
                loading(btn, false, '<i class="fa fa-save"></i> Sauvegarder');
                if (rep.status) {
                    showToast(rep.message, 'success');
                    setTimeout(() => window.location.href = LINK + 'mission/list', 700);
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
