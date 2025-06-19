<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_signatures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('document_id');
            $table->uuid('user_id');
            $table->string('signature_hash', 512);
            $table->timestamp('signature_timestamp')->default(now());
            $table->enum('status', ['pending', 'signed', 'rejected'])->default('pending');
            $table->longText('signature_data')->nullable();
            $table->text('certificate_info')->nullable();
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
            
            $table->index(['document_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_signatures');
    }
};