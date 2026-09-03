<?php

class GeniusPayConfig
{
    public static function get(): array
    {
        $mode = getenv('GENIUSPAY_MODE') ?: 'sandbox';
        return [
            'mode' => $mode,
            'webhook_secret' => $mode === 'sandbox'
                ? (getenv('GENIUSPAY_WEBHOOK_SECRET_SANDBOX') ?: 'whsec_WXg2uRBE6YZ8EE9F3Ymmerbd9ca7NwQidDOTvnnFIaJOgTXq')
                : (getenv('GENIUSPAY_WEBHOOK_SECRET_LIVE') ?: 'whsec_WXg2uRBE6YZ8EE9F3Ymmerbd9ca7NwQidDOTvnnFIaJOgTXq'),
            'webhook_secret_alt' => 'whsec_UcGwsB95ERjTMX6GqUZ5ktZfMH60hJyXjUD8sRq7NLEHWcK5',
            'api_key' => $mode === 'sandbox'
                ? (getenv('GENIUSPAY_API_KEY_SANDBOX') ?: 'sk_sandbox_3cb4Hr013eAsOkvp66bwhxby0ZEhSUL8')
                : (getenv('GENIUSPAY_API_KEY_LIVE') ?: 'gp_live_key'),
            'api_secret' => $mode === 'sandbox'
                ? (getenv('GENIUSPAY_API_SECRET_SANDBOX') ?: 'ss_sandbox_npYoZWl2BvIbwGpPBwAuZ9GW1TcDiQyDC0tTPHMgfZukbujY')
                : (getenv('GENIUSPAY_API_SECRET_LIVE') ?: ''),
            'webhook_url' => 'http://localhost/geicg/webhooks/geniuspay',
            'api_url' => 'https://pay.genius.ci/api/v1/merchant',
            'wallet_id' => '67d8e536-0afc-46df-b786-3f55d8980bc3', // API Disponible
            'commission_rate' => 0.00,
            'minimum_retrait' => 2000.00,
        ];
    }
}
