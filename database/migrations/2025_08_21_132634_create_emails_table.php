<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['sent', 'received']); // enviado o recibido
            $table->string('subject'); // asunto
            $table->string('sender_recipient'); // remitente (si es recibido) o destinatario (si es enviado)
            $table->text('content'); // contenido del correo
            $table->json('attachments')->nullable(); // archivos adjuntos (nombres de archivos)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
