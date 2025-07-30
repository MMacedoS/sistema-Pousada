<?php

$router->create('GET', '/api/v1/token', [$tokenController, 'index'], null);
$router->create('POST', '/api/v1/token', [$tokenController, 'store'], null);