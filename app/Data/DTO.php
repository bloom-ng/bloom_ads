<?php

namespace App\Data;

abstract class DTO
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