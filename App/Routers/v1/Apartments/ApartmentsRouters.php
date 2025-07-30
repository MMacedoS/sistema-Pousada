<?php

$router->create("GET", "/apartments", [$apartamentoController, "index"], $auth);

$router->create("GET", "/apartment", [$apartamentoController, "create"], $auth);
$router->create("POST", "/apartment", [$apartamentoController, "store"], $auth);
$router->create("GET", "/apartment/{id}", [$apartamentoController, "edit"], $auth);
$router->create("POST", "/apartment/{id}", [$apartamentoController, "update"], $auth);
$router->create("DELETE", "/apartment/{id}", [$apartamentoController, "changeSituation"], $auth);
