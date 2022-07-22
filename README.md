# fast2sms
send otp via fast2sms
here you need to install guzzlehttp

- composer require guzzlehttp/guzzle
- create config file which named fast2sms.php
    return [
        'authorization_token' => env('FAST2SMS_AUTHORIZATION_TOKEN'), //your fast2sms authorization token or api key
    ];
    
