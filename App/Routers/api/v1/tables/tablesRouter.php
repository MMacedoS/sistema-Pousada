<?php

$router->create('GET', '/api/v1/tables', [$mesaController, 'index'], $auth);
$router->create('POST', '/api/v1/tables', [$mesaController, 'store'], $auth);
$router->create('GET', '/api/v1/tables/{uuid}', [$mesaController, 'show'], $auth);
$router->create('PUT', '/api/v1/tables/{uuid}', [$mesaController, 'update'], $auth);
$router->create('DELETE', '/api/v1/tables/{uuid}', [$mesaController, 'destroy'], $auth);

$router->create('POST', '/api/v1/tables/{uuid}/open', [$mesaController, 'openTable'], $auth);
$router->create('POST', '/api/v1/tables/{uuid}/close', [$mesaController, 'closeTable'], $auth);
$router->create('GET', '/api/v1/tables/available', [$mesaController, 'getAvailableTables'], $auth);
