<?php

namespace App\Repositories\Contracts\Settings;

interface IConfiguracaoRepository 
{
    public function getSettings();

    public function create(array $params);
    
    public function update(array $params, int $id);

    public function findById(int $id);
}