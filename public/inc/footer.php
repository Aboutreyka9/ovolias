<footer class="footer" id="footer">
        <div class="footer-content">
            <div>&copy; 2026 GEICG - Grande École. Tous droits réservés.</div>
            <div class="footer-links">
                <a href="#">Documentation</a>
                <a href="#">Support Technique</a>
            </div>
        </div>
    </footer>

    <div class="bottom-nav" id="bottomNav">
        <a href="<?= RACINE ?>" class="bottom-nav-item">
            <i data-lucide="layout-dashboard"></i>
            <span>Accueil</span>
        </a>
        <a href="<?= RACINE ?>inscription/list" class="bottom-nav-item">
            <i data-lucide="user-plus"></i>
            <span>Inscriptions</span>
        </a>
        <a href="<?= RACINE ?>paiement/list" class="bottom-nav-item">
            <i data-lucide="credit-card"></i>
            <span>Caisse</span>
        </a>
        <a href="<?= RACINE ?>note/list" class="bottom-nav-item">
            <i data-lucide="edit-3"></i>
            <span>Notes</span>
        </a>

        <button type="button" class="bottom-nav-item" id="bnProfil">
            <i data-lucide="user"></i>
            <span>Profil</span>
        </button>
    </div>


    <div class="dropdown-panel bottom-sheet" id="panelProfil">
        <div class="dropdown-header">
            <h3>Profil</h3>
        </div>
        <div class="dropdown-grid">
            <a href="<?= RACINE ?>user/profil" class="dropdown-card">
                <i data-lucide="user"></i>
                <span>Mon profil</span>
            </a>
            <a href="<?= RACINE ?>user/editPassword" class="dropdown-card">
                <i data-lucide="lock"></i>
                <span>Mot de passe</span>
            </a>
        </div>
    </div>

    <!-- Generic Modal -->
    <div class="modal-overlay" id="genericModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Ajouter</h3>
                <button class="modal-close" id="modalClose"><i data-lucide="x"></i></button>
            </div>
            <div class="modal-body" id="modalBody">
                <form id="modalForm"></form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" id="modalCancel">Annuler</button>
                <button class="btn-primary" id="modalSave">Enregistrer</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="confirmModal">
        <div class="modal" style="max-width: 400px;">
            <div class="modal-header">
                <h3 class="modal-title" id="confirmTitle">Confirmation</h3>
                <button class="modal-close" id="confirmClose"><i data-lucide="x"></i></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage">Êtes-vous sûr ?</p>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" id="confirmCancel">Annuler</button>
                <button class="btn-primary" style="background: var(--danger);" id="confirmOk">Confirmer</button>
            </div>
        </div>
    </div>



    <div class="mobile-actions-overlay" id="mobileActionOverlay">
        <div class="mobile-actions-sheet" id="mobileActionSheet">
            <div class="mobile-actions-content" id="mobileActionsContent"></div>
        </div>
    </div>

   <?php include_once __DIR__ . '/footer-link.php' ?>
</body>
</html>