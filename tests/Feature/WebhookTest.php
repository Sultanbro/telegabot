<?php
namespace Tests\Feature;
use Tests\TestCase;
class WebhookTest extends TestCase
{
    public function test_missing_and_incorrect_secrets_are_rejected(): void
    {
        config(['telegram.webhook_secret' => 'local-demo-webhook-secret']);
        $this->postJson('/api/telegram/webhook', [])->assertForbidden();
        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', 'wrong')->postJson('/api/telegram/webhook', [])->assertForbidden();
    }
    public function test_empty_configuration_denies_access(): void
    {
        config(['telegram.webhook_secret' => '']);
        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', '')->postJson('/api/telegram/webhook', [])->assertForbidden();
    }
    public function test_verified_empty_update_is_acknowledged_without_contacting_telegram(): void
    {
        config(['telegram.webhook_secret' => 'local-demo-webhook-secret']);
        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', 'local-demo-webhook-secret')->postJson('/api/telegram/webhook', ['update_id' => 1])->assertOk();
    }
    public function test_management_endpoints_are_not_public(): void
    {
        foreach (['/get-me', '/set-hook', '/upda'] as $path) $this->getJson($path)->assertNotFound();
    }
    public function test_webhook_registration_requires_valid_configuration(): void
    {
        config(['telegram.webhook_secret' => '', 'telegram.bots.mybot.webhook_url' => 'http://localhost']);
        $this->artisan('telegram:webhook')->assertExitCode(1);
    }
}
