<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SolicitacaoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'origem'              => $this->faker->streetAddress(),
            'destino'             => $this->faker->streetAddress(),
            'data_desejada'       => now()->addDays(rand(5, 30))->format('Y-m-d'),
            'descricao'           => $this->faker->sentence(),
            'precisa_desmontagem' => $this->faker->boolean(),
            'precisa_montagem'    => $this->faker->boolean(),
            'itens_frageis'       => $this->faker->boolean(),
            'status'              => 'solicitado',
        ];
    }
}