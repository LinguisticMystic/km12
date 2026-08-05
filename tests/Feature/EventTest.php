<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_events_index_is_successful(): void
    {
        $response = $this->get(route('events.index'));

        $response->assertOk();
        $response->assertSee('Events');
        $response->assertSee('No events yet');
    }

    public function test_events_index_lists_events(): void
    {
        Event::query()->create([
            'name' => 'Community Night',
            'date' => now()->addWeek(),
            'description' => 'An evening at KM12.',
            'ticket_url' => 'https://example.com/tickets',
            'poster_path' => null,
        ]);

        $response = $this->get(route('events.index'));

        $response->assertOk();
        $response->assertSee('Community Night');
        $response->assertDontSee('No events yet');
    }

    public function test_events_show_is_successful(): void
    {
        $event = Event::query()->create([
            'name' => 'Workshop',
            'date' => now()->addDays(3),
            'description' => 'Learn something new.',
            'ticket_url' => 'https://example.com/tickets/workshop',
            'poster_path' => null,
        ]);

        $response = $this->get(route('events.show', $event));

        $response->assertOk();
        $response->assertSee('Workshop');
        $response->assertSee('Learn something new.');
        $response->assertSee('Get tickets');
        $response->assertSee('https://example.com/tickets/workshop');
    }

    public function test_home_page_links_to_events_and_hides_door_opener(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee(route('events.index'), false);
        $response->assertSee('Events');
        $response->assertDontSee('Door opener');
    }
}
