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
        Schema::create('rdv', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->string('lastname', 255);
            $table->string('firstname', 255);
            $table->string('number', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->text('motif');
            $table->text('comment')->nullable();
            $table->timestamp('daterdv_at')->nullable();
            $table->tinyinteger('status')->default('0');
            $table->timestamps();
            $table->integer('user_id')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rdv');
    }
};
