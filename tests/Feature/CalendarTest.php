<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_do_not_see_calendar_on_the_home_page(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Kalendārs')
            ->assertDontSee(route('calendar'), false);
    }

    public function test_guests_are_redirected_to_login_when_visiting_the_calendar(): void
    {
        $this->get(route('calendar'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_users_see_calendar_on_the_home_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Kalendārs')
            ->assertSee(route('calendar'), false);
    }

    public function test_authenticated_users_can_view_the_calendar(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('calendar'))
            ->assertOk()
            ->assertSee('Kalendārs');
    }
}
