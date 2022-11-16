<?php

namespace App\Models\Concerns;

use App\Models\Annotation;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAnnotations
{
    /**
     * Annotations related models.
     *
     * @return MorphMany
     */
    public function annotations(): MorphMany
    {
        return $this->morphMany(Annotation::class, 'annotable');
    }

    /**
     * Get the model annotation.
     *
     * @return MorphOne
     */
    public function annotation(): MorphOne
    {
        return $this->morphOne(Annotation::class, 'annotable');
    }

    /**
     * Get the model's most recent annotation.
     *
     * @return MorphOne
     */
    public function latestAnnotation(): MorphOne
    {
        return $this->morphOne(Annotation::class, 'annotable')->latestOfMany();
    }

    /**
     * Get the model's oldest annotation.
     *
     * @return MorphOne
     */
    public function oldestAnnotation(): MorphOne
    {
        return $this->morphOne(Annotation::class, 'annotable')->oldestOfMany();
    }
}
