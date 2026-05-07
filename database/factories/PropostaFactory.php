<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PropostaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'valor'           => $this->faker->randomFloat(2, 300, 3000),
            'data_disponivel' => now()->addDays(rand(3, 20))->format('Y-m-d'),
            'observacoes'     => $this->faker->sentence(),
            'status'          => 'pendente',
        ];
    }
}