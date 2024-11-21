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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('house_id')->constrained('houses')->cascadeOnDelete();
            $table->boolean('nanage_priv_priv')->default(false);
            $table->boolean('manage_house_priv')->default(false);
            $table->boolean('manage_member_priv')->default(false);
            $table->boolean('manage_task_priv')->default(false);
            $table->boolean('manage_even_priv')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
