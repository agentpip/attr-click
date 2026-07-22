<?php

namespace Database\Factories;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Invitation> */
class InvitationFactory extends Factory
{
    protected $model = Invitation::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->bothify('ATTR-####-????')),
            'max_uses' => null,
            'expires_at' => now()->addWeek(),
        ];
    }
}
