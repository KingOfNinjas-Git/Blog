<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The authors, categories and articles tables used to be created by hand. They are guarded so
// that a database that already has them (the original local one) is left alone.
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('authors')) {
            return;
        }

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
