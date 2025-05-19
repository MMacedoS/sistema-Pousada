<?php

namespace App\Repositories\Contracts\Settings;

interface ISiteSettingRepository 
{
    public function getSettings();

    public function create(array $params);
    
    public function update(array $params, int $id);

    public function findByUuid(string $uuid);

    public function findById(int $id);
}