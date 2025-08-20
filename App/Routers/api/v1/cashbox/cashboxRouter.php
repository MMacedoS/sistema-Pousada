<?php

$router->create("GET", "/api/v1/cashbox", [$caixaController, 'index'], $auth);
$router->create("POST", "/api/v1/cashbox", [$caixaController, 'store'], $auth);
$router->create("GET", "/api/v1/cashbox/{uuid}", [$caixaController, 'show'], $auth);
$router->create("PUT", "/api/v1/cashbox/{uuid}", [$caixaController, 'update'], $auth);
$router->create("GET", "/api/v1/cashbox/{uuid}/transactions", [$caixaController, 'transactions'], $auth);
$router->create("POST", "/api/v1/cashbox/{uuid}/close", [$caixaController, 'closed'], $auth);

require_once __DIR__ . '/transacaoCaixaRouter.php';
