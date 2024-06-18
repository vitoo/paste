<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Paste;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
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
}
