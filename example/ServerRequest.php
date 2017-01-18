<?php
/*
 * This file is part of the Router package.
 *
 * (c) Unit6 <team@unit6websites.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require realpath(dirname(__FILE__) . '/../autoload.php');
require realpath(dirname(__FILE__) . '/../vendor/autoload.php');

use Unit6\Router;

$router = new Router\Dispatcher();

$router->map( '/about', 'pages/about', [ 'name' => 'about', 'methods' => 'GET' ] );

$route = $router->match();

if ( ! $route) {
    throw new UnexpectedValueException('Route not found');
};

$response = [];
$response['uri'] = sprintf('%s', $route->getURI());
$response['name'] = $route->getName();
$response['target'] = $route->getTarget();
$response['input'] = $router->getInput();

foreach ($_SERVER as $key => $value) $response[strtolower($key)] = $value;

echo json_encode($response);
