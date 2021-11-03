<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\TelegramUser;
use Carbon\Carbon;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Facades\Telegram;
use coinmarketcap\api\CoinMarketCap;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use function PHPUnit\Framework\isNull;

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
        $url = 'https://e360-87-255-197-10.ngrok.io/' . env('TELEGRAM_BOT_TOKEN');
        $response = $this->telegram->setWebhook(['url' => $url]);

        return $response == true ? redirect()->back() : dd($response);
    }

    public function handleRequest(Request $request)
    {
        if (isset($request['message']['text'])) {
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
    }

    public function saveTelegramUser()
    {
        $user = TelegramUser::firstOrCreate($this->from);

        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => "Привет твой tegram id: " . $this->user_id . ' 11 ' . date('Y-m-d', strtotime($user->pay_day. " +5 day")),
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

    public function userInGroup()
    {
        $response = $this->telegram->getChatMember(['chat_id' => env('CHAT_ID'), 'user_id' => '844867712',]);
//        return $response;
        if ($response->status == 'restricted' or $response->status == 'member') {
            return $response['status'];
        }
        return false;
    }

    public function usersCheck($chats)
    {
        $users = TelegramUser::all();
        $count = 0;
        foreach ($users as $user) {
            $checks = $this->checkStatus($user->id, $chats);
            $this->saveCheck($user, $checks);

        }
    }

    public function checkStatus($user_id, $chats_id)
    {
        foreach ($chats_id as $k => $chat) {
            try {
                $response = $this->telegram->getChatMember(['chat_id' => $chat, 'user_id' => $user_id,]);
                if ($response->status == 'restricted' or $response->status == 'member') {
                    $results[$k] = 1;
                } else {
                    $results[$k] = 0;
                }
            } catch (TelegramSDKException $e) {
                var_dump($e);
                continue;
            }
        }
        return $results;
    }

    public function saveCheck(TelegramUser $user, $checks)
    {
        foreach ($checks as $k => $check) {
            $group = Group::where('telegram_user_id', $user->id)->first();
            if ($check == 1) {
            if ($group) {
                $group->update([$k => $check]);
                    }else {
                        Group::create(['telegram_user_id' => $user->id, $k => $check,]);
                    }
                if (is_null($user->pay_day)) {
                    $user->update(['status' => $check, 'pay_day' => Carbon::now()->addDays(7)]);
                }
                $user->update(['status' => $check,]);
            }
        }
    }

    public function checkUserDay($chats)
    {
        $users = TelegramUser::where('status', 1)->get();
        foreach ($users as $user) {
            if ($user->pay_day < Carbon::now()->toDate()) {
                foreach ($chats as $chat){
                    try {
                        $this->telegram->kickChatMember(['chat_id' => $chat, 'user_id' => $user->id,]);
                        $user->update(['status' => 2]);
                    } catch (TelegramSDKException $e) {
                        continue;
                    }
                }
            }
            $date = date('Y-m-d', strtotime($user->pay_day. " - 3 day")) ;
            if ($date <= Carbon::now()->toDate()) {
                try {
                    $this->telegram->sendMessage([
                        'chat_id' => $user->id,
                        'text' => "Привет, оплати дае а то из группы потеряещся до" . $user->pay_day,
                    ]);
                } catch (TelegramSDKException $e) {
                    continue;
                }
            }
        }

    }

    public function checkUserPayDay()
    {
        $users = TelegramUser::all();
        foreach ($users as $user) {
            if ($user->pay == 1) {
                $pay_day = $user->pay_day;
                $user->update(['pay_day' => date('Y-m-d', strtotime($pay_day. " +10 day")), 'pay' => 0,]);
            }
        }
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
