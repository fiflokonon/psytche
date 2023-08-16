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
        Schema::create('user_forfaits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained("users");
            $table->foreignId('forfait_id')->constrained("forfaits");
            $table->date('date_by');
            $table->integer('nbr_minutes_rest');
            $table->boolean('statut')->default(0);
            $table->integer('payement_term');
            $table->integer('payement_statut');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_forfaits');
    }
};
