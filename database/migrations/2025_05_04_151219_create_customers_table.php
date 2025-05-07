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
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->uuid('uuid')->unique();
            $table->string('name', 100);
            $table->string('email')->unique();
            $table->string('password', 255);
            $table->string('bio', 255);
            $table->unsignedTinyInteger('gender');
            $table->text('photo');
            $table->string('number', 15)->nullable();
            $table->boolean('is_deleted')->default(0); // 0 = not deleted, 1 = deleted
            $table->timestamps();              // created_at and updated_at
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
