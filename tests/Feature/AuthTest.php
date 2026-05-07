<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_pode_se_cadastrar(): void
    {
        $response = $this->postJson('/api/register', [
            'name'                  => 'Teste IMoove',
            'email'                 => 'teste@imoove.com',
            'password'              => '123456',
            'password_confirmation' => '123456',
            'perfil'                => 'cliente',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'token',
                     'user' => ['id', 'name', 'email', 'perfil'],
                 ]);
    }

    public function test_usuario_pode_fazer_login(): void
    {
        $user = User::factory()->create([
            'email'    => 'login@imoove.com',
            'password' => bcrypt('123456'),
            'perfil'   => 'cliente',
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'login@imoove.com',
            'password' => '123456',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'token',
                     'user',
                 ]);
    }

    public function test_login_com_credenciais_invalidas(): void
    {
        $response = $this->postJson('/api/login', [
            'email'    => 'naoexiste@imoove.com',
            'password' => 'senhaerrada',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Credenciais inválidas.']);
    }

    public function test_usuario_pode_fazer_logout(): void
    {
        $user = User::factory()->create(['perfil' => 'cliente']);
        $token = $user->createToken('imoove')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Logout realizado com sucesso!']);
    }
}