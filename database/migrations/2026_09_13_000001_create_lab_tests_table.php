<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Labor-Katalog — team-lokal, frei pflegbar. Laboranforderung/-analyt (LOINC). Referenziert
 * von erbrachten Leistungen (encounter_services, catalog_type='lab_test'). Ergebnisse laufen
 * perspektivisch als Observations (Core). Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lab_tests')) {
            return;
        }

        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('catalog_code', 64)->nullable();   // kanonischer Schlüssel (Sync-Grundlage)
            $table->string('loinc_code', 32)->nullable();      // LOINC-Code des Analyten
            $table->string('name');
            $table->string('material')->nullable();            // Probenmaterial (Serum, Urin, …)
            $table->text('description')->nullable();
            $table->string('status', 16)->default('active');   // active|archived
            $table->unsignedInteger('position')->default(0);
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_tests');
    }
};
