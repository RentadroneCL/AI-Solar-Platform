<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Site extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sites_information';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'commissioning_date',
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
     * The user related model.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The inspections related to the site.
     *
     * @return HasMany
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class, 'site_id');
    }

    /**
     * Equipment types related to the site.
     *
     * @return HasMany
     */
    public function equipmentTypes(): HasMany
    {
        return $this->hasMany(EquipmentType::class, 'site_id');
    }

    /**
     * The equipments related to the site.
     *
     * @return HasMany
     */
    public function equipments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Equipment::class,
            EquipmentType::class,
            'site_id',
            'equipment_type_id'
        );
    }

    /**
     * Check authenticated user is the owner of the site.
     *
     * @return boolean
     */
    public function isOwner(): bool
    {
        return Auth::id() === $this->user_id;
    }
}
