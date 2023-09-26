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
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caller_id')->constrained('users');
            $table->foreignId('called_id')->nullable()->constrained('users');
            $table->time('duration')->default('00:00:00');
            $table->json('languages')->nullable();
            $table->string('country')->nullable();
            $table->string('sex')->nullable();
            $table->bigInteger('benefit')->default(0);
            $table->boolean('voice_hidden')->default(false);
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
