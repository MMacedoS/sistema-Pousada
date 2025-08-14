<?php

namespace App\Transformers\Settings;

use App\Models\Settings\Configuracao;

class SettingTransformer
{
    public function transform(Configuracao $setting)
    {
        return [
            'code' => $setting->id ?? null,
            'company_name' => $setting->company_name ?? null,
            'company_email' => $setting->email ?? null,
            'company_phone' => $setting->phone ?? null,
            'company_address' => $setting->address ?? null,
            'company_cnpj' => $setting->cnpj ?? null,
            'company_checkin' => $setting->checkin ?? null,
            'company_checkout' => $setting->checkout ?? null,
            'company_service_fee' => $setting->percentage_service_fee ?? null,
            'company_cleaning_rate' => $setting->cleaning_rate ?? null,
            'company_values_on_dashboard' => $setting->display_values_on_dashboard ?? null,
            'company_allow_booking_online' => $setting->allow_booking_online ?? null,
            'company_currency' => $setting->currency ?? null,
            'company_advance_booking_days' => $setting->advance_booking_days ?? null,
            'company_cancellation_policies' => $setting->cancellation_policies ?? null,
            'company_time_zone' => $setting->time_zone ?? null,
            'created_at' => $setting->created_at ?? null,
            'updated_at' => $setting->updated_at ?? null,
        ];
    }
}
