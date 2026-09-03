<?php

class SettingController extends BaseController
{
    private ModelSetting $settingModel;

    public function __construct()
    {
        $this->settingModel = new ModelSetting();
        parent::__construct();
    }

    protected function resolveModel()
    {
        return new ModelSetting();
    }

    /**
     * Affichage et gestion de la page des paramètres globaux système (Super Admin)
     */
    public function list()
    {
        $this->requireAuth();
        if (!$this->isSuperAdmin()) {
            $_SESSION['error_msg'] = "Accès réservé au Super Admin.";
            header('Location: ' . RACINE);
            exit();
        }

        $promoModel = new ModelPromotion();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action_type']) && $_POST['action_type'] === 'add_promo') {
                $res = $promoModel->createPromotion($_POST);
                if ($res['success']) {
                    $_SESSION['success_msg'] = "Code promo créé avec succès !";
                } else {
                    $_SESSION['error_msg'] = $res['error'];
                }
                header('Location: ' . RACINE . 'setting/list');
                exit();
            }

            $commission = isset($_POST['commission_defaut_lavex']) ? (float)$_POST['commission_defaut_lavex'] : 0.00;
            $fraisCollecte = isset($_POST['frais_collecte_defaut_lavex']) ? (float)$_POST['frais_collecte_defaut_lavex'] : 1000.00;
            $fraisLivraison = isset($_POST['frais_livraison_defaut_lavex']) ? (float)$_POST['frais_livraison_defaut_lavex'] : 1000.00;
            $delaiLivraison = !empty($_POST['delai_livraison_defaut_lavex']) ? trim($_POST['delai_livraison_defaut_lavex']) : '24h - 48h';
            $nomPlateforme = !empty($_POST['nom_plateforme']) ? trim($_POST['nom_plateforme']) : 'Lavex';
            $emailSupport = !empty($_POST['email_support']) ? trim($_POST['email_support']) : 'contact@lavex.ci';

            $onesignalAppId = !empty($_POST['onesignal_app_id']) ? trim($_POST['onesignal_app_id']) : '';
            $onesignalRestKey = !empty($_POST['onesignal_rest_api_key']) ? trim($_POST['onesignal_rest_api_key']) : '';
            $textePromoBar = !empty($_POST['texte_promo_bar']) ? trim($_POST['texte_promo_bar']) : 'Lavex - Pressing & Laverie à domicile | -20% sur votre première commande avec le code LAVEX20 | Service rapide & livraison offerte dès 15 000 FCFA';

            $this->settingModel->setSetting('commission_defaut_lavex', $commission);
            $this->settingModel->setSetting('frais_collecte_defaut_lavex', $fraisCollecte);
            $this->settingModel->setSetting('frais_livraison_defaut_lavex', $fraisLivraison);
            $this->settingModel->setSetting('delai_livraison_defaut_lavex', $delaiLivraison);
            $this->settingModel->setSetting('nom_plateforme', $nomPlateforme);
            $this->settingModel->setSetting('email_support', $emailSupport);
            $this->settingModel->setSetting('onesignal_app_id', $onesignalAppId);
            $this->settingModel->setSetting('onesignal_rest_api_key', $onesignalRestKey);
            $this->settingModel->setSetting('texte_promo_bar', $textePromoBar);

            $_SESSION['success_msg'] = "Les paramètres globaux de la plateforme ont été enregistrés avec succès.";
            header('Location: ' . RACINE . 'setting/list');
            exit();
        }

        $settings = $this->settingModel->getAllSettings();
        $promotions = $promoModel->getAllPromotions();

        $data = [
            'titre' => 'Paramètres Système & Promotions Lavex',
            'settings' => $settings,
            'promotions' => $promotions
        ];

        $this->loadView('../views/settings/list.php', $data);
    }
}
