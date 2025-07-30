<?php

$router->create("GET", "/users", [$usuarioController, "index"], $auth);

$router->create("GET", "/user", [$usuarioController, "create"], $auth);

$router->create("POST", "/user", [$usuarioController, "store"], $auth);

$router->create("GET", "/user/{id}", [$usuarioController, "edit"], $auth);

$router->create("POST", "/user/{id}", [$usuarioController, "update"], $auth);

$router->create("DELETE", "/user/{id}", [$usuarioController, "delete"], $auth);

$router->create("POST", "/user/{id}/active", [$usuarioController, "activeUser"], $auth);

$router->create("GET", "/user/{id}/permission", [$usuarioController, "permissionUser"], $auth);

$router->create("POST", "/user/{id}/permission", [$usuarioController, "add_permissao"], $auth);


$router->create('POST', '/permissao/{id}/deletar', [$permissaoController, 'delete'], $auth);
$router->create('GET', '/permissao/{id}/editar', [$permissaoController, 'edit'], $auth);
$router->create('POST', '/permissao/{id}/upt', [$permissaoController, 'update'], $auth);
$router->create('POST', '/permissao/add/', [$permissaoController, 'store'], $auth);
$router->create('GET', '/permissao/criar', [$permissaoController, 'create'], $auth);
$router->create('GET', '/permissao/{request}', [$permissaoController, 'index'], $auth);
$router->create('GET', '/permissao/', [$permissaoController, 'index'], $auth);