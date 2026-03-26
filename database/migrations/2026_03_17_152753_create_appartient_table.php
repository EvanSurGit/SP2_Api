<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appartient', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_Panier');
            $table->unsignedBigInteger('id_Puzzle');
            $table->integer('quantite')->default(1);
            $table->decimal('prix', 8, 2);
            $table->timestamps();

            // Cl�s �trang�res si tes tables existent d�j�
            $table->foreign('id_Panier')->references('id')->on('paniers')->onDelete('cascade');
            $table->foreign('id_Puzzle')->references('id')->on('puzzles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appartient');
    }
};
