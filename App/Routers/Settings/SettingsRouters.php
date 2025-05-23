<?php

$router->create("GET", "/settings", [$configuracaoController, "index"], $auth);

$router->create("POST", "/settings", [$configuracaoController, "storeAndUpdate"], $auth);