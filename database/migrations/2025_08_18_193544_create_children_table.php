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
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->tinyinteger('rank');
            $table->string('filename', 255);
            $table->tinyinteger('status')->default('1');
            $table->timestamps();
            $table->integer('user_id');
            $table->integer('children_id');
            $table->integer('requestdoc_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
