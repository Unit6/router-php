<?php
/*
 * This file is part of the Router package.
 *
 * (c) Unit6 <team@unit6websites.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Unit6\HTTP\Client;
use Unit6\HTTP\Headers;

/**
 * Test Router
 *
 * Check for correct operation of the standard features.
 */
class RouterTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function testRouterResponse()
    {
        $headers = new Headers();
        $headers->set('Content-Type', 'application/json');
        $headers->set('Accept', 'application/json');
        $headers->set('X-PHP-Version', phpversion());

        $options = [];

        $uri = ENDPOINT . '/about/?foo=bar';

        $request = Client\Request::get($uri, $headers);

        $response = $request->send();

        $responseBody = $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());

        $contents = $responseBody->getContents();
        $data = json_decode($contents, true);

        $this->assertEquals('GET', $data['request_method']);
        $this->assertEquals('/about/', $data['uri']);
        $this->assertEquals('about', $data['name']);
        $this->assertEquals('pages/about', $data['target']);
        $this->assertEquals(['foo' => 'bar'], $data['input']);
    }
}