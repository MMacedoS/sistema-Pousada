<?php
namespace App\Config;        

use App\Http\CorsHelper;

CorsHelper::handle([
    'https://localhost:5173',
    'https://sistemareserva.localhost:5173',
]);

use App\Http\Request\Request;

class Router {
    protected $routers = [];    
    protected $auth = null; 
    public $userLogado;

    public function create(string $method, string $path, callable $callback, ?Auth $auth) {
        $normalizedPath = $this->normalizePath($path);
        $this->routers[$method][$normalizedPath] = [
            'callback' => $callback,
            'auth' => $auth
        ];
    }

    public function init() {
        $httpMethod = $_SERVER["REQUEST_METHOD"];
        $requestUri = $_SERVER["REQUEST_URI"];
        $request = new Request();

        $normalizedRequestUri = $this->normalizePath($requestUri);

        // Verifica se a rota existe
        foreach ($this->routers[$httpMethod] as $path => $route) {
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $path);
            $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';

            if (preg_match($pattern, $normalizedRequestUri, $matches)) {
                array_shift($matches); // Remove o caminho completo
                $params = $matches;

                $token = $request->getAuthorization();

                // Verifica autenticação
                if (!is_null($route['auth']) && !$route['auth']->isValidToken($token)) {
                    http_response_code(401);
                    echo json_encode([
                        'status' => 401,
                        'message' => 'Unauthorized'
                    ]);
                    return;
                }

                // Executa o callback da rota
                return call_user_func_array($route['callback'], array_merge([$request], $params));
            }
        }            

    }

    private function normalizePath($path) {
        return rtrim(parse_url($path, PHP_URL_PATH), '/');
    }

    public function view(string $viewName, array $data = []) {
        extract($data);
        require_once __DIR__ . '/../Resources/Views/' . $viewName . '.php';
        exit();
    }

    public function redirect($page = '', $delay = 0) {
        $url = $_ENV['URL_PREFIX_APP'] . '/' . $page;
        if ($delay > 0) {
            echo "<meta http-equiv='refresh' content='{$delay};url={$url}'>";
            exit();
        } 
        
        header("Location: $url");
        exit();
    }

    public function userLogged()
    {
        $session = new Session();
        return $session->get('user');
    }

    public function permissionUserLogged()
    {
        $session = new Session();
        return $session->get('my_permissions');
    }

    public function fileUserLogged()
    {
        $session = new Session();
        return $session->get('files');
    }
}