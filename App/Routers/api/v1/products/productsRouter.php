<?php

$router->create('GET', '/api/v1/products', [$produtoController, 'index'], $auth);
$router->create('POST', '/api/v1/products', [$produtoController, 'store'], $auth);
$router->create('GET', '/api/v1/products/{uuid}', [$produtoController, 'show'], $auth);
$router->create('PUT', '/api/v1/products/{uuid}', [$produtoController, 'update'], $auth);
$router->create('DELETE', '/api/v1/products/{uuid}', [$produtoController, 'destroy'], $auth);

$router->create('GET', '/api/v1/products/category/{category}', [$produtoController, 'getByCategory'], $auth);
$router->create('GET', '/api/v1/products/available', [$produtoController, 'getAvailableProducts'], $auth);
$router->create('PUT', '/api/v1/products/{uuid}/stock', [$produtoController, 'updateStock'], $auth);
