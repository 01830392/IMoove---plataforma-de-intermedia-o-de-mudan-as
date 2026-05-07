<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('origem');
            $table->string('destino');
            $table->date('data_desejada');
            $table->text('descricao')->nullable();
            $table->boolean('precisa_desmontagem')->default(false);
            $table->boolean('precisa_montagem')->default(false);
            $table->boolean('itens_frageis')->default(false);
            $table->enum('status', [
                'solicitado',
                'em_analise',
                'proposto',
                'aceito',
                'em_andamento',
                'concluido',
                'cancelado'
            ])->default('solicitado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacoes');
    }
};
