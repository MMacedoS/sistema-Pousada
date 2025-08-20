<?php

namespace App\Http\Controllers\v1\Traits;

use App\Repositories\Entities\Person\PessoaFisicaRepository;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

trait UserToPerson
{
    public function authUser()
    {
        $user = $_SESSION['user']->code;
        $pessoaFisicaRepository = new PessoaFisicaRepository();

        $pessoa = $pessoaFisicaRepository->personByUserId($user);

        return $pessoa;
    }

    public function authUserByApi()
    {
        if (!isset($_COOKIE['refresh_token'])) {
            return null;
        }

        $refreshToken = $_COOKIE['refresh_token'];

        try {
            $decoded = JWT::decode($refreshToken, new Key($_ENV['SECRET_KEY'], 'HS256'));
            $userId = $decoded->sub;
            return $userId;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['status' => 401, 'message' => 'Invalid or expired refresh token']);
        }
    }
}
