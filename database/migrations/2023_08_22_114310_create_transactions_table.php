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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_transaction_id')->nullable();
            $table->bigInteger('amount')->nullable();
            $table->string('phone')->nullable();
            $table->string('token')->nullable();
            $table->string('transaction_status')->default('initiated');
            $table->string('payment_status')->nullable();
            $table->json('custom_data')->nullable();
            $table->json('payment_details')->nullable();
            $table->foreignId('user_id');
            $table->foreignId('package_id')->nullable();
            $table->boolean('package_status')->default(0);
            $table->date('bought_at')->nullable();
            $table->time('remaining_time')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
