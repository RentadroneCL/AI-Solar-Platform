<?php

use App\Models\Site;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPanelTypeAndPanelTypeSiteTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('panel_type_site');
        Schema::dropIfExists('panel_type');
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('panel_type', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('panel_type_site', function (Blueprint $table) {
            $table->id();
            $table->foreign('panel_type_id')->references('id')->on('panel_type')->cascadeOnDelete();
            $table->foreignIdFor(Site::class, 'site_id')->constrained('sites_information', 'id')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }
}
