<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // "published" articles are public; a "draft" can only be seen by the person who wrote it
        Schema::table('articles', function (Blueprint $table) {
            $table->string('status', 12)->default('published')->after('image_alt')->index();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
