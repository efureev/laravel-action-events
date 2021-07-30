<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Php\Support\Laravel\Database\Schema\Postgres\Blueprint;

class CreateTestStuffTable extends Migration
{
    private static string $table = 'stuffs';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(
            static::$table,
            static function (Blueprint $table) {
                $table->primaryUUID();
                $table->string('name');
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(static::$table);
    }
}
