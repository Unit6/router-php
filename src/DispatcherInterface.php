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

/**
 * Dispatcher Class
 *
 * Register routes by mapping URIs. Executing the dispatcher my running match.
 */
interface DispatcherInterface
{
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
    public function map($uri, $target = '', array $options = []);

    /**
     * Get HTTP Request Object
     *
     * PSR-7 Request
     *
     * @return Unit6\HTTP\Request
     */
    public function getRequest();

    /**
     * Dispatch Router
     *
     * Matches the current request against mapped routes
     *
     * @return Route|null
     */
    public function match();

    /**
     * Reverse route a named route
     *
     * @param string $route_name The name of the route to reverse route.
     * @param array $params Optional array of parameters to use in URL
     *
     * @return string The url to the route
     */
    public function uri($routeName, array $params = []);

    /**
     * Get Request Input
     *
     * Convenience method to return coalesced parameters found in the request.
     * Query string parameters will be override by parameters in the body.
     *
     * @return array
     */
    public function getInput();
}