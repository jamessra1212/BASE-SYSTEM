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
        Schema::create('su_submenus', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('sub_menu_id');
            $table->string('x_menu_id');
            $table->string('name');
            $table->string('nav_name')->nullable();
            $table->string('route');
            $table->boolean('is_nav')->default(false);
            $table->integer('sort')->unsigned();
            $table->boolean('public')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('su_submenus');
    }
};
