<?php

$router->create('GET', '/api/v1/payments', [$pagamentoController, 'index'], $auth);
$router->create('POST', '/api/v1/payments', [$pagamentoController, 'store'], $auth);
$router->create('GET', '/api/v1/payments/{uuid}', [$pagamentoController, 'show'], $auth);
$router->create('PUT', '/api/v1/payments/{uuid}', [$pagamentoController, 'update'], $auth);
$router->create('DELETE', '/api/v1/payments/{uuid}', [$pagamentoController, 'destroy'], $auth);

$router->create('GET', '/api/v1/sales/{venda_uuid}/payments', [$pagamentoController, 'getPaymentsByVenda'], $auth);
$router->create('PATCH', '/api/v1/payments/{uuid}/cancel', [$pagamentoController, 'cancelPayment'], $auth);
$router->create('GET', '/api/v1/payments/sales/{venda_uuid}/list', [$pagamentoController, 'getPaymentsByVenda'], $auth);
