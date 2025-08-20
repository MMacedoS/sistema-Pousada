<?php

$create = $router->create("GET", "/api/v1/employees", [$funcionariosController, 'index'], $auth);
$router->create("POST", "/api/v1/employees", [$funcionariosController, 'store'], $auth);
$router->create("PUT", "/api/v1/employees/{uuid}", [$funcionariosController, 'update'], $auth);
$router->create("DELETE", "/api/v1/employees/{uuid}", [$funcionariosController, 'destroy'], $auth);
