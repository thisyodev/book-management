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
        Schema::create('books', function (Blueprint $table) {
            $table->id(); // 🔥 Primary Key auto increment (bigint unsigned)

            $table->string('title');
            $table->string('author');
            $table->integer('published_year')->nullable();
            $table->string('genre')->nullable();

            $table->timestamps();

            $table->unique(['title', 'author']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
