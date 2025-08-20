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
        Schema::create('requestdocs', function (Blueprint $table) {
            $table->id();
            $table->string('label_fr', 255);
            $table->string('label_en', 255);
            $table->tinyinteger('status')->default('1');
            $table->timestamps();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requestdocs');
    }
};
