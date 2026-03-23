<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('adresse', function (Blueprint $table) {
            $table->id();
            $table->string('numero');
            $table->string('rue');
            $table->string('ville');
            $table->string('cp');
            $table->string('pays');
            $table->foreignId('id_utilisateur')
                  ->constrained('users')   // change en 'utilisateur' si ta table users a un autre nom
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('adresse');
    }
};
