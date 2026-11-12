<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 32)->unique();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_group_option_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name', 150);
            $table->string('group_name', 150);
            $table->string('option_name', 150)->nullable();
            $table->string('variant_name', 150);
            $table->json('customer_data');
            $table->decimal('total', 15, 2);
            $table->string('payment_method', 100);
            $table->string('status', 32)->default('pending_payment')->index();
            $table->timestamp('completed_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
