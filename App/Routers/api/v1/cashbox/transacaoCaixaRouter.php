<?php

// $router->create("GET", "/api/v1/cashbox/{uuid}/transactions/report", [$transacaoController, 'report'], $auth);
// $router->create("GET", "/api/v1/cashbox/{uuid}/transactions/{id}", [$transacaoController, 'show'], $auth);
$router->create("POST", "/api/v1/cashbox/{uuid}/transactions", [$transacaoCaixaController, 'store'], $auth);
$router->create("PUT", "/api/v1/cashbox/{uuid}/transactions/{id}", [$transacaoCaixaController, 'update'], $auth);
$router->create("DELETE", "/api/v1/cashbox/{uuid}/transactions/{id}", [$transacaoCaixaController, 'cancel'], $auth);
