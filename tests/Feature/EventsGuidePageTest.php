<?php

namespace Tests\Feature;

use App\Filament\Pages\Guide;
use App\Filament\Resources\Events\EventResource;
use App\Models\Event;
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

    public function test_admin_event_pages_keep_using_ids_not_slugs(): void
    {
        $admin = User::factory()->admin()->create();

        $event = Event::query()->create([
            'name' => 'Saknes un Asni 2026',
            'date' => now()->addWeek(),
            'description' => 'A forest gathering.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $editUrl = EventResource::getUrl('edit', ['record' => $event], panel: 'admin');

        $this->assertStringContainsString('/admin/events/'.$event->id.'/edit', $editUrl);
        $this->assertStringNotContainsString($event->slug, $editUrl);

        $this->actingAs($admin)
            ->get(EventResource::getUrl('index', panel: 'admin'))
            ->assertOk()
            ->assertSee('Saknes un Asni 2026');

        $this->actingAs($admin)
            ->get($editUrl)
            ->assertOk()
            ->assertSee('Saknes un Asni 2026');
    }
}
