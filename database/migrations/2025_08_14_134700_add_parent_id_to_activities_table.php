<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddParentIdToActivitiesTable extends Migration
{
    /* public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('activities')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    } */
}