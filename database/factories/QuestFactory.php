<?php

namespace Database\Factories;

use App\Enums\QuestStatus;
use App\Models\Quest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quest>
 */
class QuestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'experience_points' => fake()->numberBetween(10, 200),
            'created_by' => User::factory()->admin(),
            'status' => QuestStatus::Available,
            'taken_by' => null,
        ];
    }

    public function inProgress(?User $taker = null): static
    {
        return $this->state(fn (): array => [
            'status' => QuestStatus::InProgress,
            'taken_by' => $taker?->id ?? User::factory(),
        ]);
    }

    public function submitted(?User $taker = null): static
    {
        return $this->state(fn (): array => [
            'status' => QuestStatus::Submitted,
            'taken_by' => $taker?->id ?? User::factory(),
        ]);
    }

    public function completed(?User $taker = null): static
    {
        return $this->state(fn (): array => [
            'status' => QuestStatus::Completed,
            'taken_by' => $taker?->id ?? User::factory(),
        ]);
    }

    public function rejected(?User $taker = null): static
    {
        return $this->state(fn (): array => [
            'status' => QuestStatus::Rejected,
            'taken_by' => $taker?->id ?? User::factory(),
        ]);
    }
}
