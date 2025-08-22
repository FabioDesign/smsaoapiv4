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
        Schema::create('spouses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->tinyinteger('rank');
            $table->string('filename', 255);
            $table->date('wedding_at')->nullable();
            $table->tinyinteger('status')->default('1');
            $table->timestamps();
            $table->integer('user_id');
            $table->integer('spouse_id');
            $table->integer('requestdoc_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spouses');
    }
};
