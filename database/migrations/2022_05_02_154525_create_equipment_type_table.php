<?php

use App\Models\Site;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_type', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Site::class, 'site_id')->constrained('sites_information', 'id')->cascadeOnDelete();
            $table->string('name');
            $table->integer('quantity');
            $table->json('custom_properties')->nullable();
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
        Schema::dropIfExists('equipment_type');
    }
}
