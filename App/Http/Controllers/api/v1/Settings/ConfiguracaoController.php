<?php

namespace App\Http\Controllers\api\v1\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\Traits\GenericTrait;
use App\Http\Controllers\Traits\HasPermissions;
use App\Http\Request\Request;
use App\Repositories\Contracts\Settings\IConfiguracaoRepository;
use App\Transformers\Settings\SettingTransformer;

class ConfiguracaoController extends Controller
{
    private $configuracaoRepository;
    private $settingTransformer;

    public function __construct(IConfiguracaoRepository $configuracaoRepository, SettingTransformer $settingTransformer)
    {
        $this->configuracaoRepository = $configuracaoRepository;
        $this->settingTransformer = $settingTransformer;
    }

    public function index(Request $request)
    {
        $this->checkPermission('settings.view');

        $settings = $this->configuracaoRepository->getSettings();
        if (!$settings) {
            http_response_code(404);
            echo json_encode(['status' => 404, 'message' => 'Settings not found']);
            return;
        }

        $settings = $this->settingTransformer->transform($settings);

        $this->responseJson($settings, 202);
        return;
    }

    public function storeAndUpdate(Request $request)
    {
        $this->checkPermission('settings.edit');

        $data = $request->getJsonBody();

        $settings = $this->configuracaoRepository->getSettings();

        if (!$settings) {
            $created = $this->configuracaoRepository->create($data);
            $this->responseJson($created, 202);
            return;
        }

        $updated = $this->configuracaoRepository->update($data, $settings->id);
        $this->responseJson($updated, 202);
        return;
    }
}
