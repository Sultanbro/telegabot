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
    protected $username;
    protected $text;

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
        $url = 'https://760c-87-255-197-10.ngrok.io/' . env('TELEGRAM_BOT_TOKEN');
        $response = $this->telegram->setWebhook(['url' => $url]);

        return $response == true ? redirect()->back() : dd($response);
    }

    public function handleRequest(Request $request)
    {
//        $this->chat_id = $request['message']['chat']['id'];
//        $this->username = $request['message']['from']['username'];
//        $this->text = $request['message']['text'];


//        $this->telegram->getChatMember(['chat_id' => $request['my_chat_member']['chat']['id'],
//            'user_id' => 610515462]);
//        if ($request['my_chat_member']['chat']['id']) {
//            $this->telegram->sendMessage([
//              'chat_id' => $request['my_chat_member']['chat']['id'],
//              'text' => "Привет Всем",
//]);
//        }


        $this->chat_id = $request['message']['chat']['id'];
        $this->user_id = $request['message']['from']['id'];
        $this->text = $request['message']['text'];

        Telegram_user::firstOrCreate($request['message']['from']);

        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => "Привет твой tegram id: " . $this->user_id,
        ]);

//        switch ($this->text) {
//            case '/start':
//
//            case '/menu':
//                $this->showMenu();
//                break;
//        }
    }

    public function updateHandler()
    {
        $update = $this->telegram->commandsHandler(true);

        Log::debug('$update');
        Log::debug($update);

        $message = $update->all();


        dd($update);
    }

    public function showMenu($info = null)
    {
        $message = '';
        if ($info) {
            $message .= $info . chr(10);
        }
        $message .= '/menu' . chr(10);
        $message .= '/menu2' . chr(10);

        $this->sendMessage($message);
    }

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
