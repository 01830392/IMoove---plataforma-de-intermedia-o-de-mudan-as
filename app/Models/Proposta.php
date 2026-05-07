<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposta extends Model
{
    use HasFactory;

    protected $fillable = [
        'solicitacao_id',
        'prestador_id',
        'valor',
        'data_disponivel',
        'observacoes',
        'status',
    ];

    public function solicitacao()
    {
        return $this->belongsTo(Solicitacao::class);
    }

    public function prestador()
    {
        return $this->belongsTo(User::class, 'prestador_id');
    }
}
