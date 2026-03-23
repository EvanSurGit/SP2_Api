<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('paniers', function (Blueprint $table) {
            if (!Schema::hasColumn('paniers', 'adresse_livraison_id')) {
                $table->unsignedBigInteger('adresse_livraison_id')->nullable()->after('id_utilisateur');
            }
        });
    }

    public function down(): void {
        Schema::table('paniers', function (Blueprint $table) {
            if (Schema::hasColumn('paniers', 'adresse_livraison_id')) {
                $table->dropColumn('adresse_livraison_id');
            }
        });
    }
};
