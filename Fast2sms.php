<?php

namespace App\Services\Fast2sms;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ClientException;

class Fast2sms
{
    protected static $client;
    public static function init()
    {
        self::$client = new Client();
    }

    public static function sendOTP($otp, $number)
    {
        try {
            self::init();
            $url = 'https://www.fast2sms.com/dev/bulkV2';
            $response = self::$client->request('post', $url, [
                'headers' => [
                    'authorization' => config('fast2sms.authorization_token'),
                ],
                'form_params' => [
                    'variables_values' => $otp,
                    'route' => 'otp',
                    'numbers' => $number,
                ]
            ]);

            return [
                'success' => 'Message sent successfully'
            ];
        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(),true);
            throw new Exception($response['message'],$response['status_code']) ;
        }
    }
}
