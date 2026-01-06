<?php

$router->create('GET', '/api/v1/financial/dashboard', [$financeiroController, 'dashboard'], $auth);
$router->create('GET', '/api/v1/financial/revenue', [$financeiroController, 'revenue'], $auth);
$router->create('GET', '/api/v1/financial/expenses', [$financeiroController, 'expenses'], $auth);
$router->create('GET', '/api/v1/financial/cash-flow', [$financeiroController, 'cashFlow'], $auth);
$router->create('GET', '/api/v1/financial/payment-methods', [$financeiroController, 'paymentMethods'], $auth);
$router->create('GET', '/api/v1/financial/summary', [$financeiroController, 'summary'], $auth);
