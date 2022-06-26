<?php

namespace App\Traits;

use Illuminate\Support\Arr;

trait CustomProperties
{
    /**
     * Set custom properties.
     *
     * @param array $customProperties
     * @return self
     */
    public function withCustomProperties(array $customProperties = []): self
    {
        $this->custom_properties = $customProperties;

        return $this;
    }

    /**
     * Check if the property exists.
     *
     * @param string $propertyName
     * @return boolean
     */
    public function hasCustomProperty(string $propertyName): bool
    {
        return Arr::has($this->custom_properties, $propertyName);
    }

    /**
     * Get the value of custom property with the given name.
     *
     * @param string $propertyName
     * @param mixed $default
     * @return mixed
     */
    public function getCustomProperty(string $propertyName, $default = null): mixed
    {
        return Arr::get($this->custom_properties, $propertyName, $default);
    }

    /**
     * Set the value of custom property with the given name.
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function setCustomProperty(string $name, $value): self
    {
        $customProperties = $this->custom_properties;

        Arr::set($customProperties, $name, $value);

        $this->custom_properties = $customProperties;

        return $this;
    }

    /**
     * Removes the value of custom property with the given name.
     *
     * @param string $name
     * @return self
     */
    public function forgetCustomProperty(string $name): self
    {
        $customProperties = $this->custom_properties;

        Arr::forget($customProperties, $name);

        $this->custom_properties = $customProperties;

        return $this;
    }
}
