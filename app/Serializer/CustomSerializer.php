<?php

namespace App\Serializer;

use League\Fractal\Serializer\ArraySerializer;

class CustomSerializer extends ArraySerializer
{
    public function collection($resourceKey, array $data) :array
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }

        return $data;
    }

    public function item($resourceKey, array $data):array
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }
        return $data;
    }
}