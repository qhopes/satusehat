<?php

namespace Qhopes\SatuSehat\Utilitys;

use Illuminate\Support\Facades\Http;
use Qhopes\SatuSehat\JsonResponse as jsonResponse;

class HttpRequest
{
    private static function checkResponse($response)
    {
        $res = json_decode($response->body(),true);
        if ($res['resourceType'] == 'OperationOutcome') {
            return [
                'status'=> false,
                'message'=> $response->body()
            ];
        }
        return [
            'status'=> true,
            'response' => $response
        ];
    }

    public static function get($url)
    {
        $getToken = jsonResponse\Auth::getToken();
        if ($getToken['status']) {
            $response = Http::asForm()
                ->timeout(300)
                ->retry(5,1000)
                ->withToken($getToken['token'])
                ->get($url);
            if ($response->successful()) {
                if ($response->status() == 200) {
                    return self::checkResponse($response);
                }
            }
            return jsonResponse\Error::response($response);
        }
        return jsonResponse\Error::getToken($getToken);
    }

    public static function poolGet($pool,$token,$as,$url)
    {
        return $pool->as($as)->asForm()
            ->timeout(300)
            ->retry(5,1000)
            ->withToken($token)
            ->get($url);
    }

    public static function post($url,$formData)
    {
        $getToken = jsonResponse\Auth::getToken();
        if ($getToken['status']) {
            $response = Http::timeout(300)
                ->retry(5,1000)
                ->withToken($getToken['token'])
                ->post($url,$formData);
            if ($response->successful()) {
                if ($response->status() == 201) {
                    return self::checkResponse($response);
                }
            }
            return jsonResponse\Error::response($response);
        }
        return jsonResponse\Error::getToken($getToken);
    }

    public static function postConsent($url,$formData)
    {
        $getToken = jsonResponse\Auth::getToken();
        if ($getToken['status']) {
            $response = Http::timeout(300)
                ->retry(5,1000)
                ->withToken($getToken['token'])
                ->post($url,$formData);
            if ($response->successful()) {
                if ($response->status() == 200) {
                    return self::checkResponse($response);
                }
            }
            return jsonResponse\Error::response($response);
        }
        return jsonResponse\Error::getToken($getToken);
    }

    public static function put($url,$formData)
    {
        $getToken = jsonResponse\Auth::getToken();
        if ($getToken['status']) {
            $response = Http::timeout(300)
                ->retry(5,1000)
                ->withToken($getToken['token'])
                ->put($url,$formData);
            if ($response->successful()) {
                if ($response->status() == 200) {
                    return self::checkResponse($response);
                }
            }
            return jsonResponse\Error::response($response);
        }
        return jsonResponse\Error::getToken($getToken);
    }
}