<?php

namespace App\Http\Controllers\api\v1\Token;

use App\Config\Auth;
use App\Http\Request\Request;
use App\Repositories\Contracts\User\IUsuarioRepository;

class TokenController extends Auth
{
    protected $usuarioRepository;

    public function __construct(IUsuarioRepository $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;  
    }

    public function index()
    {
        echo json_encode([
            'status' => 'success',
            'message' => 'API is working correctly'
        ]);
        http_response_code(200);
        return;
    }

    public function store(Request $request)
    {
        if (!$request->getJsonBody()) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid request body'
            ]);
            return;

        }

        $user = $this->usuarioRepository->getLogin(
            $request->getJsonBody()['email'], 
            $request->getJsonBody()['password']
        );

        if (!$user) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid email or password'
            ]);
            return;
        }

        // Generate and return the token
        $token = $this->generateToken($user);
        echo json_encode([
            'status' => 'success',
            'token' => $token,
            'user' => $user
        ]);
        http_response_code(201);
    }

}