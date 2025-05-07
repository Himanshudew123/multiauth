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
            $table->id(); // Primary key
            $table->uuid('uuid')->unique(); // UUID column
            $table->string('name', 100); // Product name
            $table->unsignedBigInteger('category_id'); // Foreign key to categories table (assuming unsigned)
            $table->decimal('price', 10, 2); // Price (adjust precision/scale as needed)
            $table->json('photo')->nullable(); // Storing multiple photos in JSON format (nullable)
            $table->timestamps(); // created_at and updated_at
            $table->boolean('is_deleted')->default(0); // 0 = not deleted, 1 = deleted
            // Add foreign key constraint for category_id (if you have a categories table)
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
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
