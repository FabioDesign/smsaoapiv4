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
        Schema::create('demands', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->string('code', 50);
            $table->timestamp('daterdv_at');
            $table->tinyinteger('status')->default('0');
            $table->integer('controller_id')->default('0');
            $table->timestamp('controller_at')->nullable();
            $table->integer('validator_id')->default('0');
            $table->timestamp('validator_at')->nullable();
            $table->integer('user_id');
            $table->integer('document_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demands');
    }
};
