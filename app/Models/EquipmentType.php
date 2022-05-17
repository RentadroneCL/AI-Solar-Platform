<?php

namespace App\Models;

use App\Traits\CustomProperties;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentType extends Model
{
    use HasFactory, CustomProperties;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'equipment_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'custom_properties',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'custom_properties' => 'array',
    ];
}
