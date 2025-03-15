<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('factories', function (Blueprint $table) {
            // Eğer bu sütunlar yoksa ekle
            if (!Schema::hasColumn('factories', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('factories', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('factories', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};