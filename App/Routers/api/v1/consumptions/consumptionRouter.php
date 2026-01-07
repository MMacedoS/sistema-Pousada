<?php

$router->create('GET', '/api/v1/reservations/{id}/consumptions', [$consumoController, 'index'], $auth);
$router->create('POST', '/api/v1/reservations/{id}/consumptions', [$consumoController, 'store'], $auth);
$router->create('DELETE', '/api/v1/reservations/{id}/consumptions/{consumptionId}', [$consumoController, 'destroy'], $auth);

$router->create('GET', '/api/v1/consumptions-all', [$consumoController, 'getConsumptionByPeriod'], null);
