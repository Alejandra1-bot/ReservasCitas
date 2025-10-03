<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('apellido')->nullable();
        $table->string('documento')->nullable();
        $table->string('telefono')->nullable();
        $table->date('fechaNacimiento')->nullable();
        $table->string('genero')->nullable();
        $table->string('rh')->nullable();
        $table->string('nacionalidad')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
  public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'apellido',
            'documento',
            'telefono',
            'fechaNacimiento',
            'genero',
            'rh',
            'nacionalidad',
            
        ]);
    });
}
    
};
