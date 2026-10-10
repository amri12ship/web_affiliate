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
        Schema::table('products', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->decimal('rating', 3, 2)->default(0)->after('price');
            $table->unsignedInteger('sold_count')->default(0)->after('rating');
            $table->boolean('is_trending')->default(false)->after('sold_count');
            $table->boolean('is_viral')->default(false)->after('is_trending');
            $table->boolean('is_recommended')->default(false)->after('is_viral');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'rating',
                'sold_count',
                'is_trending',
                'is_viral',
                'is_recommended',
            ]);
        });
    }
};
