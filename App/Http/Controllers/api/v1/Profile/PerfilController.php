<?php

namespace App\Http\Controllers\api\v1\Profile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\Traits\GenericTrait;
use App\Http\Controllers\v1\Traits\UserToPerson;
use App\Http\Controllers\Traits\HasPermissions;
use App\Http\Request\Request;
use App\Repositories\Contracts\Person\IPessoaFisicaRepository;
use App\Repositories\Contracts\User\IUsuarioRepository;

class PerfilController extends Controller
{
    protected $pessoaFisicaRepository;
    protected $usuarioRepository;

    public function __construct(
        IPessoaFisicaRepository $pessoaFisicaRepository,
        IUsuarioRepository $usuarioRepository
    ) {
        $this->pessoaFisicaRepository = $pessoaFisicaRepository;
        $this->usuarioRepository = $usuarioRepository;
    }

    public function profileUpdate(Request $request, string $id)
    {
        $data = $request->getJsonBody();

        if (is_null($id) || empty($id)) {
            return $this->responseJson("usuario não pode ser nullo", 422);
        }

        $user = $this->usuarioRepository->findByUuid($id);

        if (is_null($user)) {
            return $this->responseJson("usuario não encontrado", 422);
        }

        $person = $this->pessoaFisicaRepository->personByUserId($user->id);

        if (is_null($person)) {
            return $this->responseJson("pessoa fisica não encontrada", 422);
        }

        $updatedPerson = $this->pessoaFisicaRepository->update($data, $person->id);

        if (is_null($updatedPerson)) {
            return $this->responseJson("pessoa fisica não atualizada", 422);
        }

        $updated = $this->usuarioRepository->update($data, $person->usuario_id);

        if (is_null($updated)) {
            return $this->responseJson("usuario não atualizado", 422);
        }

        $this->responseJson($updated, 202);
    }

    public function passwordUpdate(Request $request, string $id)
    {
        $user = $this->usuarioRepository->findByUuid($id);

        if (is_null($user)) {
            return $this->responseJson("usuario não encontrado", 422);
        }

        $data = $request->getJsonBody();

        $updated = $this->usuarioRepository->updatePassword($data, $user->id);

        if (is_null($updated)) {
            return $this->responseJson("usuario não atualizado", 422);
        }

        $this->responseJson($updated, 202);
    }

    public function uploadPhoto(Request $request, string $id)
    {
        if (!isset($_FILES['file'])) {
            return $this->responseJson("Arquivo não enviado", 400);
        }

        $data = $request->getBodyParams();

        if (isset($_FILES['file'])) {
            $data['file'] = $_FILES['file'];
        }

        $dir = '/files/profile/';

        $user = $this->usuarioRepository->findByUuid($id);

        if (is_null($user)) {
            return $this->responseJson("Usuário não encontrado", 422);
        }

        $dir = '/files/profile/';

        $fileUploaded = $this->usuarioRepository->updatePhoto($data, $dir, $user->id);

        $this->responseJson($fileUploaded, 202);
    }
}
