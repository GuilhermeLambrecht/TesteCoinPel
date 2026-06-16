<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Cliente contratante (obrigatório). constrained() cria FK + índice.
            // Como o fluxo é migrate:fresh, a tabela está vazia ao adicionar a coluna.
            $table->foreignId('client_id')->after('package_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
        });
    }
};
