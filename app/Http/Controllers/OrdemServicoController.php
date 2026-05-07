<?php

namespace App\Http\Controllers;

use App\Models\OrdemServico;
use App\Models\Proposta;
use Illuminate\Http\Request;

class OrdemServicoController extends Controller
{
    public function index(Request $request)
    {
        $user_id = $request->user()->id;

        $ordens = OrdemServico::where('cliente_id', $user_id)
            ->orWhere('prestador_id', $user_id)
            ->with([
                'solicitacao:id,origem,destino,data_desejada',
                'cliente:id,name,email',
                'prestador:id,name,email',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($ordens);
    }

    public function show(Request $request, $id)
    {
        $user_id = $request->user()->id;

        $ordem = OrdemServico::where('id', $id)
            ->where(function ($query) use ($user_id) {
                $query->where('cliente_id', $user_id)
                      ->orWhere('prestador_id', $user_id);
            })
            ->with([
                'solicitacao',
                'cliente:id,name,email',
                'prestador:id,name,email',
            ])
            ->firstOrFail();

        return response()->json($ordem);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:em_andamento,concluido,cancelado',
        ]);

        $user_id = $request->user()->id;

        $ordem = OrdemServico::where('id', $id)
            ->where(function ($query) use ($user_id) {
                $query->where('cliente_id', $user_id)
                      ->orWhere('prestador_id', $user_id);
            })
            ->firstOrFail();

        $ordem->update(['status' => $request->status]);

        if ($request->status === 'concluido') {
            $ordem->solicitacao->update(['status' => 'concluido']);
        }

        return response()->json([
            'message' => 'Status atualizado com sucesso!',
            'ordem'   => $ordem,
        ]);
    }
}
