<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 500);
            $table->longText('content')->nullable();
            $table->string('document_type', 100)->default('acta');
            $table->enum('status', ['draft', 'review', 'approved', 'published', 'archived'])->default('draft');
            $table->string('audio_file_path', 500)->nullable();
            $table->longText('transcription_text')->nullable();
            $table->uuid('created_by');
            $table->uuid('reviewed_by')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->date('session_date')->nullable();
            $table->string('document_hash', 64)->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('reviewed_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
            
            $table->index(['status']);
            $table->index(['created_by']);
            $table->index(['session_date']);
            $table->index(['is_public']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};