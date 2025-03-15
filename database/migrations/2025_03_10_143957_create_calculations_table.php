<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('source_factory_id')->constrained('factories')->onDelete('cascade');
            $table->foreignId('destination_factory_id')->constrained('factories')->onDelete('cascade');
            $table->decimal('weight', 10, 2);
            $table->decimal('distance', 10, 2);
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('calculations');
    }
};