<?php

$router->create('GET', '/api/v1/dashboard/apartments', [$dashboardController, 'apartmentsStatus'], $auth);

$router->create('GET', '/api/v1/dashboard/checkin-today', [$dashboardController, 'checkinToday'], $auth);

$router->create('GET', '/api/v1/dashboard/checkout-today', [$dashboardController, 'checkoutToday'], $auth);

$router->create('GET', '/api/v1/dashboard/guests', [$dashboardController, 'guestsCount'], $auth);

$router->create('GET', '/api/v1/dashboard/daily-revenue', [$dashboardController, 'dailyRevenue'], $auth);
