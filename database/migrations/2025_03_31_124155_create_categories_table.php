<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('category_id'); // Auto-incrementing primary key
            $table->string('category_name')->unique(); // Category name must be unique
            $table->timestamps(); // Created_at & Updated_at timestamps
        });
    }

    public function down() {
        Schema::dropIfExists('categories');
    }
};
