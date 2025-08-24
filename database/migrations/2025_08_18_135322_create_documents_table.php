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
            $table->string('code', 2)->unique();
            $table->string('label_en', 255);
            $table->string('label_fr', 255);
            $table->string('amount', 50);
            $table->string('deadline', 255);
            $table->text('description_en');
            $table->text('description_fr');
            $table->tinyinteger('status');
            $table->timestamps();
            $table->integer('user_id');
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
