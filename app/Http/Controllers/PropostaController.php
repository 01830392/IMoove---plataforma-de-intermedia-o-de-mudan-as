<?php

namespace App\Http\Controllers;

use App\Models\Proposta;
use App\Models\Solicitacao;
use App\Models\OrdemServico;
use App\Helpers\NotificacaoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropostaController extends Controller
{
    public function store(Request $request, $solicitacao_id)
    {
        $request->validate([
            'valor'           => 'required|numeric|min:0',
            'data_disponivel' => 'required|date|after:today',
            'observacoes'     => 'nullable|string',
        ]);

        $solicitacao = Solicitacao::findOrFail($solicitacao_id);

        if (!in_array($solicitacao->status, ['solicitado', 'em_analise', 'proposto'])) {
            return response()->json([
                'message' => 'Esta solicitação não aceita mais propostas.',
            ], 422);
        }

        $proposta = Proposta::create([
            'solicitacao_id'  => $solicitacao_id,
            'prestador_id'    => $request->user()->id,
            'valor'           => $request->valor,
            'data_disponivel' => $request->data_disponivel,
            'observacoes'     => $request->observacoes,
        ]);

        $solicitacao->update(['status' => 'proposto']);

        NotificacaoHelper::enviar(
            $solicitacao->user_id,
            'Nova proposta recebida!',
            "Você recebeu uma proposta de R$ {$request->valor} para sua mudança de {$solicitacao->origem} para {$solicitacao->destino}.",
            'proposta_recebida',
            $proposta
        );

        return response()->json([
            'message'  => 'Proposta enviada com sucesso!',
            'proposta' => $proposta,
        ], 201);
    }

    public function index($solicitacao_id)
    {
        $propostas = Proposta::where('solicitacao_id', $solicitacao_id)
            ->with('prestador:id,name,email')
            ->get();

        return response()->json($propostas);
    }

    public function aceitar(Request $request, $id)
    {
        $proposta = Proposta::findOrFail($id);
        $solicitacao = $proposta->solicitacao;

        if ($solicitacao->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Você não tem permissão para aceitar esta proposta.',
            ], 403);
        }

        if ($solicitacao->status === 'aceito') {
            return response()->json([
                'message' => 'Esta solicitação já foi contratada.',
            ], 422);
        }

        DB::transaction(function () use ($proposta, $solicitacao) {
            $proposta->update(['status' => 'aceita']);

            Proposta::where('solicitacao_id', $solicitacao->id)
                ->where('id', '!=', $proposta->id)
                ->update(['status' => 'recusada']);

            $solicitacao->update(['status' => 'aceito']);

            OrdemServico::create([
                'solicitacao_id' => $solicitacao->id,
                'proposta_id'    => $proposta->id,
                'cliente_id'     => $solicitacao->user_id,
                'prestador_id'   => $proposta->prestador_id,
                'valor'          => $proposta->valor,
                'data_combinada' => $proposta->data_disponivel,
            ]);
        });

        NotificacaoHelper::enviar(
            $proposta->prestador_id,
            'Proposta aceita!',
            "Sua proposta de R$ {$proposta->valor} foi aceita! Prepare-se para a mudança de {$solicitacao->origem} para {$solicitacao->destino}.",
            'proposta_aceita',
            $proposta
        );

        return response()->json([
            'message'  => 'Proposta aceita e ordem de serviço gerada!',
            'proposta' => $proposta,
        ]);
    }
}