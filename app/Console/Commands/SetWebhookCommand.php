<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Telegram\Bot\Api;
class SetWebhookCommand extends Command
{
    protected $signature = 'telegram:webhook';
    protected $description = 'Register the configured HTTPS webhook and its verification secret';
    public function handle()
    {
        $url = config('telegram.bots.mybot.webhook_url');
        $secret = config('telegram.webhook_secret');
        if (parse_url($url ?? '', PHP_URL_SCHEME) !== 'https' || !is_string($secret)
            || !preg_match('/^[A-Za-z0-9_-]{32,256}$/', $secret)) {
            $this->error('Configure an HTTPS TELEGRAM_WEBHOOK_URL and a 32–256 character TELEGRAM_WEBHOOK_SECRET.');
            return 1;
        }
        $bot = new Api(config('telegram.bots.mybot.token'));
        $bot->setWebhook(['url' => $url, 'secret_token' => $secret]);
        $this->info('Webhook registered.');
        return 0;
    }
}
