<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class VerifyTelegramWebhook
{
    public function handle(Request $request, Closure $next)
    {
        $expected = config('telegram.webhook_secret');
        $received = $request->header('X-Telegram-Bot-Api-Secret-Token');
        if (!is_string($expected) || $expected === '' || !is_string($received) || !hash_equals($expected, $received)) {
            abort(403);
        }
        return $next($request);
    }
}
