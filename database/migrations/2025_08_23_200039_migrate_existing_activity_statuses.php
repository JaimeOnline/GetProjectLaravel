<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrar los estados existentes de activities a la tabla pivot
        $activities = DB::table('activities')->whereNotNull('status')->get();
        
        foreach ($activities as $activity) {
            // Buscar el status_id correspondiente
            $status = DB::table('statuses')->where('name', $activity->status)->first();
            
            if ($status) {
                // Insertar en la tabla pivot si no existe ya
                DB::table('activity_statuses')->insertOrIgnore([
                    'activity_id' => $activity->id,
                    'status_id' => $status->id,
                    'assigned_at' => $activity->created_at ?? now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Limpiar la tabla pivot
        DB::table('activity_statuses')->truncate();
    }
};
