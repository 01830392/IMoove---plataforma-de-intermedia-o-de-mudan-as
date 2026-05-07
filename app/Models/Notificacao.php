<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    use HasFactory;

    protected $table = 'notificacoes';

    protected $fillable = [
        'user_id',
        'titulo',
        'mensagem',
        'tipo',
        'referencia_id',
        'referencia_type',
        'lida',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referencia()
    {
        return $this->morphTo();
    }
}