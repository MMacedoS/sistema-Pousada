<?php

$router->create('PUT', '/api/v1/profile/{id}', [$perfilController, 'profileUpdate'], $auth);
$router->create('PUT', '/api/v1/profile/{id}/password', [$perfilController, 'passwordUpdate'], $auth);
$router->create('POST', '/api/v1/profile/{id}/photo', [$perfilController, 'uploadPhoto'], $auth);