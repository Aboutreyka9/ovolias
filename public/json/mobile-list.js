function renderMobileCards(tableId, config) {
    const table = $(`#${tableId}`).DataTable();
    const cardContainer = $(`#${tableId}`).closest('.card').find('.mobile-list-container');
    const actionSheet = $('#mobileActionSheet');
    const actionSheetContent = $('#mobileActionsContent');
    const actionOverlay = $('#mobileActionOverlay');

    if (!cardContainer.length || !actionSheet.length) return;

    function getRowValue(row, key) {
        if (key === '_index') return row.index() + 1;
        return row.data()[key];
    }

    function getRowDisplay(field, row) {
        const key = field.key || field;
        const value = getRowValue(row, key);
        if (field.render && typeof field.render === 'function') {
            return field.render(value, 'display');
        }
        if (value === null || value === undefined) return '-';
        return value;
    }

    function buildCard(row) {
        const primaryHtml = (config.primary || []).map(function(f) {
            const label = f.label || f.key || f;
            const value = getRowDisplay(f, row);
            return '<div class="mobile-item-primary">' +
                '<span class="mobile-item-label">' + label + '</span>' +
                '<span class="mobile-item-value">' + value + '</span>' +
            '</div>';
        }).join('');

        const secondaryHtml = (config.secondary || []).map(function(f) {
            const label = f.label || f.key || f;
            const value = getRowDisplay(f, row);
            return '<small class="mobile-item-secondary">' + label + ': ' + value + '</small>';
        }).join('');

        const summaryHtml = secondaryHtml ? '<div class="mobile-item-meta">' + secondaryHtml + '</div>' : '';

        return '<div class="mobile-item" data-row-index="' + row.index() + '">' +
            '<div class="mobile-item-body">' +
                '<div>' + primaryHtml + '</div>' +
                summaryHtml +
            '</div>' +
            '<button type="button" class="mobile-actions-toggle" aria-label="Actions">' +
                '<i data-lucide="chevron-down" style="width:18px;height:18px;"></i>' +
            '</button>' +
        '</div>';
    }

    function updateView() {
        const rows = table.rows({ search: 'applied' });
        let html = '';
        rows.every(function(rowIdx) {
            html += buildCard(table.row(rowIdx));
        });
        cardContainer.html(html);

        cardContainer.off('click', '.mobile-actions-toggle').on('click', '.mobile-actions-toggle', function(e) {
            e.stopPropagation();
            var mobileItem = $(this).closest('.mobile-item');
            var idx = mobileItem.data('row-index');
            var row = table.row(idx);
            var rowData = row.data();
            var actions = typeof config.getActions === 'function' ? config.getActions(rowData) : (config.actions || []);
            var sheetHtml = '';
            actions.forEach(function(action) {
                var href = action.href ? ' href="' + action.href + '"' : '';
                var attrs = href ? '' : ' type="button"';
                var cls = 'mobile-actions-item ' + (action.class || '');
                var label = action.label || 'Action';
                var icon = action.icon ? '<i data-lucide="' + action.icon + '" class="mobile-action-icon"></i> ' : '';
                if (href) {
                    sheetHtml += '<a ' + href + ' class="' + cls + '">' + icon + label + '</a>';
                } else {
                    sheetHtml += '<button ' + attrs + ' class="' + cls + '" data-action="' + (action.id || label) + '">' + icon + label + '</button>';
                }
            });
            actionSheetContent.html(sheetHtml);
            if (typeof lucide !== 'undefined') lucide.createIcons();
            actionOverlay.addClass('active');
            actionSheet.addClass('active');

            actionSheetContent.off('click', 'button[data-action]').on('click', 'button[data-action]', function() {
                var actionId = $(this).data('action');
                var found = actions.find(function(a) { return (a.id || a.label) === actionId; });
                if (found && found.onClick) {
                    found.onClick(rowData, row);
                }
                closeMobileActionSheet();
            });
        });

        cardContainer.off('click', '.mobile-item').on('click', '.mobile-item', function() {
            var idx = $(this).data('row-index');
            var rowData = table.row(idx).data();
            var url = typeof config.detailUrl === 'function' ? config.detailUrl(rowData) : null;
            if (url) {
                window.location.href = url;
            }
        });

        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    window.closeMobileActionSheet = function() {
        actionOverlay.removeClass('active');
        actionSheet.removeClass('active');
    };

    actionOverlay.on('click', closeMobileActionSheet);

    const handleUpdate = function() {
        updateView();
        if (typeof config.onDraw === 'function') config.onDraw(table);
    };

    table.on('init.dt', handleUpdate);
    table.on('draw', handleUpdate);

    handleUpdate();
}
