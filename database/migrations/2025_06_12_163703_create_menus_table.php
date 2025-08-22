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
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 50)->unique();
            $table->string('label_fr', 50);
            $table->string('label_en', 50);
            $table->string('target')->nullable();
            $table->string('icone')->nullable();
            $table->tinyInteger('position');
            $table->tinyInteger('status');
            $table->timestamps();
            $table->bigInteger('menu_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
