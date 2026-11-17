<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('gdpr-breaches.table', 'gdpr_breaches'), function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamp('discovered_at');
            $table->timestamp('due_at')->index();
            $table->string('risk', 10)->nullable();
            $table->json('data_categories')->nullable();
            $table->unsignedInteger('affected_subjects')->nullable();
            $table->unsignedInteger('affected_records')->nullable();
            $table->text('consequences')->nullable();
            $table->text('measures')->nullable();
            $table->timestamp('authority_notified_at')->nullable();
            $table->string('authority_reference')->nullable();
            $table->text('delay_reason')->nullable();
            $table->boolean('further_information_owed')->default(false);
            $table->timestamp('further_information_at')->nullable();
            $table->timestamp('subjects_notified_at')->nullable();
            $table->text('no_subject_notice_reason')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('gdpr-breaches.table', 'gdpr_breaches'));
    }
};
