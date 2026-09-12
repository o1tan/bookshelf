<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reading_plans', function (Blueprint $table) {
            $table->index('user_id');

            $table->dropUnique(
                'reading_plans_user_id_book_id_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('reading_plans', function (Blueprint $table) {
            $table->unique(['user_id', 'book_id']);
            $table->dropIndex('reading_plans_user_id_index');
        });
    }
};
