<?php
// database/migrations/2024_01_01_000002_create_conversations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Conversaciones
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Para grupos
            $table->boolean('is_group')->default(false);
            $table->string('avatar')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Tabla pivote usuarios-conversaciones
        Schema::create('conversation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            $table->unique(['conversation_id', 'user_id']);
        });

        // Mensajes (contenido cifrado con AES-256)
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('content'); // Cifrado con Laravel Crypt (AES-256-CBC)
            $table->enum('type', ['text', 'image', 'file'])->default('text');
            $table->string('file_path')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversation_user');
        Schema::dropIfExists('conversations');
    }
};
