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
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->date('Fecha_cita');
            $table->time('Hora');
            $table->string('Estado', ['Pendiente' , 'Confirmada' , 'Cancelada'])->default('Pendiente');
            $table->foreignId('idPaciente')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('idMedico')->constrained('medicos')->onDelete('cascade');
            $table->foreignId('idResepcionista')->constrained('resepcionistas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
