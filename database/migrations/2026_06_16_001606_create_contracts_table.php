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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            // O pacote contratado é obrigatório; constrained() cria FK + índice.
            $table->foreignId('package_id')->constrained();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('value', 10, 2);
            $table->string('status')->default('rascunho');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
