<?php

namespace App\Config;

use App\Repositories\Contracts\File\IArquivoRepository;
use App\Repositories\Contracts\Permission\IPermissaoRepository;
use App\Repositories\Contracts\Person\IPessoaFisicaRepository;
use App\Repositories\Contracts\User\IUsuarioRepository;
use App\Repositories\Entities\File\ArquivoRepository;
use App\Repositories\Entities\Permission\PermissaoRepository;
use App\Repositories\Entities\Person\PessoaFisicaRepository;
use App\Repositories\Entities\User\UsuarioRepository;

class AppServiceProvider 
{
    protected $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function registerDependencies() {
        $this->container
            ->set(
                IArquivoRepository::class,
                new ArquivoRepository()
            );

        $this->container
            ->set(
                IPermissaoRepository::class,
                new PermissaoRepository()
            );

        $this->container
            ->set(
                IPessoaFisicaRepository::class,
                new PessoaFisicaRepository()
            );

        $this->container
            ->set(
                IUsuarioRepository::class,
                new UsuarioRepository()
            );
    }

}