<?php

namespace App\Services\RockAds\Data;

abstract class DataTransferObject
{
    public function __construct(array $parameters = [])
    {
        foreach ($parameters as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }
} 