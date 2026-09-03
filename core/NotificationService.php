<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/const.php';

class NotificationService
{
    private static ?PDO $db = null;

    private static function getDb(): PDO
    {
        if (self::$db === null) {
            self::$db = (new Database())->getCon();
        }
        return self::$db;
    }

    public function send(
        string $title,
        string $message,
        string $clientCode,
        string $target = 'client',
        string $type = 'commande',
        ?string $referenceCode = null,
        array $extraData = []
    ): ?string {
        return self::notifyClient($clientCode, $type, $title, $message, $referenceCode, $extraData);
    }

    /**
     * Envoie une notification au client (In-App en BDD + Push OneSignal)
     */
    public static function notifyClient(
        string $clientCode,
        string $type,
        string $title,
        string $message,
        ?string $referenceCode = null,
        array $extraData = []
    ): ?string {
        if (empty($clientCode)) {
            return null;
        }

        $codeNotification = 'NOT-' . strtoupper(bin2hex(random_bytes(4))) . '-' . substr(time(), -4);
        $dataJsonStr = !empty($extraData) ? json_encode($extraData, JSON_UNESCAPED_UNICODE) : null;

        // 1. Sauvegarde en Base de Données (In-App)
        try {
            $pdo = self::getDb();
            $stmt = $pdo->prepare("
                INSERT INTO `notifications` 
                (`code_notification`, `client_code`, `type_notification`, `titre_notification`, `message_notification`, `reference_code`, `data_json`, `lu_notification`, `statut_notification`) 
                VALUES 
                (:code, :client_code, :type, :title, :message, :reference_code, :data_json, 0, 'envoyee')
            ");
            $stmt->execute([
                ':code' => $codeNotification,
                ':client_code' => $clientCode,
                ':type' => $type,
                ':title' => $title,
                ':message' => $message,
                ':reference_code' => $referenceCode,
                ':data_json' => $dataJsonStr
            ]);
        } catch (Exception $e) {
            error_log("Erreur BDD NotificationService (Admin): " . $e->getMessage());
            return null;
        }

        // 2. Envoi Push OneSignal en arrière-plan
        self::sendOneSignalPush($clientCode, $title, $message, $codeNotification, $referenceCode, $extraData);

        return $codeNotification;
    }

    /**
     * Envoie une notification dédiée au livreur (In-App)
     */
    public static function notifyLivreur(
        string $livreurCode,
        string $type,
        string $title,
        string $message,
        ?string $referenceCode = null,
        array $extraData = []
    ): ?string {
        if (empty($livreurCode)) {
            return null;
        }

        $codeNotification = 'NOT-' . strtoupper(bin2hex(random_bytes(4))) . '-' . substr(time(), -4);
        $dataJsonStr = !empty($extraData) ? json_encode($extraData, JSON_UNESCAPED_UNICODE) : null;

        try {
            $pdo = self::getDb();
            $stmt = $pdo->prepare("
                INSERT INTO `notifications` 
                (`code_notification`, `livreur_code`, `type_notification`, `titre_notification`, `message_notification`, `reference_code`, `data_json`, `lu_notification`, `statut_notification`) 
                VALUES 
                (:code, :livreur_code, :type, :title, :message, :reference_code, :data_json, 0, 'envoyee')
            ");
            $stmt->execute([
                ':code' => $codeNotification,
                ':livreur_code' => $livreurCode,
                ':type' => $type,
                ':title' => $title,
                ':message' => $message,
                ':reference_code' => $referenceCode,
                ':data_json' => $dataJsonStr
            ]);
        } catch (Exception $e) {
            error_log("Erreur BDD NotificationService (Livreur): " . $e->getMessage());
            return null;
        }

        self::sendOneSignalPush($livreurCode, $title, $message, $codeNotification, $referenceCode, $extraData);

        return $codeNotification;
    }

    /**
     * Envoie une notification dédiée au pressing (In-App + Push OneSignal)
     */
    public static function notifyPressing(
        string $pressingCode,
        string $type,
        string $title,
        string $message,
        ?string $referenceCode = null,
        array $extraData = []
    ): ?string {
        if (empty($pressingCode)) {
            return null;
        }

        $codeNotification = 'NOT-' . strtoupper(bin2hex(random_bytes(4))) . '-' . substr(time(), -4);
        $dataJsonStr = !empty($extraData) ? json_encode($extraData, JSON_UNESCAPED_UNICODE) : null;

        try {
            $pdo = self::getDb();
            $stmt = $pdo->prepare("
                INSERT INTO `notifications` 
                (`code_notification`, `pressing_code`, `type_notification`, `titre_notification`, `message_notification`, `reference_code`, `data_json`, `lu_notification`, `statut_notification`) 
                VALUES 
                (:code, :pressing_code, :type, :title, :message, :reference_code, :data_json, 0, 'envoyee')
            ");
            $stmt->execute([
                ':code' => $codeNotification,
                ':pressing_code' => $pressingCode,
                ':type' => $type,
                ':title' => $title,
                ':message' => $message,
                ':reference_code' => $referenceCode,
                ':data_json' => $dataJsonStr
            ]);
        } catch (Exception $e) {
            error_log("Erreur BDD NotificationService (Pressing): " . $e->getMessage());
            return null;
        }

        self::sendOneSignalPush($pressingCode, $title, $message, $codeNotification, $referenceCode, $extraData);

        return $codeNotification;
    }

    /**
     * Envoi Push OneSignal REST API
     */
    private static function sendOneSignalPush(
        string $clientCode,
        string $title,
        string $message,
        string $codeNotification,
        ?string $referenceCode = null,
        array $extraData = []
    ): void {
        // Push désactivé pour le projet GEICG (notifications BDD uniquement)
        return;
    }

    // --- Helpers Événements Métier Client ---

    public static function notifyOrderCreated(string $clientCode, string $orderCode, string $pressingName): ?string
    {
        return self::notifyClient(
            $clientCode,
            'commande.creee',
            'Commande envoyée',
            "Votre commande #{$orderCode} a été transmise à {$pressingName}.",
            $orderCode,
            ['pressing' => $pressingName, 'step' => 'creee']
        );
    }

    public static function notifyOrderAccepted(string $clientCode, string $orderCode, string $pressingName): ?string
    {
        return self::notifyClient(
            $clientCode,
            'commande.acceptee',
            'Commande acceptée',
            "{$pressingName} a accepté votre commande #{$orderCode}.",
            $orderCode,
            ['pressing' => $pressingName, 'step' => 'acceptee']
        );
    }

    public static function notifyOrderRejected(string $clientCode, string $orderCode, string $pressingName, ?string $motif = null): ?string
    {
        $msg = "{$pressingName} ne peut pas traiter votre commande #{$orderCode}.";
        if (!empty($motif)) {
            $msg .= " (Motif : {$motif})";
        }
        return self::notifyClient(
            $clientCode,
            'commande.refusee',
            'Commande refusée',
            $msg,
            $orderCode,
            ['pressing' => $pressingName, 'motif' => $motif, 'step' => 'refusee']
        );
    }

    public static function notifyCollectionScheduled(string $clientCode, string $orderCode): ?string
    {
        return self::notifyClient(
            $clientCode,
            'collecte.programmee',
            'Collecte programmée',
            "Un livreur va récupérer votre commande #{$orderCode}.",
            $orderCode,
            ['step' => 'collecte_programmee']
        );
    }

    public static function notifyDriverAssigned(string $clientCode, string $orderCode, string $driverName): ?string
    {
        return self::notifyClient(
            $clientCode,
            'collecte.livreur_assigne',
            'Livreur assigné',
            "Votre collecte #{$orderCode} sera effectuée par {$driverName}.",
            $orderCode,
            ['driver_name' => $driverName, 'step' => 'livreur_assigne']
        );
    }

    public static function notifyDriverEnRoute(string $clientCode, string $orderCode, string $driverName): ?string
    {
        return self::notifyClient(
            $clientCode,
            'collecte.livreur_en_route',
            'Votre livreur est en route',
            "{$driverName} arrive pour récupérer votre linge (#{$orderCode}).",
            $orderCode,
            ['driver_name' => $driverName, 'step' => 'livreur_en_route']
        );
    }

    public static function notifyCollectionCompleted(string $clientCode, string $orderCode): ?string
    {
        return self::notifyClient(
            $clientCode,
            'collecte.effectuee',
            'Linge collecté',
            "Votre commande #{$orderCode} a été récupérée avec succès.",
            $orderCode,
            ['step' => 'collectee']
        );
    }

    public static function notifyReceivedAtPressing(string $clientCode, string $orderCode, string $pressingName): ?string
    {
        return self::notifyClient(
            $clientCode,
            'pressing.receptionnee',
            'Linge arrivé au pressing',
            "Vos vêtements sont arrivés chez {$pressingName} (#{$orderCode}).",
            $orderCode,
            ['pressing' => $pressingName, 'step' => 'recue_pressing']
        );
    }

    public static function notifyColisPriceToConfirm(string $clientCode, string $orderCode, float $amount, string $pressingName): ?string
    {
        $formattedAmount = number_format($amount, 0, ',', ' ');
        return self::notifyClient(
            $clientCode,
            'colis.prix_a_valider',
            'Devis après inventaire',
            "Votre linge a été inventorié chez {$pressingName}. Montant : {$formattedAmount} FCFA. Cliquez pour confirmer.",
            $orderCode,
            ['amount' => $amount, 'requires_confirmation' => true, 'step' => 'prix_a_valider']
        );
    }

    public static function notifyProcessingStarted(string $clientCode, string $orderCode): ?string
    {
        return self::notifyClient(
            $clientCode,
            'traitement.en_cours',
            'Commande en traitement',
            "Votre linge (#{$orderCode}) est actuellement en cours de nettoyage.",
            $orderCode,
            ['step' => 'en_traitement']
        );
    }

    public static function notifyOrderReady(string $clientCode, string $orderCode): ?string
    {
        return self::notifyClient(
            $clientCode,
            'commande.prete',
            'Votre commande est prête',
            "Votre linge (#{$orderCode}) est propre et prêt à être livré.",
            $orderCode,
            ['step' => 'prete']
        );
    }

    public static function notifyDeliveryEnRoute(string $clientCode, string $orderCode, ?string $driverName = null): ?string
    {
        $driverMsg = !empty($driverName) ? " par {$driverName}" : "";
        return self::notifyClient(
            $clientCode,
            'livraison.en_cours',
            'Livraison en cours',
            "Votre commande #{$orderCode} est en route vers votre adresse{$driverMsg}.",
            $orderCode,
            ['driver_name' => $driverName, 'step' => 'en_livraison']
        );
    }

    public static function notifyOrderDelivered(string $clientCode, string $orderCode): ?string
    {
        return self::notifyClient(
            $clientCode,
            'commande.livree',
            'Commande livrée',
            "Votre linge propre (#{$orderCode}) vous a été remis. Merci d'avoir utilisé Lavex !",
            $orderCode,
            ['step' => 'livree']
        );
    }
}
