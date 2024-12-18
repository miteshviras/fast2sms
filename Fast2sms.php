<?php

namespace App\Services\Fast2sms;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Fast2sms
{
    // Guzzle HTTP client instance
    protected static $client;

    // Initialize the Guzzle HTTP client
    public static function init()
    {
        if (!self::$client) {
            self::$client = new Client();
        }
    }

    /**
     * Send OTP to a given number using Fast2sms API
     *
     * @param string $otp The OTP to send
     * @param string $number The recipient's phone number
     * @return array Response indicating success
     * @throws Exception If there is an error with the request
     */
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
            // Decode the response body to get the error message and status code
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);
            throw new Exception($response['message'], $response['status_code']);
        }
    }
}
