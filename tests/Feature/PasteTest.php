<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Paste;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PasteTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_can_create_paste()
    {
        $response = $this->post(route('pastes.store'), [
            'content' => 'This is a test paste.',
            'expiration' => '1day',
        ]);
        $response->assertRedirect(route('pastes.show', Paste::first()->slug));

        $this->assertDatabaseHas('pastes', [
            'content' => 'This is a test paste.',
        ]);
    }

    public function test_cannot_create_paste_with_missing_content()
    {
        $response = $this->post(route('pastes.store'), [
            'expiration' => '1day',
        ]);

        $response->assertSessionHasErrors('content');
        $this->assertDatabaseCount('pastes', 0);
    }

    public function test_cannot_create_paste_with_invalid_expiration()
    {
        $response = $this->post(route('pastes.store'), [
            'content' => 'This is a test paste.',
            'expiration' => 'invalid',
        ]);

        $response->assertSessionHasErrors('expiration');
        $this->assertDatabaseCount('pastes', 0);
    }

    public function test_cannot_see_expired_paste()
    {
        $paste = Paste::factory()->create([
            'expired_at' => now()->subDay(),
        ]);

        $response = $this->get(route('pastes.show', $paste->slug));

        $response->assertNotFound();
    }

    public function test_expired_paste_is_deleted()
    {
        $paste = Paste::factory()->create([
            'expired_at' => now()->subDay(),
        ]);

        $this->artisan('delete-expired-pastes');

        $this->assertDatabaseMissing('pastes', [
            'id' => $paste->id,
        ]);
    }

    public function test_command_is_run_by_scheduler_at_midnight()
    {
        Event::fake();
        Paste::factory()->create([
            'expired_at' => now()->subDay(2),
        ]);

        //travel to midnight 
        $this->travelTo(now()->startOfDay());
        $this->artisan('schedule:run');
        Event::assertDispatched(ScheduledTaskFinished::class, function ($event) {
            return strpos($event->task->command, 'delete-expired-pastes') !== false;
        });
        //1 am
        $this->travelTo(now()->hour(1));
        $this->artisan('schedule:run');
        Event::assertNotDispatched(ScheduledTaskFinished::class, function ($event) {
            return strpos($event->task->command, 'delete-expired-pastes') === false;
        });
    }

    function test_long_content_is_truncated_when_creating_paste()
    {
        $longContent = str_repeat('a', 70000); // Create a string that's longer than our limit

        $response = $this->post(route('pastes.store'), [
            'content' => $longContent,
            'expiration' => '1day',
        ]);
        $response->assertRedirect();
        $contentCreated = Paste::first()->content;
        $this->assertEquals(65000, strlen($contentCreated));
        $this->assertEquals(str_repeat('a', 65000),  $contentCreated);
    }
}
