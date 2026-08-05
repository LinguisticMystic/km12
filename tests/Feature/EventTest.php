<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\ParticipantType;
use App\Models\ScheduleEntry;
use App\Models\Stage;
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

    public function test_events_show_renders_participants_and_schedule(): void
    {
        $event = Event::query()->create([
            'name' => 'Forest Gathering',
            'date' => now()->addDays(10),
            'description' => 'A night in the woods.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $djType = ParticipantType::query()->create([
            'name' => 'DJ',
            'sort_order' => 0,
        ]);

        $mainStage = Stage::query()->create([
            'name' => 'Main Stage',
            'sort_order' => 0,
        ]);

        $forestStage = Stage::query()->create([
            'name' => 'Forest Stage',
            'sort_order' => 1,
        ]);

        $alice = EventParticipant::query()->create([
            'event_id' => $event->id,
            'participant_type_id' => $djType->id,
            'name' => 'DJ Alice',
            'bio' => 'Deep house and ambient.',
            'image_path' => null,
            'sort_order' => 0,
        ]);

        ScheduleEntry::query()->create([
            'event_participant_id' => $alice->id,
            'stage_id' => $mainStage->id,
            'starts_at' => now()->addDays(10)->setTime(20, 0),
            'ends_at' => now()->addDays(10)->setTime(22, 0),
            'notes' => null,
        ]);

        ScheduleEntry::query()->create([
            'event_participant_id' => $alice->id,
            'stage_id' => $forestStage->id,
            'starts_at' => now()->addDays(11)->setTime(1, 0),
            'ends_at' => now()->addDays(11)->setTime(3, 0),
            'notes' => 'Late set',
        ]);

        $response = $this->get(route('events.show', $event));

        $response->assertOk();
        $response->assertSee('Participants');
        $response->assertSee('DJ Alice');
        $response->assertSee('DJ');
        $response->assertSee('Deep house and ambient.');
        $response->assertSee('Schedule');
        $response->assertSee('Main Stage');
        $response->assertSee('Forest Stage');
        $response->assertSee('20:00');
        $response->assertSee('01:00');
        $response->assertSee('Late set');
        $response->assertSee('schedule-entry-', false);
    }

    public function test_events_show_hides_empty_participant_sections(): void
    {
        $event = Event::query()->create([
            'name' => 'Quiet Meetup',
            'date' => now()->addDays(2),
            'description' => 'No lineup yet.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $response = $this->get(route('events.show', $event));

        $response->assertOk();
        $response->assertDontSee('Participants');
        $response->assertDontSee('id="schedule"', false);
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
