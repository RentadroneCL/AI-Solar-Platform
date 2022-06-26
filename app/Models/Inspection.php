<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\{HasManyAnnotations, CustomProperties};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inspection extends Model implements HasMedia
{
    use HasFactory,
        InteractsWithMedia,
        CustomProperties;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inspections_information';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site_id',
        'name',
        'commissioning_date',
        'custom_properties',
        'custom_properties->data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'custom_properties' => 'array',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'commissioning_date',
    ];

    /**
     * Site related model.
     *
     * @return BelongsTo
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }
}
