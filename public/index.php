<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DebugBar\StandardDebugBar;
use FastRoute\Dispatcher;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\FileViewFinder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Events\Dispatcher as EventsDispatcher;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\EngineResolver;
use Symfony\Component\VarDumper\VarDumper;

// Enable Whoops
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

// Enable DebugBar
$debugbar = new StandardDebugBar();
$debugbarRenderer = $debugbar->getJavascriptRenderer();

$debugbar["messages"]->addMessage("Welcome to BR Framework!");

// Dump a variable and die
function dd()
{
  foreach (func_get_args() as $arg) {
    VarDumper::dump($arg);
  }
  exit();
}

// Configure view factory
$viewPaths = [__DIR__ . '/../src/Views'];
$finder = new FileViewFinder(new Filesystem, $viewPaths);

// Create an EngineResolver instance and add Blade engine to it
$engineResolver = new EngineResolver;
$bladeCompiler = new BladeCompiler(new Filesystem, __DIR__.'/../cache/views');
$engineResolver->register('blade', function () use ($bladeCompiler) {
    return new \Illuminate\View\Engines\CompilerEngine($bladeCompiler);
});

$viewFactory = new ViewFactory(
    $engineResolver,
    $finder,
    new EventsDispatcher
);

// Share $debugbarRenderer with all views
$viewFactory->share('debugbarRenderer', $debugbarRenderer);

// Add the Blade engine resolver to the ViewFactory
$viewFactory->addExtension('blade.php', 'blade');

// Load routes
$routes = require_once __DIR__ . '/../routes/web.php';

$dispatcher = FastRoute\simpleDispatcher($routes);

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// Dispatch the request
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// Handle the request
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 - Not Found';
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405 - Method Not Allowed';
        break;
    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        // Extract controller and method
        list($controllerClass, $method) = $handler;

        // Instantiate controller
        $controller = new $controllerClass($viewFactory);

        // convert associative array to indexed array
        //$vars = array_values($vars);

        // Call controller method
        //$response = call_user_func_array([$controller, $method], $vars);
        try {
            $response = $controller->$method($vars);
        } catch (\Exception $e) {
            $response = 'Method Error: '.$e->getMessage();
            dd($response);
        }
        //$response = $controller->$method($vars);

        // Check if the response is a view
        if ($response instanceof \Illuminate\Contracts\View\View) {
            echo $response->render();
        } else {
            // Check if the response is JSON
            if (is_array($response) || $response instanceof \JsonSerializable) {
                dump($response);
                // Set JSON content type header
                //header('Content-Type: application/json');
                // Output JSON encoded response
                echo json_encode($response);
            } else {
                dump("Unknow response type: " . gettype($response));
                // Handle other types of responses if needed
                dump($response);
                echo $response;
            }
            
        }
        break;
}

