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
        Schema::create('su_menus', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('menu_id');
            $table->string('category');
            $table->string('name');
            $table->string('route')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_menu')->default(false);
            $table->boolean('is_dropdown')->default(false);
            $table->integer('order')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('su_menus');
    }
};
