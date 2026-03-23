<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_adresses', function (Blueprint $table) {
            $table->timestamps(); // ajoute created_at et updated_at
        });
    }

    public function down(): void
    {
        Schema::table('delivery_adresses', function (Blueprint $table) {
            $table->dropTimestamps(); // supprime created_at et updated_at si rollback
        });
    }
};
