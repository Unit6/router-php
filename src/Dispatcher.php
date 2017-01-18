<?php
/*
 * This file is part of the Router package.
 *
 * (c) Unit6 <team@unit6websites.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unit6\Router;

use InvalidArgumentException;
use UnexpectedValueException;

use Unit6\HTTP\Environment;
use Unit6\HTTP\Request;

/**
 * Dispatcher Class
 *
 * Register routes by mapping URIs. Executing the dispatcher my running match.
 */
class Dispatcher implements DispatcherInterface
{
    /**
     * Supported HTTP Methods
     *
     * @var array
     */
    public static $methods = ['GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'PATCH', 'OPTIONS', 'TRACE', 'CONNECT'];

    /**
     * Unnamed Routes
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Named Routes
     *
     * @var array
     */
    protected $routesNamed = [];

    /**
     * PSR-7 Request Object
     *
     * @var Request
     */
    protected $request;

    /**
     * Assigned Route
     *
     * @var Route
     */
    protected $route;

    /**
     * Create new Router
     *
     * @param Request|null $request Context of PSR-7 Request object.
     */
    public function __construct(Request $request = null)
    {
        $this->request = ($request ? $request : Environment::getRequest());
    }

    /**
     * Execute the Dispatcher
     *
     * Process the registered routes.
     *
     * @return Route|null
     */
    public function __invoke()
    {
        return $this->match();
    }

    /**
     * Set Route Map
     *
     * Assign a new route.
     *
     * @param string $uri     Request URI
     * @param string $target  Request Target
     * @param array  $options Request Options
     *
     * @return void
     */
    public function map($uri, $target = '', array $options = [])
    {
        $route = new Route($uri, $target, $options);

        if ($name = $route->getName()) {
            $this->routesNamed[$name] = $route;
        }

        $this->routes[] = $route;
    }

    /**
     * Get HTTP Request Object
     *
     * PSR-7 Request
     *
     * @return Unit6\HTTP\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get Route
     *
     * Returns the assigned route.
     *
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Dispatch Router
     *
     * Matches the current request against mapped routes
     *
     * @return Route|null
     */
    public function match()
    {
        $this->route = null;

        $request = $this->getRequest();
        $method = $request->getMethod();
        $path = $request->getURI()->getPath();

        foreach ($this->routes as $route) {
            // compare server request method with route's allowed HTTP methods
            if ( ! in_array($method, $route->getMethods())) {
                continue;
            }

            // check if request url matches route regex. if not, return FALSE.
            if ( ! preg_match($route->getPattern(), $path, $matches)) {
                continue;
            }

            $route->setParameters($matches);

            $this->route = $route;

            return $route;
        }
    }

    /**
     * Set Parameters in URI
     *
     * Subsitute placeholders in URI.
     *
     * @param string $uri    URL path.
     * @param array  $params URI parameters.
     *
     * @return string
     */
    private function setParameters($uri, array $params = [])
    {
        // replace route url with given parameters
        if ($params && preg_match_all('/:(\w+)/', $uri, $keys)) {
            // grab array with matches
            $keys = $keys[1];
            // loop trough parameter names, store matching value in $params array
            foreach ($keys as $i => $key) {
                if (isset($params[$key])) {
                    $uri = preg_replace('/:(\w+)/', $params[$key], $uri, 1);
                }
            }
        }

        return $uri;
    }

    /**
     * Reverse route a named route
     *
     * @param string $route_name The name of the route to reverse route.
     * @param array $params Optional array of parameters to use in URL
     *
     * @return string The url to the route
     */
    public function uri($routeName, array $params = [])
    {
        // Check if route exists
        if ( ! isset($this->named_routes[$routeName])) {
            throw new InvalidArgumentException(sprintf('Invalid route name: %s', $routeName));
        }

        $route = $this->routesNamed[$routeName];

        $uri = $this->setParameters($route->getURI(), $params);

        return $uri;
    }

    /**
     * Get Request Input
     *
     * Convenience method to return coalesced parameters found in the request.
     * Query string parameters will be override by parameters in the body.
     *
     * @return array
     */
    public function getInput()
    {
        $input = [];

        $request = $this->getRequest();

        // Check the query string for parameters.
        parse_str($request->getURI()->getQuery(), $input);

        // Check the request body for input.
        $body = $request->getBody();
        if ($body->getSize()) {
            $input = array_merge($input, $request->getParsedBody());
        }

        // Check the assigned route for additional parameters.
        $route = $this->getRoute();
        if ($route instanceof Route) {
            $input = array_merge($input, $this->getRoute()->getParameters());
        }

        return $input;
    }
}