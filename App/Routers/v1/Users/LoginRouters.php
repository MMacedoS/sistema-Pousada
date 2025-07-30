<?php

$router->create("GET", "/login", [$usuarioController, "login"], null);

$router->create("POST", "/login", [$usuarioController, "auth"], null);

$router->create("GET", "/logout", [$usuarioController, "logout"], $auth);