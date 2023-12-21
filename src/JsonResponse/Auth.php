<?php

namespace Qhopes\SatuSehat\JsonResponse;

use Qhopes\SatuSehat\Utilitys\Enviroment;
use Qhopes\SatuSehat\Utilitys\Url;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class Auth
{
    private static function requestToken() : array
    {
        $url = Url::authUrl();
        $data['client_id'] = Enviroment::clientId();
        $data['client_secret'] = Enviroment::clientSecret();
        $response = Http::asForm()
            ->timeout(300)
            ->retry(5,1000)
            ->post($url,$data);
        if ($response->successful()) {
            if ($response->status() == 200) {
                $data = json_decode($response->body(),true);
                return [
                    'status' => true,
                    'data' => [
                        'token' => $data['access_token'],
                        'expired' => $data['expires_in'],
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                ];
            }
        }
        return [
            'status' => false,
            'message' => 'Error generate token!'
        ];
    }

    private static function generateToken() : array
    {
        $requestToken = self::requestToken();
        if ($requestToken['status']) {
            $data = $requestToken['data'];
            session()->put('satusehat_token_key',$data['token']);
            session()->put('satusehat_token_exp',$data['expired']);
            session()->put('satusehat_token_created_at',$data['created_at']);
            return [
                'status' => true,
                'data' => [
                    'token' => $data['token']
                ]
            ];
        }
        return [
            'status' => false,
            'message' => $requestToken['message']
        ];
    }

    public static function getToken() : array
    {
        $token = session('satusehat_token_key',false);
        $expired = session('satusehat_token_exp',false);
        $createdAt = session('satusehat_token_created_at',false);
        if (!$token && !$expired && !$createdAt) {
            $generate = self::generateToken();
            if ($generate['status']) {
                $data = $generate['data'];
                return [
                    'status' => true,
                    'token' => $data['token']
                ];
            } else {
                return [
                    'status' => false,
                    'message' => $generate['message']
                ];
            }
        } else {
            $diff = now()->diffInSeconds(Carbon::parse($createdAt));
            $expired = intval($expired - 60);
            if ($diff >= $expired) {
                $generate = self::requestToken();
                if ($generate['status']) {
                    $generateData = $generate['data'];
                    return [
                        'status' => true,
                        'token' => $generateData['token']
                    ];
                } else {
                    return [
                        'status' => false,
                        'message' => $generate['message']
                    ];
                }
            } else {
                $generate = self::generateToken();
                if ($generate['status']) {
                    $data = $generate['data'];
                    return [
                        'status' => true,
                        'token' => $data['token']
                    ];
                } else {
                    return [
                        'status' => false,
                        'message' => $generate['message']
                    ];
                }
            }
        }
    }
}
