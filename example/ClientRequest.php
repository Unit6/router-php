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

use Unit6\HTTP\Client;
use Unit6\HTTP\Headers;

$headers = new Headers();
$headers->set('Content-Type', 'application/json');
$headers->set('Accept', 'application/json');
$headers->set('X-PHP-Version', phpversion());

$options = [];

$uri = 'http://localhost:9000/about/?foo=bar';


$request = Client\Request::get($uri, $headers);

try {
    $response = $request->send();
} catch (UnexpectedValueException $e) {
    var_dump($e->getMessage()); exit;
}

$responseBody = $response->getBody();

echo 'Status Code: ' . $response->getStatusCode() . PHP_EOL;
echo 'Reason Phrase: ' . $response->getReasonPhrase() . PHP_EOL;
echo 'Contents: ' . PHP_EOL . $responseBody->getContents() . PHP_EOL;
