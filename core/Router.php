<?php

namespace Core;

class Router
{
    protected array $routes = [];

    public function add(string $method, string $uri, array $controller)
    {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => $method,
            'middleware' => null
        ];
    }

    public function get(string $uri, array $controller)
    {
        $this->add('GET', $uri, $controller);
    }

    public function post(string $uri, array $controller)
    {
        $this->add('POST', $uri, $controller);
    }

    public function dispatch(string $uri, string $method)
    {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
                
                // Aqui podemos adicionar a lógica de middleware (permissões) no futuro

                $controllerClass = $route['controller'][0];
                $controllerMethod = $route['controller'][1];

                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    if (method_exists($controllerInstance, $controllerMethod)) {
                        // Chama o método do controller
                        return $controllerInstance->$controllerMethod();
                    }
                }
            }
        }

        // Se nenhuma rota for encontrada, podemos mostrar uma página 404
        http_response_code(404);
        // Incluir uma view de 404 aqui
        echo "Página não encontrada.";
        exit();
    }
}
