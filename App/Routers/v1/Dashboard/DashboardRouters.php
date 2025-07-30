<?php

$router->create("GET", "/dashboard", [$dashboardController, "index"], $auth);