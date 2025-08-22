<?php

namespace App\Config;

use App\Repositories\Contracts\Apartments\IApartamentoRepository;
use App\Repositories\Contracts\Cashbox\ICaixaRepository;
use App\Repositories\Contracts\Cashbox\ITransacaoCaixaRepository;
use App\Repositories\Contracts\Customer\IClienteRepository;
use App\Repositories\Contracts\File\IArquivoRepository;
use App\Repositories\Contracts\Payment\IPagamentoRepository;
use App\Repositories\Contracts\Permission\IPermissaoRepository;
use App\Repositories\Contracts\Person\IPessoaFisicaRepository;
use App\Repositories\Contracts\Product\IProdutoRepository;
use App\Repositories\Contracts\Sale\IItemVendaRepository;
use App\Repositories\Contracts\Sale\IVendaRepository;
use App\Repositories\Contracts\Settings\IConfiguracaoRepository;
use App\Repositories\Contracts\Table\IMesaRepository;
use App\Repositories\Contracts\User\IUsuarioRepository;
use App\Repositories\Entities\Apartments\ApartamentoRepository;
use App\Repositories\Entities\Cashbox\CaixaRepository;
use App\Repositories\Entities\Cashbox\TransacaoCaixaRepository;
use App\Repositories\Entities\Customer\ClienteRepository;
use App\Repositories\Entities\File\ArquivoRepository;
use App\Repositories\Entities\Payment\PagamentoRepository;
use App\Repositories\Entities\Permission\PermissaoRepository;
use App\Repositories\Entities\Person\PessoaFisicaRepository;
use App\Repositories\Entities\Product\ProdutoRepository;
use App\Repositories\Entities\Sale\ItemVendaRepository;
use App\Repositories\Entities\Sale\VendaRepository;
use App\Repositories\Entities\Settings\ConfiguracaoRepository;
use App\Repositories\Entities\Table\MesaRepository;
use App\Repositories\Entities\User\UsuarioRepository;

class AppServiceProvider
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function registerDependencies()
    {
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

        $this->container
            ->set(
                IConfiguracaoRepository::class,
                new ConfiguracaoRepository()
            );

        $this->container
            ->set(
                IApartamentoRepository::class,
                new ApartamentoRepository()
            );

        $this->container
            ->set(
                ICaixaRepository::class,
                new CaixaRepository()
            );

        $this->container
            ->set(
                ITransacaoCaixaRepository::class,
                new TransacaoCaixaRepository()
            );
        $this->container
            ->set(
                IProdutoRepository::class,
                new ProdutoRepository()
            );
        $this->container
            ->set(
                IVendaRepository::class,
                new VendaRepository()
            );
        $this->container->set(
            IItemVendaRepository::class,
            new ItemVendaRepository()
        );
        $this->container->set(
            IMesaRepository::class,
            new MesaRepository()
        );

        $this->container->set(
            IPagamentoRepository::class,
            new PagamentoRepository()
        );

        $this->container->set(
            IClienteRepository::class,
            new ClienteRepository()
        );
    }
}
