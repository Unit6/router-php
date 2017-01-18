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
 * Route Interface
 *
 * Define a contract for interacting with a route.
 */
interface RouteInterface
{
    /**
     * Create new route
     *
     * Define a new route with target and attributes.
     *
     * @param string $uri    Route URI.
     * @param string $target Route target.
     * @param array  $attr   Route attributes.
     */
    public function __construct($uri, $target = '', array $attr = []);

    /**
     * Get Route Identifier
     *
     * @return string
     */
    public function getID();

    /**
     * Get Route URI
     *
     * @return string
     */
    public function getURI();

    /**
     * Get Route Name
     *
     * @return string
     */
    public function getName();

    /**
     * Get Route Supported Methods
     *
     * @return array
     */
    public function getMethods();

    /**
     * Set Methods for Route
     *
     * @param array $methods List of supported HTTP methods.
     *
     * @return void
     */
    public function setMethods(array $methods);

    /**
     * Get Route Target
     *
     * @return string
     */
    public function getTarget();

    /**
     * Get Route Pattern for URI
     *
     * @return string
     */
    public function getPattern();

    /**
     * Get Route Parameters
     *
     * List of route parameters.
     *
     * @return array
     */
    public function getParameters();

    /**
     * Get Route Attribute
     *
     * List of route attributes.
     *
     * @return array
     */
    public function getAttribute($name);

    /**
     * Set Route Parameters
     *
     * @param array $matches Detected values in URI
     *
     * @return void
     */
    public function setParameters(array $matches = []);
}