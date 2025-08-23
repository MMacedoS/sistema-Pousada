<?php

$router->create('GET', '/api/v1/reservations', [$reservaController, 'index'], $auth);
$router->create('POST', '/api/v1/reservations', [$reservaController, 'store'], $auth);
$router->create('POST', '/api/v1/reservations-group', [$reservaController, 'storeAll'], $auth);
$router->create('GET', '/api/v1/reservations/{uuid}', [$reservaController, 'show'], $auth);
$router->create('PUT', '/api/v1/reservations/{uuid}', [$reservaController, 'update'], $auth);
$router->create('DELETE', '/api/v1/reservations/{uuid}', [$reservaController, 'destroy'], $auth);

$router->create('POST', '/api/v1/reservations/available', [$reservaController, 'available'], $auth);
$router->create('PUT', '/api/v1/reservations/{uuid}/change-apartment', [$reservaController, 'changeApartment'], $auth);

$router->create('GET', '/api/v1/reservations/reports/financial', [$reservaController, 'financialReport'], $auth);
$router->create('GET', '/api/v1/reservations/reports/status', [$reservaController, 'statusReport'], $auth);
$router->create('GET', '/api/v1/reservations/reports/count', [$reservaController, 'countReport'], $auth);
