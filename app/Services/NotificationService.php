<?php

namespace App\Services;

use App\Mail\LowStockAlert;
use App\Mail\ProductionCompleteAlert;
use App\Models\Notification;
use App\Models\PushToken;
use App\Models\RawMaterial;
use App\Models\FinishedGood;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public static function create(string $type, string $title, string $message, array $data = [], ?int $userId = null): Notification
    {
        return Notification::create([
            'type'      => $type,
            'title'     => $title,
            'message'   => $message,
            'data'      => $data,
            'user_id'   => $userId,
            'is_global' => $userId === null,
        ]);
    }

    public static function checkLowStock(): void
    {
        $gerants = User::where('role', 'gerant')->where('is_active', true)->get();

        // MP en stock bas
        $lowMP = RawMaterial::where('is_active', true)
            ->whereColumn('quantity_in_stock', '<=', 'min_stock_alert')
            ->where('min_stock_alert', '>', 0)
            ->get();

        foreach ($lowMP as $mp) {
            $exists = Notification::where('type', 'low_stock_mp')
                ->whereNull('read_at')
                ->whereJsonContains('data->id', $mp->id)
                ->exists();

            if (!$exists) {
                self::create(
                    'low_stock_mp',
                    '⚠️ Stock bas — ' . $mp->name,
                    "Le stock de {$mp->name} est à {$mp->quantity_in_stock} {$mp->unit}",
                    ['id' => $mp->id, 'name' => $mp->name, 'stock' => $mp->quantity_in_stock, 'unit' => $mp->unit]
                );

                // Email aux gérants
                foreach ($gerants as $gerant) {
                    Mail::to($gerant->email)->send(new LowStockAlert(
                        $mp->name,
                        $mp->quantity_in_stock,
                        $mp->min_stock_alert,
                        $mp->unit,
                        'raw_material'
                    ));
                }

                // Push
                self::sendPush(
                    '⚠️ Stock bas',
                    "Le stock de {$mp->name} est critique !",
                    ['type' => 'low_stock_mp', 'id' => $mp->id]
                );
            }
        }

        // PF en stock bas
        $lowPF = FinishedGood::whereColumn('quantity_in_stock', '<=', 'min_stock_alert')
            ->where('min_stock_alert', '>', 0)
            ->get();

        foreach ($lowPF as $pf) {
            $exists = Notification::where('type', 'low_stock_pf')
                ->whereNull('read_at')
                ->whereJsonContains('data->id', $pf->id)
                ->exists();

            if (!$exists) {
                self::create(
                    'low_stock_pf',
                    '⚠️ Stock bas — ' . $pf->product_name,
                    "Le stock de {$pf->product_name} est à {$pf->quantity_in_stock} packets",
                    ['id' => $pf->id, 'name' => $pf->product_name, 'stock' => $pf->quantity_in_stock]
                );

                foreach ($gerants as $gerant) {
                    Mail::to($gerant->email)->send(new LowStockAlert(
                        $pf->product_name,
                        $pf->quantity_in_stock,
                        $pf->min_stock_alert,
                        'packets',
                        'finished_good'
                    ));
                }

                self::sendPush(
                    '⚠️ Stock bas PF',
                    "Le stock de {$pf->product_name} est critique !",
                    ['type' => 'low_stock_pf', 'id' => $pf->id]
                );
            }
        }
    }

    public static function sendProductionEmail(
        string $batchNumber,
        int    $packetsActual,
        int    $packetsEstimated,
        string $recipeName,
        string $operatorName
    ): void {
        $gerants = User::where('role', 'gerant')->where('is_active', true)->get();
        foreach ($gerants as $gerant) {
            Mail::to($gerant->email)->send(new ProductionCompleteAlert(
                $batchNumber,
                $packetsActual,
                $packetsEstimated,
                $recipeName,
                $operatorName
            ));
        }
    }

    public static function sendPush(string $title, string $body, array $data = [], ?int $userId = null): void
{
    $query = PushToken::query();
    if ($userId) {
        $query->where('user_id', $userId);
    }
    $tokens = $query->pluck('token')->toArray();

    if (empty($tokens)) return;

    $accessToken = self::getFcmAccessToken();

    foreach ($tokens as $token) {
        Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type'  => 'application/json',
        ])->post(
            'https://fcm.googleapis.com/v1/projects/' . config('services.fcm.project_id') . '/messages:send',
            [
                'message' => [
                    'token'        => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => array_map('strval', $data), // FCM data values must be strings
                    'android' => [
                        'priority' => 'high', // needed for heads-up / wake screen
                    ],
                    'apns' => [
                        'headers' => ['apns-priority' => '10'],
                    ],
                ],
            ]
        );
    }
}

// FCM HTTP v1 uses OAuth2, not a server key
private static function getFcmAccessToken(): string
{
    $cached = cache('fcm_access_token');
    if ($cached) return $cached;

    $credentialsPath = storage_path('app/firebase/service-account.json');
    $credentials = json_decode(file_get_contents($credentialsPath), true);

    $now = time();

    $header = self::base64UrlEncode(json_encode([
        'alg' => 'RS256',
        'typ' => 'JWT',
    ]));

    $payload = self::base64UrlEncode(json_encode([
        'iss'   => $credentials['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'iat'   => $now,
        'exp'   => $now + 3600,
    ]));

    $signingInput = "$header.$payload";
    openssl_sign($signingInput, $signature, $credentials['private_key'], 'SHA256');
    $jwt = "$signingInput." . self::base64UrlEncode($signature);

    $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion'  => $jwt,
    ]);

    $accessToken = $response->json('access_token');

    cache(['fcm_access_token' => $accessToken], now()->addMinutes(55));

    return $accessToken;
}

private static function base64UrlEncode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
}