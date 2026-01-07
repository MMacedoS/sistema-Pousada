<?php

$router->create('GET', '/api/v1/customers', [$clienteController, 'index'], $auth);
$router->create('POST', '/api/v1/customers', [$clienteController, 'store'], $auth);
$router->create('PUT', '/api/v1/customers/{uuid}', [$clienteController, 'update'], $auth);
$router->create('DELETE', '/api/v1/customers/{uuid}', [$clienteController, 'destroy'], $auth);
