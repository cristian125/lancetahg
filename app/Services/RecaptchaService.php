<?php

namespace App\Services;

use GuzzleHttp\Client;

class RecaptchaService
{
    protected $client;
    protected $secretKey;

    public function __construct()
    {
        $this->client = new Client(['verify'=>false]);
        $this->secretKey = config('services.recapcha.secret_key');
    }

    public function verify($token)
    {
        $response = $this->client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => $this->secretKey,
                'response' => $token,
            ],
        ]);

        $body = json_decode((string) $response->getBody());

        return $body->success && $body->score > 0.5; // Ajusta el score según tus necesidades
    }
}
