<?php

namespace Database\Factories;

use App\Models\ScheduledMerge;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduledMergeFactory extends Factory
{
    protected $model = ScheduledMerge::class;

    public function definition(): array
    {
        $owner = fake()->userName();
        $repo = fake()->slug(2);
        $pullNumber = fake()->numberBetween(1, 999);

        return [
            'user_id' => User::factory(),
            'github_pr_url' => "https://github.com/{$owner}/{$repo}/pull/{$pullNumber}",
            'owner' => $owner,
            'repo' => $repo,
            'pull_number' => $pullNumber,
            'merge_method' => fake()->randomElement(['merge', 'squash', 'rebase']),
            'scheduled_at' => fake()->dateTimeBetween('now', '+1 week'),
            'status' => 'pending',
            'error_message' => null,
            'merged_at' => null,
        ];
    }
}
