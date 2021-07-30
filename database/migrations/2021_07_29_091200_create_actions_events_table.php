<?php

use Fureev\ActionEvents\Entity\ActionEventStatus;
use Fureev\ActionEvents\Entity\ActionEventType;
use Fureev\ActionEvents\Helpers\ConfigHelpers;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Php\Support\Laravel\Database\Schema\Postgres\Blueprint;

class CreateActionsEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');

        $userColumnType = config('actionEvents.database.user_column_type', 'uuid');

        Schema::create(
            'action_events',
            static function (Blueprint $table) {
                $table->primaryUUID();
                $table->generateUUID('thread_id', false)->index();
                static::resolveUserColumn($table);
                $table->text('name');
                $table->string('status', 25)->default(ActionEventStatus::RUNNING);
                $table->enum('type', ActionEventType::TYPES)->nullable();
                $table->jsonb('result')->nullable(); // exception, result...

                static::resolveMorphColumn($table, 'model');

                $table->jsonb('original')->nullable();
                $table->jsonb('changes')->nullable();
                $table->jsonb('extra')->nullable();

                static::resolveMorphColumn($table, 'actionable');

                $table->timestamp('created_at');

                /*
                                $table->string('target_type');
                                $table->string('target_id');
                */

                $table->index(['thread_id', 'model_type', 'model_id']);
            }
        );
    }

    private static function resolveUserColumn(Blueprint $table): void
    {
        $userColumnType = config('actionEvents.database.user_column_type', 'uuid');

        ConfigHelpers::validateUserColumnType($userColumnType);

        $userColumnNullable = config('actionEvents.database.user_column_nullable', true);
        $table->addColumn($userColumnType, 'user_id')->nullable($userColumnNullable);
    }

    private static function resolveMorphColumn(Blueprint $table, string $name): void
    {
        $userColumnType = config('actionEvents.database.user_column_type', 'uuid');

        ConfigHelpers::validateUserColumnType($userColumnType);

        $table->string("{$name}_type")->nullable();

        if ($userColumnType === 'uuid') {
            $table->uuid("{$name}_id")->nullable();
        } else {
            $table->unsignedBigInteger("{$name}_id")->nullable();
        }

        $table->index(["{$name}_type", "{$name}_id"]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('action_events');
    }
}
