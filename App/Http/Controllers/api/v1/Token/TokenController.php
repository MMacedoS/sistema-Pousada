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

        // Definir o refresh_token como cookie HTTP-only
        $secure = ($_ENV['APP_ENV'] === 'production');

        setcookie('refresh_token', $tokens['refresh_token'], [
            'expires' => time() + (60 * 60 * 24 * 7),
            'path' => '/',
            'secure' => $secure, // true em produção
            'httponly' => true,
            'samesite' => $secure ? 'None' : 'Lax', // Lax em dev, None em prod
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

            $accessToken = $this->generateTokens($user)['access_token'];

            http_response_code(200);
            echo json_encode([
                'status' => 200,
                'access_token' => $accessToken,
            ]);
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['status' => 401, 'message' => 'Invalid or expired refresh token']);
        }
    }
}