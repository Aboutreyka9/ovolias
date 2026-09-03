<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?= RACINE ?>public/json/func.js?v=<?= time() ?>"></script>
<script src="<?= RACINE ?>public/json/validator.js?v=<?= time() ?>"></script>
<script src="<?= RACINE ?>public/json/app.js?v=<?= time() ?>"></script>
<script src="<?= RACINE ?>public/json/auth.js?v=<?= time() ?>"></script>
<script src="<?= RACINE ?>public/json/theme-manager.js?v=<?= time() ?>"></script>
<script>
    try {
        lucide.createIcons();
    } catch(e) {
        console.warn('lucide error:', e);
    }
    window.addEventListener('error', function(e) {
        console.error('[global error]', e.message, 'at', e.filename + ':' + e.lineno);
    });
</script>
