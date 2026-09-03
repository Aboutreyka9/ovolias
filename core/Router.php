<?php

class Router 
{
    private $routes = [];

    public function addRoute($path, $callback)
    {
        $this->routes[$path] = $callback;
    }

    public function run($url)
    {
        foreach ($this->routes as $path => $callback) {
            // Transformer les paramètres d'URL : /{param} devient (?:/([^/]+))?
            $pattern = preg_replace('/\/\{([a-zA-Z0-9_]+)\}/', '(?:/([^/]+))?', $path);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches); // Supprimer l'URL complète capturée

                // Lazy loading : Instanciation dynamique du contrôleur uniquement au moment du match
                if (is_array($callback) && count($callback) === 2 && is_string($callback[0])) {
                    $controllerClass = $callback[0];
                    $methodName = $callback[1];
                    if (class_exists($controllerClass)) {
                        $controllerInstance = new $controllerClass();
                        return call_user_func_array([$controllerInstance, $methodName], $matches ?: []);
                    }
                } elseif (is_string($callback) && strpos($callback, '@') !== false) {
                    list($controllerClass, $methodName) = explode('@', $callback);
                    if (class_exists($controllerClass)) {
                        $controllerInstance = new $controllerClass();
                        return call_user_func_array([$controllerInstance, $methodName], $matches ?: []);
                    }
                }

                return call_user_func_array($callback, $matches ?: []);
            }
        }

        // Si aucune route ne correspond, afficher la page d'erreur 404 complète
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            if (!headers_sent()) {
                http_response_code(404);
                header('Content-Type: application/json; charset=utf-8');
            }
            echo json_encode(['status' => 0, 'message' => 'Ressource ou route introuvable (404)']);
            exit;
        }

        $message = "La page que vous essayez de consulter n'existe pas ou l'adresse URL est incorrecte.";
        require_once __DIR__ . '/../views/errors/404.php';
        exit;
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}
