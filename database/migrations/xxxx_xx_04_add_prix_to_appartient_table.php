<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('appartient', function (Blueprint $table) {
            if (!Schema::hasColumn('appartient', 'prix')) {
                $table->decimal('prix', 10, 2)->nullable()->after('quantite');
            }
        });
    }
    public function down(): void {
        Schema::table('appartient', function (Blueprint $table) {
            if (Schema::hasColumn('appartient', 'prix')) {
                $table->dropColumn('prix');
            }
        });
    }
};
