<?php

namespace App\Services\Fast2sms;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class Fast2sms
{
    protected static $client;

    /**
     * Initialize the Guzzle HTTP client if not already initialized
     */
    protected static function initClient()
    {
        if (!isset(self::$client)) {
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
    public static function sendOTP(string $otp, string $number): array
    {
        self::initClient();

        $url = 'https://www.fast2sms.com/dev/bulkV2';
        $headers = [
            'authorization' => config('env.FAST2SMS_TOKEN'),
        ];
        $formParams = [
            'variables_values' => $otp,
            'route' => 'otp',
            'numbers' => $number,
        ];

        try {
            $response = self::$client->post($url, [
                'headers' => $headers,
                'form_params' => $formParams,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            if (isset($responseBody['return']) && $responseBody['return'] === true) {
                return ['success' => 'Message sent successfully'];
            }

            throw new Exception($responseBody['message'] ?? 'Failed to send message');
        } catch (ClientException | RequestException $e) {
            $responseBody = $e->getResponse() ? json_decode($e->getResponse()->getBody()->getContents(), true) : null;
            $errorMessage = $responseBody['message'] ?? $e->getMessage();
            $statusCode = $responseBody['status_code'] ?? $e->getCode();

            throw new Exception($errorMessage, $statusCode);
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred: ' . $e->getMessage(), $e->getCode());
        }
    }
}
