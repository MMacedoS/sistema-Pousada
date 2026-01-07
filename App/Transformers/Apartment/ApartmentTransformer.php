<?php

namespace App\Transformers\Apartment;

use App\Models\Apartment\Apartamento;
use App\Repositories\Entities\Reservation\ReservaRepository;
use App\Transformers\Reservation\ReservaTransformer;

class ApartmentTransformer
{
    public function transform(Apartamento $apartment)
    {
        if (!$apartment) {
            return null;
        }

        if (is_array($apartment)) {
            $apartment = (object) $apartment;
        }

        return [
            'id' => $apartment->uuid ?? null,
            'name' => $apartment->name ?? null,
            'description' => $apartment->description ?? null,
            'category' => $apartment->category ?? null,
            'situation' => $apartment->situation ?? null,
            'active' => $apartment->active ?? null,
            'created_at' => $apartment->created_at ?? null,
            'updated_at' => $apartment->updated_at ?? null,
        ];
    }

    public function transformCollection(array $apartments)
    {
        return array_map(fn($apartment) => $this->transform($apartment), $apartments);
    }

    public function accommodationWithAllApartments(array $apartments)
    {
        return array_map(fn($apartment) => $this->transformWithReservation($apartment), $apartments);
    }

    public function transformWithReservation(Apartamento $apartment)
    {
        $transformed = $this->transform($apartment);
        $reservationRepository = ReservaRepository::getInstance();
        $reservation = $reservationRepository->findFirstByApartmentId((int)$apartment->id);
        if (!is_null($reservation)) {
            $reservationTransformer = new ReservaTransformer();
            $reservationTransformed = $reservationTransformer->transform($reservation);
        }
        return [
            ...$transformed,
            'reservation' =>  $reservationTransformed ?? null,
        ];
    }
}
