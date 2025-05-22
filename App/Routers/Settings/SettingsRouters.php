<?php

$router->create("GET", "/settings", [$configuracaoController, "index"], $auth);