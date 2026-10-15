<?php
namespace Tests\Feature;
use App\Http\Controllers\TelegramController;
use App\Models\TrialLink;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Telegram\Bot\Api;
class TrialAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        Schema::create('trial_links', function ($table) {
            $table->id(); $table->bigInteger('telegram_user_id'); $table->boolean('trial_link')->default(false); $table->timestamps();
        });
        Http::fake(['*' => Http::response(['result' => ['invite_link' => 'https://example.invalid/demo-invite']])]);
    }
    private function controller($userId)
    {
        $controller = new TelegramController();
        $api = \Mockery::mock(Api::class);
        $api->shouldReceive('sendMessage')->once()->andReturn(new \Telegram\Bot\Objects\Message([]));
        foreach (['telegram' => $api, 'from' => ['id' => $userId], 'chat_id' => $userId] as $name => $value) {
            $property = new \ReflectionProperty($controller, $name);
            $property->setAccessible(true); $property->setValue($controller, $value);
        }
        return $controller;
    }
    public function test_new_user_receives_trial_even_when_another_user_has_used_it(): void
    {
        TrialLink::create(['telegram_user_id' => 1, 'trial_link' => 1]);
        $this->controller(2)->trialLinkGroup();
        $this->assertDatabaseHas('trial_links', ['telegram_user_id' => 2, 'trial_link' => 1]);
        Http::assertSentCount(2);
    }
    public function test_repeated_trial_does_not_create_invite_links(): void
    {
        TrialLink::create(['telegram_user_id' => 1, 'trial_link' => 1]);
        $this->controller(1)->trialLinkGroup();
        Http::assertNothingSent();
        $this->assertDatabaseCount('trial_links', 1);
    }
}
