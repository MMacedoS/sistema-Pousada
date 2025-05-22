<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\AppServiceProvider;
use App\Config\Auth;
use App\Config\Container;
use App\Config\Router;
use App\Http\Controllers\v1\Dashboard\DashboardController;
use App\Http\Controllers\v1\Page404\NotFoundController;
use App\Http\Controllers\v1\Settings\ConfiguracaoController;
use App\Http\Controllers\v1\Site\SiteController;
use App\Http\Controllers\v1\User\UsuarioController;

$container = new Container();

$appServiceProvider = new AppServiceProvider($container);
$appServiceProvider->registerDependencies();

$dashboardController = $container->get(DashboardController::class);
$siteController = $container->get(SiteController::class);
$usuarioController = $container->get(UsuarioController::class);
$notFoundController = $container->get(NotFoundController::class);
$configuracaoController = $container->get(ConfiguracaoController::class);

$router = new Router();
$auth = new Auth();

require_once __DIR__ . '/Dashboard/DashboardRouters.php';
require_once __DIR__ . '/Home/HomeRouters.php';
require_once __DIR__ . '/Users/UsersRouters.php';
require_once __DIR__ . '/Users/LoginRouters.php';
require_once __DIR__ . '/404/NotFoundRouters.php';
require_once __DIR__ . '/Settings/SettingsRouters.php';

return $router;