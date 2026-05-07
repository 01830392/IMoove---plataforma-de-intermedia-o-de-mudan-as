<?php

namespace App\Http\Controllers;

use App\Models\Avaliacao;
use App\Models\Solicitacao;
use App\Models\Proposta;
use Illuminate\Http\Request;

class AvaliacaoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'solicitacao_id' => 'required|exists:solicitacoes,id',
            'nota'           => 'required|integer|min:1|max:5',
            'comentario'     => 'nullable|string',
        ]);

        $solicitacao = Solicitacao::findOrFail($request->solicitacao_id);

        if ($solicitacao->status !== 'aceito') {
            return response()->json([
                'message' => 'A avaliação só pode ser feita após o aceite do serviço.',
            ], 422);
        }

        $avaliador_id = $request->user()->id;

        $jaAvaliou = Avaliacao::where('solicitacao_id', $solicitacao->id)
            ->where('avaliador_id', $avaliador_id)
            ->exists();

        if ($jaAvaliou) {
            return response()->json([
                'message' => 'Você já avaliou este serviço.',
            ], 422);
        }

        $proposta = Proposta::where('solicitacao_id', $solicitacao->id)
            ->where('status', 'aceita')
            ->firstOrFail();

        if ($avaliador_id === $solicitacao->user_id) {
            $avaliado_id = $proposta->prestador_id;
        } else {
            $avaliado_id = $solicitacao->user_id;
        }

        $avaliacao = Avaliacao::create([
            'solicitacao_id' => $solicitacao->id,
            'avaliador_id'   => $avaliador_id,
            'avaliado_id'    => $avaliado_id,
            'nota'           => $request->nota,
            'comentario'     => $request->comentario,
        ]);

        return response()->json([
            'message'   => 'Avaliação registrada com sucesso!',
            'avaliacao' => $avaliacao,
        ], 201);
    }

    public function historico(Request $request)
    {
        $avaliacoes = Avaliacao::where('avaliado_id', $request->user()->id)
            ->with('avaliador:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($avaliacoes);
    }
}
