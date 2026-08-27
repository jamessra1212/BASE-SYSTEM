<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->string('name');
            $table->string('icon')->nullable(); // e.g. bootstrap-icons class, "bi bi-speedometer2"
            $table->string('route')->nullable();  // named route, e.g. "admin.users.index"
            $table->string('url')->nullable();    // fallback raw url if no named route
            $table->string('permission_name')->unique(); // e.g. "menu.dashboard"
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['parent_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
