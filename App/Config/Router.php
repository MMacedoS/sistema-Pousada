<?php

namespace App\Config;

use App\Http\Request\Request;

class Router {
    protected $routers = [];    
    protected $auth = null; 

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

        if (isset($this->routers[$httpMethod])) {
            foreach ($this->routers[$httpMethod] as $path => $route) {
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $path);
                $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';

                if (preg_match($pattern, $normalizedRequestUri, $matches)) {
                    // Verifica autenticação
                    if (!is_null($route['auth']) && !$route['auth']->check()) {
                        return $this->view('admin/login/login', ['message' => 'Deslogado', 'danger' => true]);
                    }

                    // Executa o callback
                    array_shift($matches); 
                    return call_user_func_array($route['callback'], array_merge([$request], $matches));
                }
            }
        }

        // Nenhuma rota encontrada
        http_response_code(404);
        $this->redirect('not-found');
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
}