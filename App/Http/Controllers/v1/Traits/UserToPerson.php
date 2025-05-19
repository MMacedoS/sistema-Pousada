<?php
 
namespace App\Http\Controllers\v1\Traits;

use App\Repositories\Entities\Person\PessoaFisicaRepository;

trait UserToPerson{
    public function authUser() 
    {
        $user = $_SESSION['user']->code;
        $pessoaFisicaRepository = new PessoaFisicaRepository();

        $pessoa = $pessoaFisicaRepository->personByUserId($user);

        return $pessoa;
    }
}