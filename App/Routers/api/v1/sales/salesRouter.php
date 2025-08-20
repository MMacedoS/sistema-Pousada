<?php

$router->create('GET', '/api/v1/sales', [$vendaController, 'index'], $auth);
$router->create('POST', '/api/v1/sales', [$vendaController, 'store'], $auth);
$router->create('GET', '/api/v1/sales/{uuid}', [$vendaController, 'show'], $auth);
$router->create('PUT', '/api/v1/sales/{uuid}', [$vendaController, 'update'], $auth);
$router->create('GET', '/api/v1/sales/{uuid}/close', [$vendaController, 'closeSale'], $auth);
$router->create('DELETE', '/api/v1/sales/{uuid}', [$vendaController, 'cancelSale'], $auth);

$router->create('GET', '/api/v1/sales/reports/total', [$vendaController, 'getSalesReport'], $auth);

require_once __DIR__ . '/itemsRouter.php';
