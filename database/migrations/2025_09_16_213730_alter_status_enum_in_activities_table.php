<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterStatusEnumInActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modificar el ENUM para agregar los nuevos valores
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE activities MODIFY COLUMN `status` ENUM(
                'no_iniciada',
                'en_ejecucion',
                'culminada',
                'en_espera_de_insumos',
                'en_certificacion_por_cliente',
                'pases_enviados',
                'pausada',
                'cancelada',
                'reiterar',
                'atendiendo_hoy'
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_ejecucion'");
        } elseif (DB::getDriverName() === 'pgsql') {
            // En PostgreSQL, crea el tipo enum si no existe y cambia la columna
            DB::statement("DO $$
            BEGIN
                IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'activity_status_enum') THEN
                    CREATE TYPE activity_status_enum AS ENUM (
                        'no_iniciada',
                        'en_ejecucion',
                        'culminada',
                        'en_espera_de_insumos',
                        'en_certificacion_por_cliente',
                        'pases_enviados',
                        'pausada',
                        'cancelada',
                        'reiterar',
                        'atendiendo_hoy'
                    );
                END IF;
            END$$;");
            // Elimina el valor por defecto antes de cambiar el tipo
            DB::statement("ALTER TABLE activities ALTER COLUMN status DROP DEFAULT");
            // Cambia el tipo de la columna
            DB::statement("ALTER TABLE activities ALTER COLUMN status TYPE activity_status_enum USING status::text::activity_status_enum");
            // Vuelve a establecer el valor por defecto
            DB::statement("ALTER TABLE activities ALTER COLUMN status SET DEFAULT 'en_ejecucion'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE activities MODIFY COLUMN `status` ENUM(
                'no_iniciada',
                'en_ejecucion',
                'culminada',
                'en_espera_de_insumos',
                'en_certificacion_por_cliente',
                'pases_enviados',
                'pausada',
                'cancelada'
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_ejecucion'");
        } elseif (DB::getDriverName() === 'pgsql') {
            // Cambia la columna a texto temporalmente
            DB::statement("ALTER TABLE activities ALTER COLUMN status TYPE varchar(255)");
            // Elimina el tipo enum anterior si existe
            DB::statement("DROP TYPE IF EXISTS activity_status_enum");
            // (Opcional) Podrías recrear el tipo enum anterior si lo necesitas
        }
    }
}
