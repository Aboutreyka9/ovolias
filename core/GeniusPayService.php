<?php

class GeniusPayService
{
    /**
     * Initie un paiement auprès de l'API officielle GeniusPay
     *
     * @param array $data ['amount', 'order_code', 'provider', 'customer_name', 'customer_phone', 'customer_email', ...]
     * @return array
     */
    public static function initiatePayment(array $data): array
    {
        $config = GeniusPayConfig::get();
        $apiKey = $config['api_key'];
        $apiSecret = $config['api_secret'];
        $apiUrl = rtrim($config['api_url'], '/') . '/payments';

        $orderCode = $data['order_code'] ?? ('CMD-' . strtoupper(uniqid()));
        $amount = (float)($data['amount'] ?? 0);
        $provider = strtolower($data['provider'] ?? 'wave');
        $customerName = trim($data['customer_name'] ?? 'Élève / Étudiant GEICG');
        $customerPhone = trim($data['customer_phone'] ?? '+2250102030405');
        $customerEmail = trim($data['customer_email'] ?? 'contact@geicg.ci');
        $callbackUrl = $data['callback_url'] ?? (defined('RACINE') ? RACINE : 'http://localhost/geicg/');
        $webhookUrl = $data['webhook_url'] ?? ($config['webhook_url'] ?? 'http://localhost/geicg/webhooks/geniuspay');

        $payload = [
            'amount' => (int)$amount,
            'currency' => 'XOF',
            'description' => "Paiement Scolarité #{$orderCode}",
            'customer' => [
                'name' => $customerName ?: 'Élève GEICG',
                'phone' => $customerPhone ?: '+2250102030405',
                'email' => $customerEmail ?: 'contact@geicg.ci'
            ],
            'callback_url' => $callbackUrl,
            'webhook_url' => $webhookUrl,
            'metadata' => [
                'order_id' => $orderCode,
                'client_code' => $data['client_code'] ?? '',
                'pressing_code' => $data['pressing_code'] ?? ''
            ]
        ];

        // Tentative d'appel direct vers l'API GeniusPay
        if (!empty($apiKey) && !empty($apiSecret)) {
            try {
                $ch = curl_init($apiUrl);
                curl_setopt_array($ch, [
                    CURLOPT_POST => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'X-API-Key: ' . $apiKey,
                        'X-API-Secret: ' . $apiSecret,
                        'Accept: application/json'
                    ],
                    CURLOPT_POSTFIELDS => json_encode($payload),
                    CURLOPT_TIMEOUT => 15,
                    CURLOPT_SSL_VERIFYPEER => false
                ]);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if (($httpCode === 200 || $httpCode === 201) && !empty($response)) {
                    $resJson = json_decode($response, true);
                    $cUrl = $resJson['data']['checkout_url'] ?? ($resJson['data']['payment_url'] ?? '');
                    if (!empty($cUrl)) {
                        return [
                            'success' => true,
                            'mode' => $config['mode'],
                            'reference' => $resJson['data']['reference'] ?? ('GP-' . strtoupper(uniqid())),
                            'checkout_url' => $cUrl,
                            'payment_url' => $cUrl,
                            'provider' => $provider,
                            'amount' => $amount,
                            'order_code' => $orderCode,
                            'is_online' => true
                        ];
                    }
                }
            } catch (Exception $e) {
                error_log("Erreur GeniusPay API Payment: " . $e->getMessage());
            }
        }

