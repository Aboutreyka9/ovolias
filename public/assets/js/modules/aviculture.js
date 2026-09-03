/**
 * Module Aviculture & Pesées OVOLIA
 * DataTables, Détection automatique au poids net & Étiquetage
 */

function openBsModal(modalId) {
    const el = document.getElementById(modalId);
    if (!el) return;
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
        modal.show();
    } else if (typeof $(el).modal === 'function') {
        $(el).modal('show');
    }
}

function closeBsModal(modalId) {
    const el = document.getElementById(modalId);
    if (!el) return;
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
        modal.hide();
    } else if (typeof $(el).modal === 'function') {
        $(el).modal('hide');
    }
}

$(document).ready(function () {
    const baseApi = (typeof LINK !== 'undefined') ? LINK : ((typeof RACINE !== 'undefined') ? RACINE : '/ovolias/');

    // -------------------------------------------------------------
    // 1. Initialisation DataTables Registre des Pesées
    // -------------------------------------------------------------
    let dtPesees = null;
    if ($('#tablePesees').length) {
        dtPesees = $('#tablePesees').DataTable({
            processing: true,
            ajax: {
                url: baseApi + 'aviculture/apiListPesees',
                type: 'GET',
                dataSrc: 'data'
            },
            columns: [
                {
                    data: 'code_etiquette',
                    render: function (data) {
                        return `<code style="font-weight:700; color:#334155; background:#F1F5F9; padding:2px 6px; border-radius:4px;">${data || '-'}</code>`;
                    }
                },
                {
                    data: 'libelle_produit',
                    render: function (data) {
                        return `<strong style="color:#0F172A;"><i data-lucide="feather" style="width:14px; height:14px; color:#047857; margin-right:4px;"></i>${data || 'Poulet entiers frais'}</strong>`;
                    }
                },
                {
                    data: 'libelle_categorie_poids',
                    render: function (data) {
                        const badges = {
                            'ESSENTIEL': '#10B981',
                            'CLASSIQUE': '#059669',
                            'GRAND': '#0284C7',
                            'EXTRA': '#2563EB',
                            'SIGNATURE': '#7C3AED',
                            'PRESTIGE': '#DC2626'
                        };
                        const bg = badges[data] || '#64748B';
                        return `<span class="badge fw-bold px-2 py-1" style="background:${bg}; color:#fff; border-radius:6px;">${data || 'ESSENTIEL'}</span>`;
                    }
                },
                {
                    data: 'poids_net_reel',
                    render: function (data) {
                        const val = parseFloat(data) || 0;
                        return `<strong style="color:#059669; font-size:14px;">${val.toFixed(3).replace('.', ',')} kg</strong>`;
                    }
                },
                {
                    data: 'prix_unitaire_applique',
                    render: function (data) {
                        const val = parseFloat(data) || 0;
                        return `<strong style="color:#0F172A;">${val.toLocaleString('fr-FR')} FCFA</strong>`;
                    }
                },
                {
                    data: 'numero_lot',
                    render: function (data) {
                        return `<span style="color:#64748B; font-family:monospace; font-size:12px;">${data || '-'}</span>`;
                    }
                },
                {
                    data: 'date_pesee',
                    render: function (data) {
                        if (!data) return '-';
                        const d = new Date(data);
                        return d.toLocaleDateString('fr-FR') + ' ' + d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                    }
                },
                {
                    data: 'agent_nom',
                    render: function (data) {
                        return `<span style="color:#64748B; font-size:12px;">${data || 'Agent'}</span>`;
                    }
                },
                {
                    data: 'statut_pesee',
                    render: function (data) {
                        if (data === 'en_stock') return `<span style="background:#DCFCE7; color:#166534; font-size:12px; font-weight:700; padding:3px 8px; border-radius:12px;">En Stock</span>`;
                        if (data === 'distribue') return `<span style="background:#DBEAFE; color:#1E40AF; font-size:12px; font-weight:700; padding:3px 8px; border-radius:12px;">Distribué</span>`;
                        if (data === 'reserve_pack') return `<span style="background:#FEF3C7; color:#92400E; font-size:12px; font-weight:700; padding:3px 8px; border-radius:12px;">Réservé Pack</span>`;
                        return `<span style="background:#F1F5F9; color:#475569; font-size:12px; font-weight:700; padding:3px 8px; border-radius:12px;">${data}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-sm btn-outline-dark me-1 btn-print-label" data-code="${row.code_etiquette}" title="Imprimer Étiquette" style="border-radius:6px; font-weight:600;">
                                🖨️ Étiquette
                            </button>
                        `;
                    }
                }
            ],
            language: {
                url: baseApi + 'json/datatables-i18n-fr-FR.json'
            },
            drawCallback: function () {
                if (window.lucide) lucide.createIcons();
            }
        });
    }

    // -------------------------------------------------------------
    // 2. Réinitialisation à l'ouverture de la Modal de Pesée
    // -------------------------------------------------------------
    $('#btnOpenPeseeModal').on('click', function () {
        if ($('#formPesee').length) {
            $('#formPesee')[0].reset();
        }
        $('#previewCategory').text('-- En attente de saisie --').css('background', '#0F172A');
        $('#previewWeight').text('0,000 kg');
        $('#previewPrice').text('0 FCFA');
        openBsModal('modalPesee');
    });

    // -------------------------------------------------------------
    // 3. Calcul en temps réel de la catégorie d'après le poids net
    // -------------------------------------------------------------
    $('#inputPoidsNet').on('input keyup change', function () {
        const p = parseFloat($(this).val()) || 0;
        $('#previewWeight').text(p.toFixed(3).replace('.', ',') + ' kg');

        if (p <= 0) {
            $('#previewCategory').text('-- En attente de saisie --').css('background', '#0F172A');
            $('#previewPrice').text('0 FCFA');
            return;
        }

        let cat = 'ESSENTIEL';
        let prix = 2500;
        let color = '#10B981';

        // Si la liste des catégories est chargée depuis la base de données
        if (window.CATEGORIES_POIDS && Array.isArray(window.CATEGORIES_POIDS) && window.CATEGORIES_POIDS.length > 0) {
            let matched = window.CATEGORIES_POIDS.find(c => p >= parseFloat(c.poids_min) && p <= parseFloat(c.poids_max));
            if (!matched) {
                // Trouver la plus proche
                matched = window.CATEGORIES_POIDS[0];
            }
            if (matched) {
                cat = matched.libelle_categorie_poids || matched.code_categorie_poids;
                prix = parseFloat(matched.prix_vente_defaut) || 2500;
            }
        } else {
            // Barème par défaut OVOLIA
            if (p >= 1.20 && p <= 1.39) {
                cat = 'ESSENTIEL';
                prix = 2500;
                color = '#10B981';
            } else if (p >= 1.40 && p <= 1.59) {
                cat = 'CLASSIQUE';
                prix = 3000;
                color = '#059669';
            } else if (p >= 1.60 && p <= 1.79) {
                cat = 'GRAND';
                prix = 3500;
                color = '#0284C7';
            } else if (p >= 1.80 && p <= 1.99) {
                cat = 'EXTRA';
                prix = 4000;
                color = '#2563EB';
            } else if (p >= 2.00 && p <= 2.29) {
                cat = 'SIGNATURE';
                prix = 4500;
                color = '#7C3AED';
            } else if (p >= 2.30) {
                cat = 'PRESTIGE';
                prix = 5000;
                color = '#DC2626';
            } else if (p < 1.20) {
                cat = 'ESSENTIEL (<1,2 kg)';
                prix = 2500;
                color = '#64748B';
            }
        }

        $('#previewCategory').text('CATÉGORIE ' + cat).css('background', color);
        $('#previewPrice').text(prix.toLocaleString('fr-FR') + ' FCFA');
    });

    // -------------------------------------------------------------
    // 4. Soumission Ajax du Formulaire de Pesée
    // -------------------------------------------------------------
    $('#formPesee').on('submit', function (e) {
        e.preventDefault();

        const $btn = $('#btnSubmitPesee');
        $btn.prop('disabled', true).html('⏳ Enregistrement...');

        $.ajax({
            url: baseApi + 'aviculture/addPesee',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false).html('🖨️ Enregistrer & Générer Étiquette');

                if (res.status === 'success' || res.success) {
                    if (typeof showToast === 'function') {
                        showToast(res.message || 'Pesée enregistrée !', 'success');
                    } else if (typeof toastr !== 'undefined') {
                        toastr.success(res.message || 'Pesée enregistrée !');
                    } else {
                        alert(res.message || 'Pesée enregistrée !');
                    }

                    closeBsModal('modalPesee');
                    if (dtPesees) dtPesees.ajax.reload(null, false);

                    // Afficher automatiquement l'étiquette produite pour impression
                    if (res.data && res.data.code_etiquette) {
                        openEtiquetteModal(res.data.code_etiquette);
                    }
                } else {
                    const msg = res.message || 'Erreur lors de la pesée.';
                    if (typeof showToast === 'function') showToast(msg, 'error');
                    else alert(msg);
                }
            },
            error: function () {
                $btn.prop('disabled', false).html('🖨️ Enregistrer & Générer Étiquette');
                alert('Erreur serveur lors de la soumission.');
            }
        });
    });

    // -------------------------------------------------------------
    // 5. Action d'Impression d'Étiquette depuis le tableau
    // -------------------------------------------------------------
    $(document).on('click', '.btn-print-label', function () {
        const code = $(this).data('code');
        if (code) {
            openEtiquetteModal(code);
        }
    });

    function openEtiquetteModal(codeEtiquette) {
        const printUrl = baseApi + 'aviculture/etiquettePrint/' + codeEtiquette;
        $('#iframeEtiquette').attr('src', printUrl);
        openBsModal('modalEtiquettePrint');
    }
});
