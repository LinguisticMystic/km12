<?php

namespace Tests\Feature;

use App\Filament\Resources\Quests\Pages\CreateQuest;
use App\Filament\Resources\Quests\QuestResource;
use App\Models\Quest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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
            ->assertSee('Kvestu dēlis')
            ->assertSee('Vēl nav kvestu');
    }

    public function test_authenticated_users_see_quests_on_the_quest_board(): void
    {
        $user = User::factory()->create();
        $creator = User::factory()->admin()->create([
            'name' => 'Aija Admin',
        ]);
        Quest::factory()->create([
            'name' => 'Sweep the yard',
            'description' => 'Pick up litter around the building.',
            'experience_points' => 25,
            'created_by' => $creator->id,
        ]);

        $this->actingAs($user)
            ->get(route('quest-board'))
            ->assertOk()
            ->assertSee('Sweep the yard')
            ->assertSee('Pick up litter around the building.')
            ->assertSee('25 XP')
            ->assertSee('Izveidoja Aija Admin');
    }

    public function test_non_admins_cannot_view_the_admin_quest_list(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(QuestResource::getUrl('index', panel: 'admin'))
            ->assertForbidden();
    }

    public function test_admins_can_view_the_admin_quest_list(): void
    {
        $admin = User::factory()->admin()->create();
        Quest::factory()->create([
            'name' => 'Water the plants',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(QuestResource::getUrl('index', panel: 'admin'))
            ->assertOk()
            ->assertSee('Water the plants')
            ->assertSee($admin->name);
    }

    public function test_admins_can_create_a_quest_and_are_stored_as_the_creator(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        Livewire::test(CreateQuest::class)
            ->fillForm([
                'name' => 'Open the gallery',
                'description' => 'Unlock the space before the event.',
                'experience_points' => 40,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('quests', [
            'name' => 'Open the gallery',
            'description' => 'Unlock the space before the event.',
            'experience_points' => 40,
            'created_by' => $admin->id,
            'status' => 'available',
        ]);
    }

    public function test_available_quests_show_available_status_and_can_be_started(): void
    {
        $user = User::factory()->create();
        $quest = Quest::factory()->create([
            'name' => 'Sweep the yard',
        ]);

        $this->actingAs($user)
            ->get(route('quest-board'))
            ->assertOk()
            ->assertSee('Pieejams')
            ->assertSee('Sākt kvestu');

        $this->actingAs($user)
            ->post(route('quests.start', $quest))
            ->assertRedirect();

        $this->assertDatabaseHas('quests', [
            'id' => $quest->id,
            'status' => 'in_progress',
            'taken_by' => $user->id,
        ]);
    }

    public function test_quest_taker_sees_submitted_status_but_other_users_see_in_progress(): void
    {
        $taker = User::factory()->create();
        $other = User::factory()->create();
        $quest = Quest::factory()->submitted($taker)->create([
            'name' => 'Sweep the yard',
        ]);

        $this->actingAs($taker)
            ->get(route('quest-board'))
            ->assertOk()
            ->assertSee('Iesniegts')
            ->assertDontSee('Procesā')
            ->assertDontSee('Sākt kvestu')
            ->assertDontSee('Iesniegt kvestu');

        $this->actingAs($other)
            ->get(route('quest-board'))
            ->assertOk()
            ->assertSee('Procesā')
            ->assertDontSee('Iesniegts')
            ->assertDontSee('Sākt kvestu')
            ->assertDontSee('Iesniegt kvestu');
    }

    public function test_quest_taker_sees_rejected_status_but_other_users_see_in_progress(): void
    {
        $taker = User::factory()->create();
        $other = User::factory()->create();
        $quest = Quest::factory()->rejected($taker)->create([
            'name' => 'Sweep the yard',
        ]);

        $this->actingAs($taker)
            ->get(route('quest-board'))
            ->assertOk()
            ->assertSee('Noraidīts')
            ->assertDontSee('Procesā')
            ->assertSee('Iesniegt kvestu');

        $this->actingAs($other)
            ->get(route('quest-board'))
            ->assertOk()
            ->assertSee('Procesā')
            ->assertDontSee('Noraidīts')
            ->assertDontSee('Iesniegt kvestu');
    }

    public function test_quest_taker_can_submit_an_in_progress_quest(): void
    {
        $taker = User::factory()->create();
        $quest = Quest::factory()->inProgress($taker)->create();

        $this->actingAs($taker)
            ->get(route('quest-board'))
            ->assertOk()
            ->assertSee('Procesā')
            ->assertSee('Iesniegt kvestu');

        $this->actingAs($taker)
            ->post(route('quests.submit', $quest))
            ->assertRedirect();

        $this->assertDatabaseHas('quests', [
            'id' => $quest->id,
            'status' => 'submitted',
        ]);
    }

    public function test_other_users_cannot_submit_a_quest_they_did_not_take(): void
    {
        $taker = User::factory()->create();
        $other = User::factory()->create();
        $quest = Quest::factory()->inProgress($taker)->create();

        $this->actingAs($other)
            ->post(route('quests.submit', $quest))
            ->assertForbidden();
    }

    public function test_completed_quests_are_visible_to_everyone(): void
    {
        $taker = User::factory()->create();
        $other = User::factory()->create();
        Quest::factory()->completed($taker)->create([
            'name' => 'Sweep the yard',
        ]);

        $this->actingAs($taker)
            ->get(route('quest-board'))
            ->assertOk()
            ->assertSee('Pabeigts');

        $this->actingAs($other)
            ->get(route('quest-board'))
            ->assertOk()
            ->assertSee('Pabeigts');
    }

    public function test_admins_see_the_real_submitted_status_in_the_admin_list(): void
    {
        $admin = User::factory()->admin()->create();
        $taker = User::factory()->create(['name' => 'Quest Taker']);
        Quest::factory()->submitted($taker)->create([
            'name' => 'Sweep the yard',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(QuestResource::getUrl('index', panel: 'admin'))
            ->assertOk()
            ->assertSee('Sweep the yard')
            ->assertSee('Submitted')
            ->assertSee('Quest Taker');
    }
}