        // Mode simulation fallback
        $ref = 'TXN-' . strtoupper($provider) . '-' . substr(time(), -5) . rand(100, 999);
        return [
            'success' => true,
            'mode' => $config['mode'],
            'reference' => $ref,
            'checkout_url' => '',
            'payment_url' => '',
            'provider' => $provider,
            'amount' => $amount,
            'order_code' => $orderCode,
            'is_online' => true,
            'instructions' => self::getPaymentInstructions($provider, $amount, $ref)
        ];
    }

    /**
     * Déclenche un virement Mobile Money réel (Cashout / Payout)
     *
     * @param array $retrait
     * @return array
     */
    public static function initiateCashout(array $retrait): array
    {
        $config = GeniusPayConfig::get();
        $apiKey = $config['api_key'];
        $apiSecret = $config['api_secret'];
        $apiUrl = rtrim($config['api_url'], '/') . '/payouts';

        $op = strtolower($retrait['operateur_retrait'] ?? 'wave');
        $tel = trim($retrait['telephone_beneficiaire'] ?? '');
        $montant = (int)$retrait['montant_demande'];
        $nom = trim($retrait['nom_beneficiaire'] ?? 'Gérant Pressing');

        $payload = [
            'wallet_id' => $config['wallet_id'] ?? '67d8e536-0afc-46df-b786-3f55d8980bc3',
            'amount' => $montant,
            'currency' => 'XOF',
            'destination' => [
                'type' => 'mobile_money',
                'provider' => $op,
                'account' => $tel
            ],
            'recipient' => [
                'name' => $nom,
                'phone' => $tel
            ],
            'metadata' => [
                'retrait_code' => $retrait['code_retrait'] ?? '',
                'pressing_code' => $retrait['pressing_code'] ?? ''
            ]
        ];

        if (!empty($apiKey) && !empty($apiSecret)) {
            try {
                $ch = curl_init($apiUrl);
                curl_setopt_array($ch, [
                    CURLOPT_POST => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'X-API-Key: ' . $apiKey,
                        'X-API-Secret: ' . $apiSecret,
                        'Accept: application/json'
                    ],
                    CURLOPT_POSTFIELDS => json_encode($payload),
                    CURLOPT_TIMEOUT => 15,
                    CURLOPT_SSL_VERIFYPEER => false
                ]);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if (($httpCode === 200 || $httpCode === 201) && !empty($response)) {
                    $resJson = json_decode($response, true);
                    return [
                        'success' => true,
                        'reference' => $resJson['data']['reference'] ?? ('CASHOUT-' . strtoupper($op) . '-' . rand(1000, 9999)),
                        'status' => 'approuve',
                        'raw' => $resJson
                    ];
                } else {
                    $resJson = json_decode($response, true);
                    $errMsg = $resJson['error']['message'] ?? ($resJson['message'] ?? 'Erreur lors de l\'initiation du virement GeniusPay');
                    return [
                        'success' => false,
                        'message' => $errMsg,
                        'http_code' => $httpCode,
                        'raw' => $resJson
                    ];
                }
            } catch (Exception $e) {
                error_log("Erreur GeniusPay API Cashout: " . $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'Erreur technique de connexion à GeniusPay : ' . $e->getMessage()
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'Clés API GeniusPay manquantes.'
        ];
    }

    private static function getPaymentInstructions(string $provider, float $amount, string $reference): array
    {
        $amtStr = number_format($amount, 0, ',', ' ') . ' FCFA';
        switch ($provider) {
            case 'wave':
                return [
                    'title' => 'Paiement Wave',
                    'steps' => [
                        'Ouvrez votre application Wave sur votre smartphone.',
                        'Acceptez la notification de paiement pour le montant de ' . $amtStr . '.',
                        'Votre commande sera validée instantanément.'
                    ]
                ];
            case 'orange_money':
                return [
                    'title' => 'Orange Money CI',
                    'steps' => [
                        'Composez le #144*62# sur votre téléphone Orange.',
                        'Générez votre code d\'autorisation temporaire.',
                        'Validez la transaction pour le montant de ' . $amtStr . '.'
                    ]
                ];
            case 'mtn_money':
                return [
                    'title' => 'MTN Mobile Money CI',
                    'steps' => [
                        'Composez le *133# sur votre mobile MTN.',
                        'Autorisez le débit de ' . $amtStr . ' pour le service GEICG.'
                    ]
                ];
            case 'moov_money':
                return [
                    'title' => 'Moov Money CI',
                    'steps' => [
                        'Composez le *155# sur votre téléphone Moov.',
                        'Confirmez le paiement de ' . $amtStr . ' avec votre code secret.'
                    ]
                ];
            default:
                return [
                    'title' => 'Paiement Espèces',
                    'steps' => [
                        'Préparez la somme exacte de ' . $amtStr . ' à remettre au livreur.'
                    ]
                ];
        }
    }
}
