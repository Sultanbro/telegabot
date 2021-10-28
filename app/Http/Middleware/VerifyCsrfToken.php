<?php

namespace App\Http\Middleware;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Telegram\Bot\Laravel\Facades\Telegram;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
//        (string)Telegram::getAccessToken(),
//        '1366133149:AAGRlED_27Qz_N_4R56WjxC9nHvyIDGZhtg/webhook',
//        'https://f488-88-204-255-195.ngrok.io/1366133149:AAGRlED_27Qz_N_4R56WjxC9nHvyIDGZhtg/webhook',
//        '1366133149:*'
    ];

    public function __construct(Application $app, Encrypter $encrypter)
    {
        $this->app = $app;
        $this->encrypter = $encrypter;
        $this->except[] = Telegram::getAccessToken();
    }
}
