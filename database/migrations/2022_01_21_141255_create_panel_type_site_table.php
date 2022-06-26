<?php

use App\Models\Site;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePanelTypeSiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('panel_type_site', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('panel_type_id');
            $table->foreign('panel_type_id')->references('id')->on('panel_type')->cascadeOnDelete();
            $table->foreignIdFor(Site::class, 'site_id')->constrained('sites_information', 'id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_panel_type');
    }
}
