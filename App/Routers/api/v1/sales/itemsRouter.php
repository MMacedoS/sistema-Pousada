<?php

$router->create('GET', '/api/v1/sale-items', [$itemVendaController, 'index'], $auth);
$router->create('POST', '/api/v1/sale-items', [$itemVendaController, 'store'], $auth);
$router->create('GET', '/api/v1/sale-items/{uuid}', [$itemVendaController, 'show'], $auth);
$router->create('PUT', '/api/v1/sale-items/{uuid}', [$itemVendaController, 'update'], $auth);
$router->create('DELETE', '/api/v1/sale-items/{uuid}', [$itemVendaController, 'destroy'], $auth);

$router->create('PUT', '/api/v1/sale-items/{uuid}/quantity', [$itemVendaController, 'updateQuantity'], $auth);
