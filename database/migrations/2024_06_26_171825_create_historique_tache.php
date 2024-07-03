<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Tache;
use App\Models\Historique;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historique_tache', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Tache::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Historique::class)->constrained()->cascadeOnDelete();
            $table->primary(['historique_id', 'tache_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_tache');
    }
};
