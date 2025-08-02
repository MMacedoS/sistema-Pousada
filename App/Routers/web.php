<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\AppServiceProvider;
use App\Config\Auth;
use App\Config\Container;
use App\Config\Router;
use App\Http\Controllers\api\v1\Apartments\ApartamentoController;
use App\Http\Controllers\api\v1\Profile\PerfilController;
use App\Http\Controllers\api\v1\Settings\ConfiguracaoController;
use App\Http\Controllers\api\v1\Token\TokenController;

$container = new Container();

$appServiceProvider = new AppServiceProvider($container);
$appServiceProvider->registerDependencies();

$tokenController = $container->get(TokenController::class);
$apartamentoController = $container->get(ApartamentoController::class);
$perfilController = $container->get(PerfilController::class);
$settingsController = $container->get(ConfiguracaoController::class);

$router = new Router();
$auth = new Auth();

// require_once __DIR__ . '/v1/Dashboard/DashboardRouters.php';
// require_once __DIR__ . '/v1/Home/HomeRouters.php';
// require_once __DIR__ . '/v1/Users/UsersRouters.php';
// require_once __DIR__ . '/v1/Users/LoginRouters.php';
// require_once __DIR__ . '/v1/404/NotFoundRouters.php';
// require_once __DIR__ . '/v1/Settings/SettingsRouters.php';
// require_once __DIR__ . '/v1/Apartments/ApartmentsRouters.php';
// require_once __DIR__ . '/v1/Customers/CustomerRouters.php';
// require_once __DIR__ . '/v1/Permissions/PermissionRouters.php';
require_once __DIR__ . '/api/v1/token/tokenRouter.php';
require_once __DIR__ . '/api/v1/apartments/apartmentsRouter.php';
require_once __DIR__ . '/api/v1/profile/profileRouter.php';
require_once __DIR__ . '/api/v1/settings/settingsRouter.php';

return $router;