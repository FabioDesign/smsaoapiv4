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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->string('code', 5)->unique();
            $table->string('en', 255);
            $table->string('fr', 255);
            $table->string('amount', 50)->nullable();
            $table->integer('number')->default('0');
            $table->text('description_en');
            $table->text('description_fr');
            $table->tinyinteger('status');
            $table->timestamps();
            $table->integer('period_id');
            $table->integer('created_user');
            $table->integer('updated_user')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
