<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;
use Illuminate\Http\Request;

class NotificacaoController extends Controller
{
    public function index(Request $request)
    {
        $notificacoes = Notificacao::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notificacoes);
    }

    public function marcarLida(Request $request, $id)
    {
        $notificacao = Notificacao::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $notificacao->update(['lida' => true]);

        return response()->json([
            'message' => 'Notificação marcada como lida.',
        ]);
    }

    public function marcarTodasLidas(Request $request)
    {
        Notificacao::where('user_id', $request->user()->id)
            ->where('lida', false)
            ->update(['lida' => true]);

        return response()->json([
            'message' => 'Todas as notificações foram marcadas como lidas.',
        ]);
    }

    public function naoLidas(Request $request)
    {
        $total = Notificacao::where('user_id', $request->user()->id)
            ->where('lida', false)
            ->count();

        return response()->json(['total' => $total]);
    }
}