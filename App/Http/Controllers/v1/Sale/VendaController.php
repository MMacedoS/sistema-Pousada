<?php

namespace App\Http\Controllers\v1\Sale;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\Traits\GenericTrait;
use App\Http\Request\Request;
use App\Repositories\Contracts\Sale\IVendaRepository;
use App\Repositories\Contracts\Sale\IItemVendaRepository;
use App\Repositories\Contracts\Table\IMesaRepository;
use App\Repositories\Contracts\Payment\IPagamentoRepository;
use App\Repositories\Contracts\Cashbox\ICaixaRepository;
use App\Transformers\Sale\VendaTransformer;
use App\Utils\Paginator;
use App\Utils\Validator;

class VendaController extends Controller
{
    use GenericTrait;

    protected $vendaRepository;
    protected $itemVendaRepository;
    protected $mesaRepository;
    protected $pagamentoRepository;
    protected $caixaRepository;
    protected $vendaTransformer;

    public function __construct(
        IVendaRepository $vendaRepository,
        IItemVendaRepository $itemVendaRepository,
        IMesaRepository $mesaRepository,
        IPagamentoRepository $pagamentoRepository,
        ICaixaRepository $caixaRepository,
        VendaTransformer $vendaTransformer
    ) {
        parent::__construct();
        $this->vendaRepository = $vendaRepository;
        $this->itemVendaRepository = $itemVendaRepository;
        $this->mesaRepository = $mesaRepository;
        $this->pagamentoRepository = $pagamentoRepository;
        $this->caixaRepository = $caixaRepository;
        $this->vendaTransformer = $vendaTransformer;
    }

