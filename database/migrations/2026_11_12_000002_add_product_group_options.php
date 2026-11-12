<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_groups', function (Blueprint $table) {
            $table->boolean('has_options')->default(false)->after('banner_description');
        });

        Schema::create('product_group_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_group_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['product_group_id', 'name']);
            $table->index(['product_group_id', 'sort_order']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreignId('product_group_option_id')->nullable()
                ->after('product_group_id')
                ->constrained('product_group_options')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_group_option_id');
        });
        Schema::dropIfExists('product_group_options');
        Schema::table('product_groups', function (Blueprint $table) {
            $table->dropColumn('has_options');
        });
    }
};
