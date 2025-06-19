<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_revisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('document_id');
            $table->integer('revision_number');
            $table->longText('content')->nullable();
            $table->text('changes_summary')->nullable();
            $table->uuid('revised_by');
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('revised_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_revisions');
    }
};