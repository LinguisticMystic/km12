<?php

namespace Tests\Feature;

use App\Filament\Pages\Guide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventsGuidePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_admin_login(): void
    {
        $this->get(Guide::getUrl(panel: 'admin'))
            ->assertRedirect();
    }

    public function test_non_admins_cannot_view_the_guide(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(Guide::getUrl(panel: 'admin'))
            ->assertForbidden();
    }

    public function test_admins_can_view_the_guide(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(Guide::getUrl(panel: 'admin'))
            ->assertOk()
            ->assertSee('Events guide')
            ->assertSee('Add artist')
            ->assertSee('Add extra')
            ->assertSee('Artists and extras live in their own lists.');
    }
}
