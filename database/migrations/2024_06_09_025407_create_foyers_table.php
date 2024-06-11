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
        Schema::create('foyers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('admin_id')->nullable();
            $table->String('image')->nullable();
            $table->timestamps();
        });

        // un utilisateur ne peut être relier qu'à un seul foyer
        Schema::table('users', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Foyer::class)->nullable()->constrained()->cascadeOnDelete();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foyers');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Foyer::class);
        });

    }
};
