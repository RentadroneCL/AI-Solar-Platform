<?php

namespace App\Traits;

trait CustomProperties
{
    /**
     * Eliminate null values from the array.
     *
     * @param array $values
     * @return void
     */
    public function setCustomPropertiesAttribute(array $values = []): void
    {
        $properties = [];

        // Ensure that does not store null values.
        foreach ($values as $value) {
            if (!is_null($value['key']) && !is_null($value['value'])) {
                $properties[] = $value;
            }
        }

        $this->attributes['custom_properties'] = json_encode($properties);
    }
}
