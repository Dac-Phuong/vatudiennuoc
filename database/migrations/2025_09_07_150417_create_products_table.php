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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique()->nullable(); // Mã hàng
            $table->text('content')->nullable();
            $table->string('short_desc', 500)->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->integer('discount')->default(0);
            $table->integer('quantity')->default(0);
            $table->boolean('status')->default(true);
            $table->boolean('type')->default(1); //phân loại sản phẩm, 1:sản phẩm mới, 2: nổi bật, 3: bán chạy, 4: giảm giá
            $table->string('thumbnail')->nullable();
            $table->unsignedInteger('category_id')->default(0);
            $table->unsignedInteger('brand_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
