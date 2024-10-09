<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prompt>
 */
class PromptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $users = User::factory()->count(1)->create();
        return [
                'message'         => fake()->text(),
                'role'            => 'user',
                'user_id'         => $users[0]->id,
                'prompt_chain_id' => Str::uuid(),
            ];
    }
}
