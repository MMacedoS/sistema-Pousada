<?php

namespace App\Http\Controllers\api\v1\Token;

use App\Config\Auth;
use App\Http\Request\Request;
use App\Repositories\Contracts\User\IUsuarioRepository;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
            echo json_encode(['status' => 400, 'message' => 'Invalid request body']);
            return;
        }

        $body = $request->getJsonBody();
        $user = $this->usuarioRepository->getLogin($body['email'], $body['password']);

        if (!$user) {
            http_response_code(401);
            echo json_encode(['status' => 401, 'message' => 'Invalid email or password']);
            return;
        }

        $tokens = $this->generateTokens($user);
        $userData = $this->usuarioRepository->findByIdWithPhoto((int)$user->code);

        $isSecure = true; // HTTPS local e produção
        $sameSite = 'None'; // necessário para cookies cross-site (React em outro domínio/porta)

        setcookie('refresh_token', $tokens['refresh_token'], [
            'expires' => time() + (60 * 60 * 24 * 7),
            'path' => '/',
            'secure' => $isSecure,         
            'httponly' => true,            
            'samesite' => $sameSite        
        ]);

        http_response_code(201);
        echo json_encode([
            'status' => 200,
            'access_token' => $tokens['access_token'],
            'user' => $userData,
        ]);
    }

    public function refresh()
    {
        if (!isset($_COOKIE['refresh_token'])) {
            http_response_code(401);
            echo json_encode(['status' => 401, 'message' => 'No refresh token provided']);
            return;
        }

        $refreshToken = $_COOKIE['refresh_token'];

        try {
            $decoded = JWT::decode($refreshToken, new Key($_ENV['SECRET_KEY'], 'HS256'));
            $userId = $decoded->sub;

            $user = $this->usuarioRepository->findByIdWithPhoto((int)$userId);
            if (!$user) {
                throw new Exception('User not found');
            }

            // Regenera os tokens (acesso + refresh)
            $tokens = $this->generateTokens($user);

            // Renova o cookie do refresh_token
            $isSecure = true;
            $sameSite = 'None';
            setcookie('refresh_token', $tokens['refresh_token'], [
                'expires' => time() + (60 * 60 * 24 * 7), // 7 dias
                'path' => '/',
                'secure' => $isSecure,
                'httponly' => true,
                'samesite' => $sameSite
            ]);

            http_response_code(200);
            echo json_encode([
                'status' => 200,
                'access_token' => $tokens['access_token'],
            ]);
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['status' => 401, 'message' => 'Invalid or expired refresh token']);
        }
    }

}