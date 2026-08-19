<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Extra;
use App\Models\ExtraType;
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
        $response->assertSee('Pasākumi');
        $response->assertSee('Vēl nav pasākumu');
    }

    public function test_events_index_lists_events(): void
    {
        $event = Event::query()->create([
            'name' => 'Community Night',
            'date' => now()->addWeek(),
            'description' => 'An evening at KM12.',
            'ticket_url' => 'https://example.com/tickets',
            'poster_path' => null,
        ]);

        $response = $this->get(route('events.index'));

        $response->assertOk();
        $response->assertSee('Community Night');
        $response->assertSee('/events/community-night', false);
        $response->assertSee(route('events.show', $event), false);
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
        $response->assertSee('Biļetes');
        $response->assertSee('https://example.com/tickets/workshop');
    }

    public function test_events_show_uses_a_slug_from_the_event_name(): void
    {
        $event = Event::query()->create([
            'name' => 'Saknes un Asni 2026',
            'date' => now()->addDays(3),
            'description' => 'A forest gathering.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $this->assertSame('saknes-un-asni-2026', $event->slug);
        $this->assertStringEndsWith('/events/saknes-un-asni-2026', route('events.show', $event));

        $this->get(route('events.show', $event))
            ->assertOk()
            ->assertSee('Saknes un Asni 2026');
    }

    public function test_numeric_event_urls_redirect_to_the_slug(): void
    {
        $event = Event::query()->create([
            'name' => 'Saknes un Asni 2026',
            'date' => now()->addDays(3),
            'description' => 'A forest gathering.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $this->get('/events/'.$event->id)
            ->assertRedirectToRoute('events.show', $event)
            ->assertStatus(301);
    }

    public function test_duplicate_event_names_get_unique_slugs(): void
    {
        $first = Event::query()->create([
            'name' => 'Community Night',
            'date' => now()->addWeek(),
            'description' => 'First night.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $second = Event::query()->create([
            'name' => 'Community Night',
            'date' => now()->addMonth(),
            'description' => 'Second night.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $this->assertSame('community-night', $first->slug);
        $this->assertSame('community-night-2', $second->slug);

        $this->get('/events/community-night')->assertOk()->assertSee('First night.');
        $this->get('/events/community-night-2')->assertOk()->assertSee('Second night.');
    }

    public function test_event_slug_updates_when_the_name_changes(): void
    {
        $event = Event::query()->create([
            'name' => 'Old Name',
            'date' => now()->addDays(3),
            'description' => 'An event.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $this->assertSame('old-name', $event->slug);

        $event->update(['name' => 'Jāņa Čakste 2026']);

        $this->assertSame('jana-cakste-2026', $event->fresh()->slug);
    }

    public function test_numeric_event_names_do_not_clash_with_id_urls(): void
    {
        $event = Event::query()->create([
            'name' => '2026',
            'date' => now()->addDays(3),
            'description' => 'A numbered night.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $this->assertSame('event-2026', $event->slug);

        $this->get('/events/'.$event->id)
            ->assertRedirectToRoute('events.show', $event)
            ->assertStatus(301);

        $this->get('/events/event-2026')->assertOk()->assertSee('A numbered night.');
    }

    public function test_unknown_event_slug_and_id_are_not_found(): void
    {
        $this->get('/events/does-not-exist')->assertNotFound();
        $this->get('/events/999')->assertNotFound();
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
            'instagram_url' => 'https://instagram.com/djalice',
            'website_url' => 'https://djalice.example',
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
            'date' => '2026-08-28',
            'starts_at' => '22:00',
            'ends_at' => '23:00',
            'notes' => null,
        ]);

        ScheduleEntry::query()->create([
            'event_participant_id' => $appearance->id,
            'stage_id' => $mainStage->id,
            'date' => '2026-08-29',
            'starts_at' => '21:00',
            'ends_at' => '22:00',
            'notes' => null,
        ]);

        ScheduleEntry::query()->create([
            'event_participant_id' => $appearance->id,
            'stage_id' => $forestStage->id,
            'date' => '2026-08-29',
            'starts_at' => '23:00',
            'ends_at' => '01:00',
            'notes' => 'Late set',
        ]);

        $response = $this->get(route('events.show', $event));

        $response->assertOk();
        $timezone = config('app.timezone');
        $friday = now()->setDate(2026, 8, 28)->timezone($timezone)->locale('lv')->translatedFormat('l, j. F');
        $saturday = now()->setDate(2026, 8, 29)->timezone($timezone)->locale('lv')->translatedFormat('l, j. F');

        $response->assertSeeInOrder([
            'A night in the woods.',
            'Programma',
            'Mākslinieki',
        ]);
        $response->assertSee('DJ Alice');
        $response->assertSee('DJ');
        $response->assertSee('House');
        $response->assertSee('Ambient');
        $response->assertSee('Deep house and ambient.');
        $response->assertSee('https://instagram.com/djalice', false);
        $response->assertSee('https://djalice.example', false);
        $response->assertSee('aria-label="Instagram"', false);
        $response->assertSee('aria-label="Mājaslapa"', false);
        $response->assertSeeInOrder([
            $friday,
            '22:00',
            'Main Stage',
            $saturday,
            '21:00',
            'Main Stage',
            '23:00',
            'Forest Stage',
            'Late set',
        ]);
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
        $response->assertDontSee('Mākslinieki');
        $response->assertDontSee('Papildus');
        $response->assertDontSee('id="schedule"', false);
    }

    public function test_events_show_renders_extras_alongside_artists_on_the_same_stage(): void
    {
        $event = Event::query()->create([
            'name' => 'Techno Night',
            'date' => now()->setDate(2026, 8, 28)->setTime(20, 0),
            'description' => 'DJs and drinks.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $djType = ParticipantType::query()->create([
            'name' => 'DJ',
            'sort_order' => 0,
        ]);

        $barType = ExtraType::query()->create([
            'name' => 'Bar',
            'sort_order' => 0,
        ]);

        $technoStage = Stage::query()->create([
            'name' => 'Techno Stage',
            'sort_order' => 0,
        ]);

        $alice = Artist::query()->create([
            'participant_type_id' => $djType->id,
            'name' => 'DJ Alice',
            'bio' => 'Peak time techno.',
            'image_path' => null,
        ]);

        $bar = Extra::query()->create([
            'extra_type_id' => $barType->id,
            'name' => 'Sideline Bar',
            'bio' => 'Drinks on the sidelines.',
            'instagram_url' => 'https://instagram.com/sidelinebar',
            'website_url' => 'https://sidelinebar.example',
            'image_path' => null,
        ]);

        $artistAppearance = EventParticipant::query()->create([
            'event_id' => $event->id,
            'artist_id' => $alice->id,
            'sort_order' => 0,
        ]);

        $extraAppearance = EventParticipant::query()->create([
            'event_id' => $event->id,
            'extra_id' => $bar->id,
            'sort_order' => 1,
        ]);

        ScheduleEntry::query()->create([
            'event_participant_id' => $artistAppearance->id,
            'stage_id' => $technoStage->id,
            'date' => '2026-08-28',
            'starts_at' => '22:00',
            'ends_at' => '23:30',
            'notes' => null,
        ]);

        ScheduleEntry::query()->create([
            'event_participant_id' => $extraAppearance->id,
            'stage_id' => $technoStage->id,
            'date' => '2026-08-28',
            'starts_at' => '21:00',
            'ends_at' => '02:00',
            'notes' => 'Serving throughout the night',
        ]);

        $response = $this->get(route('events.show', $event));

        $response->assertOk();
        $response->assertSee('Mākslinieki');
        $response->assertSee('DJ Alice');
        $response->assertSee('Papildus');
        $response->assertSee('Sideline Bar');
        $response->assertSee('Bar');
        $response->assertSee('Drinks on the sidelines.');
        $response->assertSee('https://instagram.com/sidelinebar', false);
        $response->assertSee('https://sidelinebar.example', false);
        $response->assertSeeInOrder([
            '21:00',
            'Sideline Bar',
            'Techno Stage',
            '22:00',
            'DJ Alice',
        ]);
        $response->assertSee('Serving throughout the night');
        $response->assertSee('id="extras-heading"', false);
        $response->assertSee('id="participant-'.$extraAppearance->id.'"', false);
    }

    public function test_extras_can_be_reused_across_events_and_persist_after_deletion(): void
    {
        $barType = ExtraType::query()->create([
            'name' => 'Bar',
            'sort_order' => 0,
        ]);

        $extra = Extra::query()->create([
            'extra_type_id' => $barType->id,
            'name' => 'Sideline Bar',
            'bio' => 'Drinks on the sidelines.',
            'image_path' => null,
        ]);

        $firstEvent = Event::query()->create([
            'name' => 'Techno Night',
            'date' => now()->addWeek(),
            'description' => 'DJs and drinks.',
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
            'extra_id' => $extra->id,
            'sort_order' => 0,
        ]);

        EventParticipant::query()->create([
            'event_id' => $secondEvent->id,
            'extra_id' => $extra->id,
            'sort_order' => 0,
        ]);

        $this->get(route('events.show', $firstEvent))
            ->assertSee('Papildus')
            ->assertSee('Sideline Bar')
            ->assertDontSee('Mākslinieki');

        $this->get(route('events.show', $secondEvent))->assertSee('Sideline Bar');

        $firstEvent->delete();

        $this->assertDatabaseHas('extras', [
            'id' => $extra->id,
            'name' => 'Sideline Bar',
        ]);

        $this->get(route('events.show', $secondEvent))->assertSee('Sideline Bar');
    }

    public function test_events_show_renders_untimed_schedule_entries_with_their_day(): void
    {
        $event = Event::query()->create([
            'name' => 'Techno Night',
            'date' => now()->setDate(2026, 8, 28)->setTime(20, 0),
            'description' => 'DJs and drinks.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $barType = ExtraType::query()->create([
            'name' => 'Bar',
            'sort_order' => 0,
        ]);

        $technoStage = Stage::query()->create([
            'name' => 'Techno Stage',
            'sort_order' => 0,
        ]);

        $fridayBar = Extra::query()->create([
            'extra_type_id' => $barType->id,
            'name' => 'Friday Bar',
            'bio' => 'Open on Friday.',
            'image_path' => null,
        ]);

        $saturdayBar = Extra::query()->create([
            'extra_type_id' => $barType->id,
            'name' => 'Saturday Bar',
            'bio' => 'Open on Saturday.',
            'image_path' => null,
        ]);

        $fridayAppearance = EventParticipant::query()->create([
            'event_id' => $event->id,
            'extra_id' => $fridayBar->id,
            'sort_order' => 0,
        ]);

        $saturdayAppearance = EventParticipant::query()->create([
            'event_id' => $event->id,
            'extra_id' => $saturdayBar->id,
            'sort_order' => 1,
        ]);

        ScheduleEntry::query()->create([
            'event_participant_id' => $fridayAppearance->id,
            'stage_id' => $technoStage->id,
            'date' => '2026-08-28',
            'starts_at' => null,
            'ends_at' => null,
            'notes' => null,
        ]);

        ScheduleEntry::query()->create([
            'event_participant_id' => $saturdayAppearance->id,
            'stage_id' => $technoStage->id,
            'date' => '2026-08-29',
            'starts_at' => null,
            'ends_at' => null,
            'notes' => null,
        ]);

        $response = $this->get(route('events.show', $event));

        $response->assertOk();
        $timezone = config('app.timezone');
        $fridayHeading = now()->setDate(2026, 8, 28)->timezone($timezone)->locale('lv')->translatedFormat('l, j. F');
        $saturdayHeading = now()->setDate(2026, 8, 29)->timezone($timezone)->locale('lv')->translatedFormat('l, j. F');

        $response->assertSeeInOrder([
            $fridayHeading,
            'Visu dienu',
            'Friday Bar',
            'Techno Stage',
            $saturdayHeading,
            'Visu dienu',
            'Saturday Bar',
        ]);
        $response->assertDontSee('00:00');
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
        $response->assertSee('Pasākumi');
        $response->assertDontSee('Door opener');
    }
}
