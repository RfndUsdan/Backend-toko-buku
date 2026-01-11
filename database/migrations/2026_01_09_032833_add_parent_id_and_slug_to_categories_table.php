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
        Schema::table('categories', function (Blueprint $table) {
            // Kolom untuk menghubungkan sub-kategori ke kategori induk
            if (!Schema::hasColumn('categories', 'parent_id')) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('categories')
                    ->onDelete('cascade');
            }

            // Kolom slug untuk navigasi di Next.js (contoh: 'fiksi-populer')
            if (!Schema::hasColumn('categories', 'slug')) {
                $table->string('slug')->unique()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'slug']);
        });
    }
};
