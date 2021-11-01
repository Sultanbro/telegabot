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
        $url = 'https://6b3e-87-255-197-10.ngrok.io/' . env('TELEGRAM_BOT_TOKEN');
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
        $response = $this->telegram->getChatMembersCount(['chat_id' => env('CHAT_ID'),]);
        $response2 = $this->telegram->getChatMember(['chat_id' => env('CHAT_ID'), 'user_id' => 844867712,]);
        $response3 = $this->telegram->getChatMember(['chat_id' => env('CHAT_ID'), 'user_id' => 529595184,]);
//        $response4 = $this->telegram->get();

        dd($response, $response2, $response3);
    }

    public function saveTelegramUser()
    {
        TelegramUser::firstOrCreate($this->from);

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

//    public function userInGroup($user_id, $chat_id)
//    {
//        $response = $this->telegram->getChatMember(['chat_id' => $chat_id, 'user_id' => $user_id,]);
//        if ($response->status == 'restricted') {
//            return true;
//        }
//        return false;
//    }

    public function verificationSubs()
    {
        $users = TelegramUser::all();

        foreach ($users as $user) {
            try {
                $response = $this->telegram->getChatMember(['chat_id' => env('CHAT_ID'), 'user_id' => $user->id,]);
                if ($response->status == 'restricted') {
                    $this->checkUserStatus($user);
                }
            } catch (TelegramSDKException $e) {
                continue;
            }
        }

    }

    public function checkUserStatus($user)
    {
        $user_group = Group::where('telegram_user_id', $user->id)->get();
        if ($user_group > 0) {
            return Group::created(['telegram_user_id' => $user->id, 'group_id' => env('CHAT_ID'), 'pay_day' => Carbon::now()->addDays(5)]);
        }
        $this->checkUserPay($user);

    }

    public function checkUserPay($user)
    {
        if ($user->join_status == 1) $user->pay = 1;
        if ($user->pay == 1) {
            if ($user->join->date() < Carbon::now()->toDate()) {
                $this->telegram->kickChatMember(['chat_id' => env('CHAT_ID'), 'user_id' => $user->id, ]);
            }
//            elseif ($user->join->date() )
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
