<?php

$router->create("GET", "/api/v1/permissions", [$permissaoController, 'index'], $auth);
$router->create("GET", "/api/v1/permissions-list", [$permissaoController, 'indexWithoutPagination'], $auth);
$router->create("POST", "/api/v1/permissions", [$permissaoController, 'store'], $auth);
$router->create("PUT", "/api/v1/permissions/{uuid}", [$permissaoController, 'update'], $auth);
$router->create("GET", "/api/v1/permissions-by-user/{uuid}", [$permissaoController, 'permissionsByUser'], $auth);
$router->create("POST", "/api/v1/assign-permission/{userId}", [$permissaoController, 'assignPermissionToUser'], $auth);
