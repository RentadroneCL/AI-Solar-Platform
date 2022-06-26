<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPanelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('panels');
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('panels', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Site::class, 'site_id')->constrained('sites_information', 'id')->cascadeOnDelete();
            $table->string('panel_id');
            $table->string('panel_serial');
            $table->string('panel_zone')->nullable();
            $table->string('panel_sub_zone')->nullable();
            $table->string('panel_string')->nullable();
            $table->json('custom_properties')->nullable();
            $table->timestamps();
        });
    }
}
