<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    use HasFactory;

    protected $table = 'avaliacoes';

    protected $fillable = [
        'solicitacao_id',
        'avaliador_id',
        'avaliado_id',
        'nota',
        'comentario',
    ];

    public function solicitacao()
    {
        return $this->belongsTo(Solicitacao::class);
    }

    public function avaliador()
    {
        return $this->belongsTo(User::class, 'avaliador_id');
    }

    public function avaliado()
    {
        return $this->belongsTo(User::class, 'avaliado_id');
    }
}
