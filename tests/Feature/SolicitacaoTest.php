<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Solicitacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolicitacaoTest extends TestCase
{
    use RefreshDatabase;

    private function criarCliente()
    {
        $user = User::factory()->create(['perfil' => 'cliente']);
        $token = $user->createToken('imoove')->plainTextToken;
        return [$user, $token];
    }

    public function test_cliente_pode_criar_solicitacao(): void
    {
        [$user, $token] = $this->criarCliente();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/solicitacoes', [
                'origem'        => 'Rua A, 123',
                'destino'       => 'Rua B, 456',
                'data_desejada' => now()->addDays(5)->format('Y-m-d'),
                'descricao'     => 'Mudança de apartamento',
            ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'solicitacao' => ['id', 'origem', 'destino'],
                 ]);
    }

    public function test_cliente_pode_listar_solicitacoes(): void
    {
        [$user, $token] = $this->criarCliente();

        Solicitacao::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/solicitacoes');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_cliente_pode_cancelar_solicitacao(): void
    {
        [$user, $token] = $this->criarCliente();

        $solicitacao = Solicitacao::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patchJson("/api/solicitacoes/{$solicitacao->id}/cancelar");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Solicitação cancelada com sucesso!']);
    }

    public function test_nao_autenticado_nao_pode_criar_solicitacao(): void
    {
        $response = $this->postJson('/api/solicitacoes', [
            'origem'        => 'Rua A, 123',
            'destino'       => 'Rua B, 456',
            'data_desejada' => now()->addDays(5)->format('Y-m-d'),
        ]);

        $response->assertStatus(401);
    }
}