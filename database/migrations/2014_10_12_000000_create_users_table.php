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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('role')->default('client');
            $table->string('phone')->nullable();
            $table->string('call_id');
            $table->string('sex')->nullable();
            $table->string('country')->nullable();
            $table->string('voice_hidden')->default(true);
            $table->integer('balance')->default(0);
            $table->boolean('status')->default(false);
            $table->dateTime('last_connexion')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
