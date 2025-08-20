<?php

$router->create('GET', '/api/v1/settings', [$settingsController, 'index'], $auth);

$router->create('PUT', '/api/v1/settings', [$settingsController, 'storeAndUpdate'], $auth);