    public function index(Request $request)
    {
        try {
            $params = $request->getQueryParams();
            $vendas = $this->vendaRepository->all($params);

            if ($request->isApi()) {
                $perPage = 10;
                $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
                $paginator = new Paginator($vendas, $perPage, $currentPage);
                $paginatedVendas = $paginator->getPaginatedItems();

                return $this->jsonResponse([
                    'success' => true,
                    'data' => $this->vendaTransformer->transformCollection($paginatedVendas),
                    'pagination' => [
                        'current_page' => $currentPage,
                        'per_page' => $perPage,
                        'total' => count($vendas),
                        'total_pages' => $paginator->getTotalPages()
                    ]
                ]);
            }

            return $this->router->view('Sale/index', [
                'active' => 'sales',
                'vendas' => $vendas
            ]);
        } catch (\Exception $e) {
            if ($request->isApi()) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Erro ao buscar vendas'
                ], 500);
            }
            return $this->router->redirect('dashboard?error=500');
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->getParsedBody();

            $validator = new Validator($data);
            $validator->setRules([
                'name' => 'required|string|max:20',
                'description' => 'string|max:255',
                'id_usuario' => 'required|integer'
            ]);

            if (!$validator->validate()) {
                if ($request->isApi()) {
                    return $this->jsonResponse([
                        'success' => false,
                        'message' => 'Dados inválidos',
                        'errors' => $validator->getErrors()
                    ], 422);
                }
                return $this->router->redirect('sales?error=validation');
            }

            $data['dt_sale'] = date('Y-m-d');
            $data['amount_sale'] = 0.00;
            $data['status'] = 1;

            $venda = $this->vendaRepository->create($data);

            if ($request->isApi()) {
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Venda criada com sucesso',
                    'data' => $this->vendaTransformer->transform($venda)
                ], 201);
            }

            return $this->router->redirect('sales?success=created');
        } catch (\Exception $e) {
            if ($request->isApi()) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Erro ao criar venda'
                ], 500);
            }
            return $this->router->redirect('sales?error=500');
        }
    }

    public function show(Request $request)
    {
        try {
            $uuid = $request->getParam('id');
            $venda = $this->vendaRepository->findByUuid($uuid);

            if (!$venda) {
                if ($request->isApi()) {
                    return $this->jsonResponse([
                        'success' => false,
                        'message' => 'Venda não encontrada'
                    ], 404);
                }
                return $this->router->redirect('sales?error=404');
            }

            $items = $this->itemVendaRepository->findByVenda($venda->id);
            $pagamentos = $this->pagamentoRepository->findByVenda($venda->id);

            if ($request->isApi()) {
                return $this->jsonResponse([
                    'success' => true,
                    'data' => [
                        'sale' => $this->vendaTransformer->transform($venda),
                        'items' => $items,
                        'payments' => $pagamentos
                    ]
                ]);
            }

            return $this->router->view('Sale/show', [
                'active' => 'sales',
                'venda' => $venda,
                'items' => $items,
                'pagamentos' => $pagamentos
            ]);
        } catch (\Exception $e) {
            if ($request->isApi()) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Erro ao buscar venda'
                ], 500);
            }
            return $this->router->redirect('sales?error=500');
        }
    }

    public function update(Request $request)
    {
        try {
            $uuid = $request->getParam('id');
            $data = $request->getParsedBody();

            $venda = $this->vendaRepository->findByUuid($uuid);
            if (!$venda) {
                if ($request->isApi()) {
                    return $this->jsonResponse([
                        'success' => false,
                        'message' => 'Venda não encontrada'
                    ], 404);
                }
                return $this->router->redirect('sales?error=404');
            }

            $validator = new Validator($data);
            $validator->setRules([
                'name' => 'string|max:20',
                'description' => 'string|max:255'
            ]);

            if (!$validator->validate()) {
                if ($request->isApi()) {
                    return $this->jsonResponse([
                        'success' => false,
                        'message' => 'Dados inválidos',
                        'errors' => $validator->getErrors()
                    ], 422);
                }
                return $this->router->redirect('sales?error=validation');
            }

            $updatedVenda = $this->vendaRepository->update($data, $venda->id);

            if ($request->isApi()) {
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Venda atualizada com sucesso',
                    'data' => $this->vendaTransformer->transform($updatedVenda)
                ]);
            }

            return $this->router->redirect('sales?success=updated');
        } catch (\Exception $e) {
            if ($request->isApi()) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Erro ao atualizar venda'
                ], 500);
            }
            return $this->router->redirect('sales?error=500');
        }
    }

    public function destroy(Request $request)
    {
        try {
            $uuid = $request->getParam('id');
            $venda = $this->vendaRepository->findByUuid($uuid);

            if (!$venda) {
                if ($request->isApi()) {
                    return $this->jsonResponse([
                        'success' => false,
                        'message' => 'Venda não encontrada'
                    ], 404);
                }
                return $this->router->redirect('sales?error=404');
            }

            $this->vendaRepository->delete($venda->id);

            if ($request->isApi()) {
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Venda cancelada com sucesso'
                ]);
            }

            return $this->router->redirect('sales?success=deleted');
        } catch (\Exception $e) {
            if ($request->isApi()) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Erro ao cancelar venda'
                ], 500);
            }
            return $this->router->redirect('sales?error=500');
        }
    }

    public function closeSale(Request $request)
    {
        try {
            $uuid = $request->getParam('id');
            $data = $request->getParsedBody();

            $venda = $this->vendaRepository->findByUuid($uuid);
            if (!$venda) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Venda não encontrada'
                ], 404);
            }

            $total = $this->itemVendaRepository->getTotalByVenda($venda->id);
            
            $this->vendaRepository->update(['amount_sale' => $total], $venda->id);
            $this->vendaRepository->closeSale($venda->id);

            if (isset($data['mesa_id'])) {
                $this->mesaRepository->freeTable($data['mesa_id']);
            }

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Venda fechada com sucesso'
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao fechar venda'
            ], 500);
        }
    }
}
        $sales = $this->vendaRepository->all($params);
        $reserva = $this->reservaRepository->allHosted();

        $salesReservaIds = array_map(function($sale) {
            return $sale->id_reserva;
        }, $sales);

        $reservasFiltradas = array_filter($reserva, function($reserva) use ($salesReservaIds) {
            return !in_array($reserva->id, $salesReservaIds); 
        });

        return $this->router->view('sale/create', ['active' => 'register', 'reservas' => $reservasFiltradas]);
    }

    public function store(Request $request) {
        $data = $request->getBodyParams();
        
        $validator = new Validator($data);
        $rules = [
            'name' => 'required',
            'description' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'sale/create', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 

        $data['id_usuario'] = $_SESSION['user']->code;
        
        $created = $this->vendaRepository->create($data);

        if(is_null($created)) {            
        return $this->router->view('sale/create', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('vendas/');
    }

    public function edit(Request $request, $id) {
        $saleCurrent = $this->vendaRepository->findByUuid($id);

        if (is_null($saleCurrent)) {
            return $this->router->view('sale/', ['active' => 'register', 'danger' => true]);
        }

        empty($params) ? $params['status'] = 1 : $params;
        $sales = $this->vendaRepository->all($params);
        $reserva = $this->reservaRepository->allHosted();

        $salesReservaIds = array_map(function($sale) use ($saleCurrent) {
            if($sale->id != $saleCurrent->id) {
                return $sale->id_reserva;
            }
        }, $sales);

        $reservasFiltradas = array_filter($reserva, function($reserva) use ($salesReservaIds) {
            return !in_array($reserva->id, $salesReservaIds); 
        });
        
        return $this->router->view('sale/edit', [
            'active' => 'register', 
            'sale' => $saleCurrent,
            'reservas' => $reservasFiltradas
        ]);
    }

    public function update(Request $request, $id) 
    {
        $saleCurrent = $this->vendaRepository->findByUuid($id);

        if (is_null($saleCurrent)) {
            return $this->router->view('sale/', ['active' => 'register', 'danger' => true]);
        }

        $data = $request->getBodyParams();
        
        $validator = new Validator($data);
        $rules = [
            'name' => 'required',
            'description' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'sale/create', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 

        $data['id_usuario'] = $_SESSION['user']->code;        
        $data['reserve_id'] = empty($data['reserve_id']) ? null : $data['reserve_id'];
        
        $created = $this->vendaRepository->update($data, $saleCurrent->id);

        if(is_null($created)) {            
        return $this->router->view('sale/create', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('vendas/');
    }
}