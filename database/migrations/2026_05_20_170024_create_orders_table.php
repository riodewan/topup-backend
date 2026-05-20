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
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->string('target_id')->comment('Nomor akun / ID game player');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->comment('Harga per item saat order');
            $table->decimal('total', 10, 2)->comment('Total = price * quantity');
            $table->enum('status', ['pending', 'processing', 'success', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->string('payment_token')->nullable()->comment('Midtrans snap token');
            $table->string('payment_url')->nullable()->comment('Midtrans redirect URL');
            $table->string('provider_ref')->nullable()->comment('Referensi transaksi dari Digiflazz');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
