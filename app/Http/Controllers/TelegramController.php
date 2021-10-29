<?php

namespace App\Http\Controllers;

//use App\Models\Telegram;
use App\Models\Telegram_user;
use Carbon\Carbon;
use Telegram\Bot\Laravel\Facades\Telegram;
use coinmarketcap\api\CoinMarketCap;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class TelegramController extends Controller
{
    protected $telegram;
    protected $chat_id;
    protected $user_id;
    protected $text;
    protected $from;

    public function __construct()
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }

    public function getMe()
    {
        $response = $this->telegram->getMe();
        return $response;
    }

    public function setWebHook()
    {
        $url = 'https://6214-88-204-255-195.ngrok.io/' . env('TELEGRAM_BOT_TOKEN');
        $response = $this->telegram->setWebhook(['url' => $url]);

        return $response == true ? redirect()->back() : dd($response);
    }

    public function handleRequest(Request $request)
    {
        $this->chat_id = $request['message']['chat']['id'];
        $this->user_id = $request['message']['from']['id'];
        $this->text = $request['message']['text'];
        $this->from = $request['message']['from'];

        switch ($this->text) {
            case '/start':
                $this->saveTelegramUser();
//            case '/menu':
//                $this->showMenu();
                break;
        }
    }

    public function updateHandler()
    {
        $response = $this->telegram->getFullChat(['chat_id' => -1001583162473,]);
//        ['chat_id' => -1001583162473,]

        dd($response);
    }

    public function saveTelegramUser()
    {
        Telegram_user::firstOrCreate($this->from);

        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => "Привет твой tegram id: " . $this->user_id ,
        ]);
    }

//    public function showMenu($info = null)
//    {
//        $message = '';
//        if ($info) {
//            $message .= $info . chr(10);
//        }
//        $message .= '/menu' . chr(10);
//        $message .= '/menu2' . chr(10);
//
//        $this->sendMessage($message);
//    }

    public function linkGroup()
    {
//        $this->telegram->sendMessage([
//            'chat_id' => $this->chat_id,
//            'text' => "<a href="https://www.google.com/">Поисковая система Яндекс</a>"  ,
//        ]);

//        $this->sendMessage($message);
//        $this->sendMessage($message, true);
    }

    protected function sendMessage($message, $parse_html = false)
    {
        $data = [
            'chat_id' => $this->chat_id,
            'text' => $message,
        ];

        if ($parse_html) $data['parse_mode'] = 'HTML';

        $this->telegram->sendMessage($data);
    }


}
