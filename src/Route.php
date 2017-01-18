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

/**
 * Route Class
 *
 * Define a registered route.
 */
class Route implements RouteInterface
{
    /**
     * Route ID
     *
     * @var string
     */
    protected $id;

    /**
     * Route URI
     *
     * @var string
     */
    protected $uri;

    /**
     * Route Methods
     *
     * Restrict the methods this route will respond to.
     * The default is HTTP HEAD and GET.
     *
     * @var array
     */
    protected $methods = ['GET', 'HEAD'];

    /**
     * Route Target
     *
     * @var string
     */
    protected $target;

    /**
     * Route Name
     *
     * Used for reverse routing by name to get URL.
     *
     * @var string
     */
    protected $name;

    /**
     * Route Filters
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Route Parameters
     *
     * @var array
     */
    protected $params = [];

    /**
     * Route Attributes
     *
     * Additional attributes defined against
     * the route when originally mapped.
     *
     * @var array
     */
    protected $attr = [];

    /**
     * Create new route
     *
     * Define a new route with target and attributes.
     *
     * @param string $uri    Route URI.
     * @param string $target Route target.
     * @param array  $attr   Route attributes.
     */
    public function __construct($uri, $target = '', array $attr = [])
    {
        // make sure that the URL is suffixed with a forward slash
        if (substr($uri, -1) !== '/') {
            $uri .= '/';
        }

        $this->uri = $uri;
        $this->target = $target;

        if (isset($attr['methods'])) {
            if (is_string($attr['methods'])) {
                $attr['methods'] = explode(',', $attr['methods']);
            }

            if (count(array_diff($attr['methods'], Dispatcher::$methods)) !== 0) {
                throw new InvalidArgumentException(sprintf('Router map uses invalid HTTP methods: %s', implode(', ', $attr['methods'])));
            }

            $this->setMethods($attr['methods']);
        }

        if (isset($attr['name'])) {
            $this->name = $attr['name'];
        }

        if (isset($attr['filters'])) {
            $this->filters = $attr['filters'];
        }

        if (isset($attr['id'])) {
            $this->id = $attr['id'];
        }

        $this->attr = $attr;
    }

    /**
     * Get Route Identifier
     *
     * @return string
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * Get Route URI
     *
     * @return string
     */
    public function getURI()
    {
        return $this->uri;
    }

    /**
     * Get Route Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get Route Supported Methods
     *
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Set Methods for Route
     *
     * @param array $methods List of supported HTTP methods.
     *
     * @return void
     */
    public function setMethods(array $methods)
    {
        $this->methods = $methods;
    }

    /**
     * Get Route Target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * URI Pattern Substitute Filter
     *
     * @param array $matches
     *
     * @return string
     */
    private function substituteFilter(array $matches)
    {
        if (isset($matches[1], $this->filters[$matches[1]])) {
            return $this->filters[$matches[1]];
        }

        return '([\w-]+)';
    }

    /**
     * Get Route Pattern for URI
     *
     * @return string
     */
    public function getPattern()
    {
        $callback = [&$this, 'substituteFilter'];

        $pattern = preg_replace_callback('/:(\w+)/', $callback, $this->getURI());

        return '@^' . $pattern . '*$@i';
    }

    /**
     * Get Route Parameters
     *
     * List of route parameters in URI.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->params;
    }

    /**
     * Get Route Parameter
     *
     * Access specific route URI parameter by name.
     *
     * @param string $name Name of attribute.
     *
     * @return string|null
     */
    public function getParameter($name)
    {
        return (isset($this->params[$name]) ? $this->params[$name] : null);
    }

    /**
     * Get Route Attribute
     *
     * Access specific route attribute by name.
     *
     * @param string $name Name of attribute.
     *
     * @return array
     */
    public function getAttribute($name)
    {
        return (isset($this->attr[$name]) ? $this->attr[$name] : null);
    }

    /**
     * Set Route Parameters
     *
     * @param array $matches Detected values in URI
     *
     * @return void
     */
    public function setParameters(array $matches = [])
    {
        $uri = $this->getURI();

        $params = [];

        if (preg_match_all('/:([\w-]+)/', $uri, $keys)) {
            // grab array with matches
            $keys = $keys[1];
            // loop trough parameter names, store matching value in $params array
            foreach ($keys as $key => $name) {
                if (isset($matches[$key + 1])) {
                    $params[$name] = $matches[$key + 1];
                }
            }
        }

        $this->params = $params;
    }
}