<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\AppServiceProvider;
use App\Config\Auth;
use App\Config\Container;
use App\Config\Router;
use App\Http\Controllers\api\v1\Apartments\ApartamentoController;
use App\Http\Controllers\api\v1\Cashbox\CaixaController;
use App\Http\Controllers\api\v1\Cashbox\TransacaoCaixaController;
use App\Http\Controllers\api\v1\Employees\FuncionariosController;
use App\Http\Controllers\api\v1\Payment\PagamentoController;
use App\Http\Controllers\api\v1\Permission\PermissaoController;
use App\Http\Controllers\api\v1\Product\ProdutoController;
use App\Http\Controllers\api\v1\Profile\PerfilController;
use App\Http\Controllers\api\v1\Sale\ItemVendaController;
use App\Http\Controllers\api\v1\Sale\VendaController;
use App\Http\Controllers\api\v1\Settings\ConfiguracaoController;
use App\Http\Controllers\api\v1\Token\TokenController;

$container = new Container();

$appServiceProvider = new AppServiceProvider($container);
$appServiceProvider->registerDependencies();

$tokenController = $container->get(TokenController::class);
$apartamentoController = $container->get(ApartamentoController::class);
$perfilController = $container->get(PerfilController::class);
$settingsController = $container->get(ConfiguracaoController::class);
$caixaController = $container->get(CaixaController::class);
$transacaoCaixaController = $container->get(TransacaoCaixaController::class);
$funcionariosController = $container->get(FuncionariosController::class);
$permissaoController = $container->get(PermissaoController::class);
$produtoController = $container->get(ProdutoController::class);
$vendaController = $container->get(VendaController::class);
$itemVendaController = $container->get(ItemVendaController::class);
$pagamentoController = $container->get(PagamentoController::class);

$router = new Router();
$auth = new Auth();

require_once __DIR__ . '/api/v1/token/tokenRouter.php';
require_once __DIR__ . '/api/v1/apartments/apartmentsRouter.php';
require_once __DIR__ . '/api/v1/profile/profileRouter.php';
require_once __DIR__ . '/api/v1/settings/settingsRouter.php';
require_once __DIR__ . '/api/v1/cashbox/cashboxRouter.php';
require_once __DIR__ . '/api/v1/employees/employeesRouter.php';
require_once __DIR__ . '/api/v1/permission/permissionRouter.php';
require_once __DIR__ . '/api/v1/products/productsRouter.php';
require_once __DIR__ . '/api/v1/sales/salesRouter.php';
require_once __DIR__ . '/api/v1/payments/paymentsRouter.php';

return $router;
