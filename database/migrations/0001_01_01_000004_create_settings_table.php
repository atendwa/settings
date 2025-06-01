<?php

declare(strict_types=1);

use Atendwa\Support\Concerns\Support\InferMigrationDownMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    use InferMigrationDownMethod;

    public function up(): void
    {
        Schema::create('settings', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('group')->index();
            $blueprint->string('key')->index()->unique();
            $blueprint->string('name')->index();
            $blueprint->string('type')->default('string');
            $blueprint->text('value')->nullable();
            $blueprint->text('encrypted_value')->nullable();
            $blueprint->boolean('is_encrypted')->default(false);
            $blueprint->timestamps();
        });
    }
};
