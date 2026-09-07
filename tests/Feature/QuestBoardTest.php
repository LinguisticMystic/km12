<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_do_not_see_quest_board_on_the_home_page(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Kvestu dēlis')
            ->assertDontSee(route('quest-board'), false);
    }

    public function test_guests_are_redirected_to_login_when_visiting_the_quest_board(): void
    {
        $this->get(route('quest-board'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_users_see_quest_board_on_the_home_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Kvestu dēlis')
            ->assertSee(route('quest-board'), false);
    }

    public function test_authenticated_users_can_view_the_quest_board(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('quest-board'))
            ->assertOk()
            ->assertSee('Kvestu dēlis');
    }
}
