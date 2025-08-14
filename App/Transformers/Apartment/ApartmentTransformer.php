<?php

namespace App\Transformers\Apartment;

class ApartmentTransformer
{
    public function transform($apartment)
    {
        if (!$apartment) {
            return null;
        }

        if (is_array($apartment)) {
            $apartment = (object) $apartment;
        }

        return [
            'id' => $apartment->uuid,
            'name' => $apartment->name,
            'description' => $apartment->description,
            'category' => $apartment->category,
            'situation' => $apartment->situation,
            'active' => $apartment->active,
            'created_at' => $apartment->created_at,
            'updated_at' => $apartment->updated_at,
        ];
    }

    public function transformCollection(array $apartments)
    {
        return array_map(fn($apartment) => $this->transform($apartment), $apartments);
    }
}
