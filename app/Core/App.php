<?php
/**
 * Application principale - Gère le routage
 */
class App
{
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        // Initialiser la base de données
        Database::init();
    }

    public function run()
    {
        $url = $this->parseUrl();
        
        // Routes définies
        $routes = [
            '' => ['HomeController', 'index'],
            'home' => ['HomeController', 'index'],
            'projects' => ['ProjectController', 'index'],
            'project' => ['ProjectController', 'show'],
            'blog' => ['BlogController', 'index'],
            'resume' => ['ResumeController', 'index'],
            'resume/download' => ['ResumeController', 'download'],
            'tools' => ['ToolsController', 'index'],
            'api/projects' => ['ApiController', 'projects'],
            'api/socials' => ['ApiController', 'socials'],
        ];

        $path = implode('/', $url);
        
        if (isset($routes[$path])) {
            $this->controller = $routes[$path][0];
            $this->method = $routes[$path][1];
        } elseif (isset($url[0]) && isset($routes[$url[0]])) {
            $this->controller = $routes[$url[0]][0];
            $this->method = $routes[$url[0]][1];
            array_shift($url);
            $this->params = $url;
        } else {
            // 404
            $this->controller = 'ErrorController';
            $this->method = 'notFound';
        }

        // Instancier le contrôleur
        $controllerInstance = new $this->controller();
        
        // Appeler la méthode avec les paramètres
        call_user_func_array([$controllerInstance, $this->method], $this->params);
    }

    protected function parseUrl()
    {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
