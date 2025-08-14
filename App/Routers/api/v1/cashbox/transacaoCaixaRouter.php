<?php

$router->create("GET", "/api/v1/cashbox/{uuid}/transactions", [$caixaController, 'transactions'], $auth);
