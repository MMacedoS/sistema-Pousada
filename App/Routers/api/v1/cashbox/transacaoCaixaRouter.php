<?php

$router->create("POST", "/api/v1/cashbox/{uuid}/transactions", [$transacaoCaixaController, 'store'], $auth);
$router->create("PUT", "/api/v1/cashbox/{uuid}/transactions/{id}", [$transacaoCaixaController, 'update'], $auth);
$router->create("DELETE", "/api/v1/cashbox/{uuid}/transactions/{id}", [$transacaoCaixaController, 'cancel'], $auth);
$router->create("GET", "/api/v1/cashbox/transactions/report", [$transacaoCaixaController, 'reportTransactions'], $auth);
$router->create("GET", "/api/v1/user/{uuid}/transactions", [$transacaoCaixaUsuarioController, 'index'], $auth);
