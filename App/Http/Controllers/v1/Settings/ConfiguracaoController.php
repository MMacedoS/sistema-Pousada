<?php

namespace App\Http\Controllers\v1\Settings;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Settings\IConfiguracaoRepository;

class ConfiguracaoController extends Controller 
{
    private $configuracaoRepository;

    public function __construct(IConfiguracaoRepository $configuracaoRepository)
    {
        parent::__construct();
        $this->configuracaoRepository = $configuracaoRepository;
    }

    public function index(Request $request)
    {
        $settings = $this->configuracaoRepository->getSettings();
        $this->router->view("Settings/index", ['setting' => $settings]);
    }

    public function storeAndUpdate(Request $request)
    {
        $data = $request->getBodyParams();

        $settings = $this->configuracaoRepository->getSettings();

        if (!$settings) {
            $this->configuracaoRepository->create($data);
            $this->router->redirect('settings');
        }
                
        $this->configuracaoRepository->update($data, $settings->id);

        $this->router->redirect('settings');
    }
}