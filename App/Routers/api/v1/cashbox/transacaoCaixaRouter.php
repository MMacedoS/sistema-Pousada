<?php

$router->create("GET", "/api/v1/transacao-caixa", [$transacaoCaixaController, 'index'], $auth);
$router->create("POST", "/api/v1/transacao-caixa", [$transacaoCaixaController, 'store'], $auth);
$router->create("GET", "/api/v1/transacao-caixa/{uuid}", [$transacaoCaixaController, 'show'], $auth);
$router->create("PUT", "/api/v1/transacao-caixa/{uuid}/cancel", [$transacaoCaixaController, 'cancel'], $auth);
$router->create("GET", "/api/v1/transacao-caixa/caixa/{caixa_id}", [$transacaoCaixaController, 'byCaixa'], $auth);
$router->create("GET", "/api/v1/transacao-caixa/relatorio", [$transacaoCaixaController, 'relatorio'], $auth);
