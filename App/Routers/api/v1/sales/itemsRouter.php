<?php

$router->create('GET', '/api/v1/sales/{uuid}/sale-items', [$itemVendaController, 'index'], $auth);
$router->create('POST', '/api/v1/sales/{uuid}/sale-items', [$itemVendaController, 'store'], $auth);
$router->create('GET', '/api/v1/sales/{uuid}/sale-items/{item_uuid}', [$itemVendaController, 'show'], $auth);
$router->create('PUT', '/api/v1/sales/{uuid}/sale-items/{item_uuid}', [$itemVendaController, 'updateQuantity'], $auth);
$router->create('DELETE', '/api/v1/sales/{uuid}/sale-items/{item_uuid}', [$itemVendaController, 'destroy'], $auth);

$router->create('PUT', '/api/v1/sales/{uuid}/sale-items/{item_uuid}/quantity', [$itemVendaController, 'updateQuantity'], $auth);
