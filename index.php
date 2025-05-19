<?php

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$router = require __DIR__ . '/App/Routers/web.php';

$router->init();
