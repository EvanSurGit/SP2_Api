<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appartient', function (Blueprint $table) {
            $table->unsignedBigInteger('id_Puzzle');
            $table->unsignedBigInteger('id_Panier');
            $table->unsignedInteger('quantite')->default(1);

            // PK composite comme sur le MCD
            $table->primary(['id_Puzzle', 'id_Panier']);

            // FKs
            $table->foreign('id_Puzzle')->references('id')->on('puzzles')->restrictOnDelete();
            $table->foreign('id_Panier')->references('id')->on('paniers')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appartient');
    }
};
