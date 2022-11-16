<?php

namespace App\Models;

use App\Models\User;
use App\Traits\CustomProperties;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Annotation extends Model
{
    use HasFactory, CustomProperties;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'annotations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'content',
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

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'custom_properties->commissioning_at',
    ];

    /**
     * Annotations related models.
     *
     * @return MorphTo
     */
    public function annotable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * User related mode.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
