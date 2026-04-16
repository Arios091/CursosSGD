<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddNameFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('primer_nombre')->nullable()->after('id');
            $table->string('segundo_nombre')->nullable()->after('primer_nombre');
            $table->string('primer_apellido')->nullable()->after('segundo_nombre');
            $table->string('segundo_apellido')->nullable()->after('primer_apellido');
        });
        
        DB::table('users')->update([
            'primer_nombre' => DB::raw("split_part(name, ' ', 1)"),
            'segundo_nombre' => DB::raw("CASE WHEN array_length(string_to_array(name, ' '), 1) > 2 THEN split_part(name, ' ', 2) ELSE NULL END"),
            'primer_apellido' => DB::raw("CASE WHEN array_length(string_to_array(name, ' '), 1) > 2 THEN split_part(name, ' ', 3) ELSE split_part(name, ' ', 2) END"),
            'segundo_apellido' => DB::raw("CASE WHEN array_length(string_to_array(name, ' '), 1) > 3 THEN split_part(name, ' ', 4) ELSE NULL END"),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido']);
        });
    }
}
