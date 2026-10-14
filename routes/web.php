<?php

use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('get-me', [TelegramController::class, 'getMe']);
Route::get('set-hook', [TelegramController::class, 'setWebHook']);
Route::get('upda', [TelegramController::class, 'userInGroup']);
Route::post(Telegram::getAccessToken(), [TelegramController::class, 'handleRequest']);
