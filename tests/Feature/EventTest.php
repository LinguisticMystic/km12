<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Genre;
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

    public function test_events_show_renders_artists_and_schedule(): void
    {
        $event = Event::query()->create([
            'name' => 'Forest Gathering',
            'date' => now()->setDate(2026, 8, 28)->setTime(20, 0),
            'description' => 'A night in the woods.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $djType = ParticipantType::query()->create([
            'name' => 'DJ',
            'sort_order' => 0,
        ]);

        $house = Genre::query()->create([
            'name' => 'House',
            'sort_order' => 0,
        ]);

        $ambient = Genre::query()->create([
            'name' => 'Ambient',
            'sort_order' => 1,
        ]);

        $mainStage = Stage::query()->create([
            'name' => 'Main Stage',
            'sort_order' => 0,
        ]);

        $forestStage = Stage::query()->create([
            'name' => 'Forest Stage',
            'sort_order' => 1,
        ]);

        $alice = Artist::query()->create([
            'participant_type_id' => $djType->id,
            'name' => 'DJ Alice',
            'bio' => 'Deep house and ambient.',
            'image_path' => null,
        ]);

        $alice->genres()->attach([$house->id, $ambient->id]);

        $appearance = EventParticipant::query()->create([
            'event_id' => $event->id,
            'artist_id' => $alice->id,
            'sort_order' => 0,
        ]);

        ScheduleEntry::query()->create([
            'event_participant_id' => $appearance->id,
            'stage_id' => $mainStage->id,
            'starts_at' => now()->setDate(2026, 8, 28)->setTime(22, 0),
            'ends_at' => now()->setDate(2026, 8, 28)->setTime(23, 0),
            'notes' => null,
        ]);

        ScheduleEntry::query()->create([
            'event_participant_id' => $appearance->id,
            'stage_id' => $mainStage->id,
            'starts_at' => now()->setDate(2026, 8, 29)->setTime(21, 0),
            'ends_at' => now()->setDate(2026, 8, 29)->setTime(22, 0),
            'notes' => null,
        ]);

        ScheduleEntry::query()->create([
            'event_participant_id' => $appearance->id,
            'stage_id' => $forestStage->id,
            'starts_at' => now()->setDate(2026, 8, 29)->setTime(23, 0),
            'ends_at' => now()->setDate(2026, 8, 30)->setTime(1, 0),
            'notes' => 'Late set',
        ]);

        $response = $this->get(route('events.show', $event));

        $response->assertOk();
        $response->assertSee('Artists');
        $response->assertSee('DJ Alice');
        $response->assertSee('DJ');
        $response->assertSee('House');
        $response->assertSee('Ambient');
        $response->assertSee('Deep house and ambient.');
        $response->assertSee('Schedule');
        $response->assertSee('Main Stage');
        $response->assertSee('Forest Stage');
        $response->assertSee('Friday, 28 August');
        $response->assertSee('Saturday, 29 August');
        $response->assertSee('22:00');
        $response->assertSee('21:00');
        $response->assertSee('Late set');
        $response->assertSee('schedule-entry-', false);
    }

    public function test_events_show_hides_empty_artist_sections(): void
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
        $response->assertDontSee('Artists');
        $response->assertDontSee('id="schedule"', false);
    }

    public function test_artists_can_be_reused_across_events_and_persist_after_deletion(): void
    {
        $djType = ParticipantType::query()->create([
            'name' => 'DJ',
            'sort_order' => 0,
        ]);

        $artist = Artist::query()->create([
            'participant_type_id' => $djType->id,
            'name' => 'DJ Alice',
            'bio' => 'Deep house and ambient.',
            'image_path' => null,
        ]);

        $firstEvent = Event::query()->create([
            'name' => 'Forest Gathering',
            'date' => now()->addWeek(),
            'description' => 'A night in the woods.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $secondEvent = Event::query()->create([
            'name' => 'Warehouse Session',
            'date' => now()->addMonth(),
            'description' => 'Another night.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        EventParticipant::query()->create([
            'event_id' => $firstEvent->id,
            'artist_id' => $artist->id,
            'sort_order' => 0,
        ]);

        EventParticipant::query()->create([
            'event_id' => $secondEvent->id,
            'artist_id' => $artist->id,
            'sort_order' => 0,
        ]);

        $this->get(route('events.show', $firstEvent))->assertSee('DJ Alice');
        $this->get(route('events.show', $secondEvent))->assertSee('DJ Alice');

        $firstEvent->delete();

        $this->assertDatabaseHas('artists', [
            'id' => $artist->id,
            'name' => 'DJ Alice',
        ]);

        $this->get(route('events.show', $secondEvent))->assertSee('DJ Alice');
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
