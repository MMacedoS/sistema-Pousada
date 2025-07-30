<?php

$router->create('GET', '/api/v1/apartments', [$apartamentoController, 'index'], $auth);
$router->create('POST', '/api/v1/apartments', [$apartamentoController, 'store'], $auth);
$router->create('PUT', '/api/v1/apartments/{id}', [$apartamentoController, 'update'], $auth);
$router->create('DELETE', '/api/v1/apartments/{id}', [$apartamentoController, 'destroy'], $auth);   
$router->create('GET', '/api/v1/apartments/{id}', [$apartamentoController, 'show'], $auth);
$router->create('POST', '/api/v1/apartments/{id}/active', [$apartamentoController, 'changeActiveStatus'], $auth);
$router->create('POST', '/api/v1/apartments/available', [$apartamentoController, 'available'], $auth);


