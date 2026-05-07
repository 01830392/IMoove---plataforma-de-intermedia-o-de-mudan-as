<?php

namespace App\Http\Controllers;

use App\Models\Solicitacao;
use Illuminate\Http\Request;

class SolicitacaoController extends Controller
{
    public function index(Request $request)
    {
        $solicitacoes = Solicitacao::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($solicitacoes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'origem'              => 'required|string',
            'destino'             => 'required|string',
            'data_desejada'       => 'required|date|after:today',
            'descricao'           => 'nullable|string',
            'precisa_desmontagem' => 'boolean',
            'precisa_montagem'    => 'boolean',
            'itens_frageis'       => 'boolean',
        ]);

        $solicitacao = Solicitacao::create([
            'user_id'             => $request->user()->id,
            'origem'              => $request->origem,
            'destino'             => $request->destino,
            'data_desejada'       => $request->data_desejada,
            'descricao'           => $request->descricao,
            'precisa_desmontagem' => $request->precisa_desmontagem ?? false,
            'precisa_montagem'    => $request->precisa_montagem ?? false,
            'itens_frageis'       => $request->itens_frageis ?? false,
        ]);

        return response()->json([
            'message'     => 'Solicitação criada com sucesso!',
            'solicitacao' => $solicitacao,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $solicitacao = Solicitacao::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json($solicitacao);
    }

    public function cancel(Request $request, $id)
    {
        $solicitacao = Solicitacao::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $solicitacao->update(['status' => 'cancelado']);

        return response()->json([
            'message' => 'Solicitação cancelada com sucesso!',
        ]);
    }

    public function historico(Request $request)
    {
        $solicitacoes = Solicitacao::where('user_id', $request->user()->id)
            ->whereIn('status', ['concluido', 'cancelado'])
            ->with(['ordemServico'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($solicitacoes);
    }
}
