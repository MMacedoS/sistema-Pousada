<?php

namespace App\Models\Settings;

class Configuracao {
    public $id;
    public ?string $company_name;
    public ?string $email; 
    public ?string $phone;
    public ?string $cnpj;
    public ?string $address;
    public ?string $checkin;
    public ?string $checkout;
    public ?string $percentage_service_fee;
    public ?string $cleaning_rate;
    public ?string $display_values_on_dashboard;
    public ?string $allow_booking_online;
    public ?string $currency;
    public ?string $advance_booking_days;
    public ?string $cancellation_policies;
    public ?string $time_zone;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create($data): Configuracao 
    {
        $setting = new Configuracao();
        $setting->id = $data['id'] ?? null;
        $setting->company_name = $data['name'];
        $setting->email = $data['email'];
        $setting->phone = $data['phone'];
        $setting->cnpj = $data['cnpj'];
        $setting->address = $data['address'];
        $setting->checkin = $data['checkin'];
        $setting->checkout = $data['checkout'];
        $setting->percentage_service_fee = $data['porcentage_service_fee'];
        $setting->cleaning_rate = $data['cleaning_rate'];
        $setting->display_values_on_dashboard = checkboxToInt($data['display_values_on_dashboard']) ?? null;
        $setting->allow_booking_online = checkboxToInt($data['allow_booking_online']);
        $setting->currency = $data['currency'];
        $setting->advance_booking_days = $data['advance_booking_days'];
        $setting->cancellation_policies = $data['cancellation_policies'];

        $setting->created_at = $data['created_at'] ?? null;
        $setting->updated_at = $data['updated_at'] ?? null;

        return $setting;
    }

    public function update($data, Configuracao $settings): Configuracao 
    {
        $settings->company_name = $data['name'] ?? $settings->company_name;
        $settings->email = $data['email'] ?? $settings->email;
        $settings->phone = $data['phone'] ?? $settings->phone;
        $settings->cnpj = $data['cnpj'] ?? $settings->cnpj;
        $settings->address = $data['address'] ?? $settings->address;
        $settings->checkin = $data['checkin'] ?? $settings->checkin;
        $settings->checkout = $data['checkout'] ?? $settings->checkout;
        $settings->percentage_service_fee = $data['porcentage_service_fee'] ?? $settings->percentage_service_fee;
        $settings->cleaning_rate = $data['cleaning_rate'] ?? $settings->cleaning_rate;
        $settings->display_values_on_dashboard = isset($data['display_values_on_dashboard']) ? 1 : 0;
        $settings->allow_booking_online = isset($data['allow_booking_online']) ? 1 : 0;
        $settings->currency = $data['currency'] ?? $settings->currency;
        $settings->advance_booking_days = $data['advance_booking_days'] ?? $settings->advance_booking_days;
        $settings->cancellation_policies = $data['cancellation_policies'] ?? $settings->cancellation_policies;

        return $settings;
    }
}