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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('code')->unique();
            $table->bigInteger('price')->default(0);
            $table->integer('validity');
            $table->foreignId('type_package_id');
            $table->time('duration');
            $table->boolean('sex')->default(false);
            $table->boolean('voice_hidden')->default(false);
            $table->boolean('language')->default(false);
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
