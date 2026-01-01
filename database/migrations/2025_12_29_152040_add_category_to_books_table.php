<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan perubahan (Tambah kolom)
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Menambahkan kolom category setelah kolom author
            $table->string('category')->after('author')->nullable(); 
        });
    }

    /**
     * Batalkan perubahan (Hapus kolom jika rollback)
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};