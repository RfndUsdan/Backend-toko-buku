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
        Schema::table('books', function (Blueprint $table) {
            // Menambahkan kolom baru
            $table->string('publisher')->after('author');     // Penerbit
            $table->integer('published_year')->after('publisher'); // Tahun Terbit
            $table->string('language')->after('published_year');   // Bahasa
            $table->integer('pages')->after('language');           // Jumlah Halaman
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['publisher', 'published_year', 'language', 'pages',]);
        });
    }
};
