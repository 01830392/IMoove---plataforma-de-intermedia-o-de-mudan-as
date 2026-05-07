<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitacao extends Model
{
    use HasFactory;

    protected $table = 'solicitacoes';

    protected $fillable = [
        'user_id',
        'origem',
        'destino',
        'data_desejada',
        'descricao',
        'precisa_desmontagem',
        'precisa_montagem',
        'itens_frageis',
        'status',
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ordemServico()
    {
        return $this->hasOne(\App\Models\OrdemServico::class);
    }
}
