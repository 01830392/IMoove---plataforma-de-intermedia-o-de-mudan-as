<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SolicitacaoController;
use App\Http\Controllers\PropostaController;
use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\OrdemServicoController;
use App\Http\Controllers\NotificacaoController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rotas do Cliente
    Route::middleware('perfil:cliente')->group(function () {
        Route::post('/solicitacoes',                [SolicitacaoController::class, 'store']);
        Route::patch('/solicitacoes/{id}/cancelar', [SolicitacaoController::class, 'cancel']);
        Route::post('/propostas/{id}/aceitar',      [PropostaController::class, 'aceitar']);
        Route::post('/avaliacoes',                  [AvaliacaoController::class, 'store']);
    });

    // Rotas do Prestador
    Route::middleware('perfil:prestador')->group(function () {
        Route::post('/solicitacoes/{id}/propostas', [PropostaController::class, 'store']);
    });

    // Rotas compartilhadas
    Route::middleware('perfil:cliente,prestador,admin')->group(function () {
        Route::get('/solicitacoes',                 [SolicitacaoController::class, 'index']);
        Route::get('/solicitacoes/{id}',            [SolicitacaoController::class, 'show']);
        Route::get('/solicitacoes/{id}/propostas',  [PropostaController::class, 'index']);
        Route::get('/ordens-servico',               [OrdemServicoController::class, 'index']);
        Route::get('/ordens-servico/{id}',          [OrdemServicoController::class, 'show']);
        Route::patch('/ordens-servico/{id}/status', [OrdemServicoController::class, 'updateStatus']);
        Route::get('/avaliacoes/historico',         [AvaliacaoController::class, 'historico']);
        Route::get('/historico',                    [SolicitacaoController::class, 'historico']);

        // Notificações
        Route::get('/notificacoes',                       [NotificacaoController::class, 'index']);
        Route::get('/notificacoes/nao-lidas',             [NotificacaoController::class, 'naoLidas']);
        Route::patch('/notificacoes/{id}/lida',           [NotificacaoController::class, 'marcarLida']);
        Route::patch('/notificacoes/marcar-todas-lidas',  [NotificacaoController::class, 'marcarTodasLidas']);
    });
});