<?php

namespace Database\Factories;

use App\Enums\ClanMembershipRoleEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Clan>
 */
class ClanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array{
     *     owner_user_id: \Illuminate\Database\Eloquent\Factories\Factory,
     *     alias: string,
     *     name: string,
     *     slug: string,
     *     description: string,
     *     discord_url: null,
     *     logo: null,
     *     image: null
     * }
     */
    public function definition(): array
    {
        return [
            'owner_user_id' => User::factory(),
            'alias' => Str::lower(Str::random(8)),
            'name' => Str::limit($this->faker->company(), 32, ''),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->text(200),
            'discord_url' => null,
            'logo' => null,
            'image' => null,
        ];
    }

    public function withOwner(User $owner): static
    {
        return $this->state(fn () => [
            'owner_user_id' => $owner->id,
        ])->afterCreating(function ($clan) use ($owner): void {
            $clan->members()->syncWithoutDetaching([
                $owner->id => ['membership_role' => ClanMembershipRoleEnum::Owner->value],
            ]);
        });
    }

    public function withHelper(User $helper): static
    {
        return $this->afterCreating(function ($clan) use ($helper): void {
            $clan->members()->syncWithoutDetaching([
                $helper->id => ['membership_role' => ClanMembershipRoleEnum::Helper->value],
            ]);
        });
    }
}
