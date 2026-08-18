<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\ParticipantType;
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
}
