<?php

namespace App\Helpers;

use App\Models\Notificacao;

class NotificacaoHelper
{
    public static function enviar(int $userId, string $titulo, string $mensagem, string $tipo, $referencia): void
    {
        Notificacao::create([
            'user_id'         => $userId,
            'titulo'          => $titulo,
            'mensagem'        => $mensagem,
            'tipo'            => $tipo,
            'referencia_id'   => $referencia->id,
            'referencia_type' => get_class($referencia),
        ]);
    }
}