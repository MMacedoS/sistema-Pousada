<?php

$router->create('GET', '/api/v1/reservations/{uuid}/per-diems', [$diariaController, 'index'], $auth);
$router->create('POST', '/api/v1/reservations/{uuid}/per-diems', [$diariaController, 'store'], $auth);
$router->create('DELETE', '/api/v1/reservations/{uuid}/per-diems/{uuid}', [$diariaController, 'destroy'], $auth);
