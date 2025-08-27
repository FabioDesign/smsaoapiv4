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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->string('lastname', 255);
            $table->string('firstname', 255);
            $table->string('gender', 1);
            $table->string('number', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->timestamp('password_at')->nullable();
            $table->date('birthday_at');
            $table->string('birthplace', 255);
            $table->string('profession', 255);
            $table->string('village', 255);
            $table->string('street_number', 255);
            $table->string('hourse_number', 255);
            $table->string('family_number', 255);
            $table->string('register_number', 255)->nullable();
            $table->string('bp', 255)->nullable();
            $table->string('diplome', 255)->nullable();
            $table->text('distinction')->nullable();
            $table->string('fullname_peson', 255);
            $table->string('number_person', 255);
            $table->string('residence_person', 255);
            $table->string('photo', 255);
            $table->string('otp', 10)->nullable();
            $table->timestamp('otp_at')->nullable();
            $table->text('comment')->nullable();
            $table->string('lg', 2);
            $table->timestamp('login_at')->nullable();
            $table->tinyinteger('status')->default('0');
            $table->timestamps();
            $table->integer('user_id')->default('0');
            $table->integer('profile_id')->default('0');
            $table->integer('maritalstatus_id');
            $table->integer('cellule_id');
            $table->integer('district_id');
            $table->integer('nationality_id')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
