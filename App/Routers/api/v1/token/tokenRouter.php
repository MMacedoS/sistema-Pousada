<?php

$router->create('GET', '/api/v1/token', [$tokenController, 'index'], null);
$router->create('POST', '/api/v1/login', [$tokenController, 'store'], null);
$router->create('GET', '/api/v1/token/refresh', [$tokenController, 'refresh'], null);
