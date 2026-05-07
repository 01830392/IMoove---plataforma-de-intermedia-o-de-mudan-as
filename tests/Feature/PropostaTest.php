<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Solicitacao;
use App\Models\Proposta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropostaTest extends TestCase
{
    use RefreshDatabase;

    private function criarCliente()
    {
        $user = User::factory()->create(['perfil' => 'cliente']);
        $token = $user->createToken('imoove')->plainTextToken;
        return [$user, $token];
    }

    private function criarPrestador()
    {
        $user = User::factory()->create(['perfil' => 'prestador']);
        $token = $user->createToken('imoove')->plainTextToken;
        return [$user, $token];
    }

    public function test_prestador_pode_enviar_proposta(): void
    {
        [$cliente] = $this->criarCliente();
        [, $tokenPrestador] = $this->criarPrestador();

        $solicitacao = Solicitacao::factory()->create(['user_id' => $cliente->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenPrestador)
            ->postJson("/api/solicitacoes/{$solicitacao->id}/propostas", [
                'valor'           => 750.00,
                'data_disponivel' => now()->addDays(5)->format('Y-m-d'),
                'observacoes'     => 'Equipe experiente disponível.',
            ]);

        $response->assertStatus(201)
         ->assertJsonStructure([
             'message',
             'proposta' => ['id', 'valor'],
         ]);
    }

    public function test_cliente_pode_aceitar_proposta(): void
    {
        [$cliente, $tokenCliente] = $this->criarCliente();
        [$prestador] = $this->criarPrestador();

        $solicitacao = Solicitacao::factory()->create(['user_id' => $cliente->id]);

        $proposta = Proposta::factory()->create([
            'solicitacao_id' => $solicitacao->id,
            'prestador_id'   => $prestador->id,
            'valor'          => 750.00,
            'data_disponivel'=> now()->addDays(5)->format('Y-m-d'),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenCliente)
            ->postJson("/api/propostas/{$proposta->id}/aceitar");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Proposta aceita e ordem de serviço gerada!']);
    }

    public function test_cliente_nao_pode_aceitar_proposta_de_outro_cliente(): void
    {
        [$cliente1] = $this->criarCliente();
        [$cliente2, $tokenCliente2] = $this->criarCliente();
        [$prestador] = $this->criarPrestador();

        $solicitacao = Solicitacao::factory()->create(['user_id' => $cliente1->id]);

        $proposta = Proposta::factory()->create([
            'solicitacao_id' => $solicitacao->id,
            'prestador_id'   => $prestador->id,
            'valor'          => 750.00,
            'data_disponivel'=> now()->addDays(5)->format('Y-m-d'),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenCliente2)
            ->postJson("/api/propostas/{$proposta->id}/aceitar");

        $response->assertStatus(403);
    }
}