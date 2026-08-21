<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Extra;
use App\Models\ExtraType;
use App\Models\ParticipantType;
use App\Models\ScheduleEntry;
use App\Models\Stage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_defaults_to_latvian(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('lang="lv"', false);
        $response->assertSee('Pasākumi');
        $response->assertSee('Kalendārs');
        $response->assertSee('Par mums');
        $response->assertSee('Ienākt');
        $response->assertSee('Rīki un resursi');
    }

    public function test_language_switcher_changes_locale_for_the_session(): void
    {
        $this->from(route('home'))
            ->post(route('locale.update'), ['locale' => 'en'])
            ->assertRedirect(route('home'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('Events')
            ->assertSee('Calendar')
            ->assertSee('About')
            ->assertSee('Log in')
            ->assertSee('Tools and resources');

        $this->from(route('home'))
            ->post(route('locale.update'), ['locale' => 'lv'])
            ->assertRedirect(route('home'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('lang="lv"', false)
            ->assertSee('Pasākumi');
    }

    public function test_event_description_and_artist_bio_follow_the_active_locale(): void
    {
        $event = Event::query()->create([
            'name' => 'Workshop',
            'date' => now()->addDays(3),
            'description' => 'Latviešu pasākuma apraksts.',
            'description_en' => 'English event description.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $type = ParticipantType::query()->create([
            'name' => 'DJ',
            'sort_order' => 0,
        ]);

        $artist = Artist::query()->create([
            'participant_type_id' => $type->id,
            'name' => 'DJ Alice',
            'bio' => 'Latviešu biogrāfija.',
            'bio_en' => 'English artist bio.',
            'image_path' => null,
        ]);

        EventParticipant::query()->create([
            'event_id' => $event->id,
            'artist_id' => $artist->id,
            'sort_order' => 0,
        ]);

        $this->get(route('events.show', $event))
            ->assertOk()
            ->assertSee('Latviešu pasākuma apraksts.')
            ->assertSee('Latviešu biogrāfija.')
            ->assertDontSee('English event description.')
            ->assertDontSee('English artist bio.');

        $this->from(route('events.show', $event))
            ->post(route('locale.update'), ['locale' => 'en'])
            ->assertRedirect(route('events.show', $event));

        $this->get(route('events.show', $event))
            ->assertOk()
            ->assertSee('English event description.')
            ->assertSee('English artist bio.')
            ->assertDontSee('Latviešu pasākuma apraksts.')
            ->assertDontSee('Latviešu biogrāfija.');
    }

    public function test_localized_fields_fall_back_when_a_translation_is_missing(): void
    {
        $event = Event::query()->create([
            'name' => 'Workshop',
            'date' => now()->addDays(3),
            'description' => 'Tikai latviski.',
            'description_en' => null,
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $this->from(route('events.show', $event))
            ->post(route('locale.update'), ['locale' => 'en']);

        $this->get(route('events.show', $event))
            ->assertOk()
            ->assertSee('Tikai latviski.');
    }

    public function test_stage_type_and_notes_follow_the_active_locale(): void
    {
        $event = Event::query()->create([
            'name' => 'Workshop',
            'date' => now()->setDate(2026, 8, 28)->setTime(20, 0),
            'description' => 'Apraksts.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $djType = ParticipantType::query()->create([
            'name' => 'Dzīvā mūzika',
            'name_en' => 'Live musician',
            'sort_order' => 0,
        ]);

        $barType = ExtraType::query()->create([
            'name' => 'Tējas telts',
            'name_en' => 'Tea tent',
            'sort_order' => 0,
        ]);

        $stage = Stage::query()->create([
            'name' => 'Galvenā skatuve',
            'name_en' => 'Main Stage',
            'sort_order' => 0,
        ]);

        $artist = Artist::query()->create([
            'participant_type_id' => $djType->id,
            'name' => 'DJ Alice',
            'bio' => 'Biogrāfija.',
            'image_path' => null,
        ]);

        $bar = Extra::query()->create([
            'extra_type_id' => $barType->id,
            'name' => 'Sideline Bar',
            'bio' => 'Dzērieni.',
            'image_path' => null,
        ]);

        $artistAppearance = EventParticipant::query()->create([
            'event_id' => $event->id,
            'artist_id' => $artist->id,
            'sort_order' => 0,
        ]);

        $extraAppearance = EventParticipant::query()->create([
            'event_id' => $event->id,
            'extra_id' => $bar->id,
            'sort_order' => 1,
        ]);

        ScheduleEntry::query()->create([
            'event_participant_id' => $artistAppearance->id,
            'stage_id' => $stage->id,
            'date' => '2026-08-28',
            'starts_at' => '22:00',
            'ends_at' => '23:00',
            'notes' => 'Vēlais sets',
            'notes_en' => 'Late set',
        ]);

        ScheduleEntry::query()->create([
            'event_participant_id' => $extraAppearance->id,
            'stage_id' => $stage->id,
            'date' => '2026-08-28',
            'starts_at' => '21:00',
            'ends_at' => '02:00',
            'notes' => 'Strādā visu nakti',
            'notes_en' => 'Open all night',
        ]);

        $this->get(route('events.show', $event))
            ->assertOk()
            ->assertSee('Dzīvā mūzika')
            ->assertSee('Tējas telts')
            ->assertSee('Galvenā skatuve')
            ->assertSee('Vēlais sets')
            ->assertSee('Strādā visu nakti')
            ->assertDontSee('Live musician')
            ->assertDontSee('Tea tent')
            ->assertDontSee('Main Stage')
            ->assertDontSee('Late set')
            ->assertDontSee('Open all night');

        $this->from(route('events.show', $event))
            ->post(route('locale.update'), ['locale' => 'en'])
            ->assertRedirect(route('events.show', $event));

        $this->get(route('events.show', $event))
            ->assertOk()
            ->assertSee('Live musician')
            ->assertSee('Tea tent')
            ->assertSee('Main Stage')
            ->assertSee('Late set')
            ->assertSee('Open all night')
            ->assertDontSee('Dzīvā mūzika')
            ->assertDontSee('Tējas telts')
            ->assertDontSee('Galvenā skatuve')
            ->assertDontSee('Vēlais sets')
            ->assertDontSee('Strādā visu nakti');
    }

    public function test_event_dates_use_a_period_after_the_day_in_latvian_only(): void
    {
        $date = now()->setDate(2026, 8, 28)->setTime(20, 0);

        $event = Event::query()->create([
            'name' => 'Workshop',
            'date' => $date,
            'description' => 'A workshop.',
            'ticket_url' => null,
            'poster_path' => null,
        ]);

        $timezone = config('app.timezone');
        $localized = $date->timezone($timezone);

        $this->get(route('events.show', $event))
            ->assertOk()
            ->assertSee($localized->locale('lv')->translatedFormat('l, j. F Y · H:i'))
            ->assertDontSee($localized->locale('lv')->translatedFormat('l, j F Y · H:i'));

        $this->from(route('events.show', $event))
            ->post(route('locale.update'), ['locale' => 'en']);

        $this->get(route('events.show', $event))
            ->assertOk()
            ->assertSee($localized->locale('en')->translatedFormat('l, j F Y · H:i'));

        $this->get(route('events.index'))
            ->assertOk()
            ->assertSee($localized->locale('en')->translatedFormat('D, j M Y · H:i'));

        $this->from(route('events.index'))
            ->post(route('locale.update'), ['locale' => 'lv']);

        $this->get(route('events.index'))
            ->assertOk()
            ->assertSee($localized->locale('lv')->translatedFormat('D, j. M Y · H:i'));
    }
}
