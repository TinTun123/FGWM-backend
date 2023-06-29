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
        Schema::create('protests', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->date('date');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('total_msg');
            $table->unsignedInteger('total_view');
            $table->string('imgURL');
            $table->text('bodyText');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('protests');
    }
};
