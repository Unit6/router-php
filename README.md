# unit6/router

Simple PSR-7 compliant HTTP Router.

## Example

A quick examples can be run on the command line. You'll need to run PHP's built-in server in one terminal and issue the request in the other.

Define routes:

```php
use Unit6\Router;

$router = new Router\Dispatcher();
$router->map( '/about', 'pages/about', [ 'name' => 'about', 'methods' => 'GET' ] );
$route = $router->match();

if ( ! $route) {
    throw new UnexpectedValueException('Route not found');
};

// $route->getURI();    # === '/about/'
// $route->getName();   # === 'about'
// $route->getTarget(); # === 'pages/about'
// $router->getInput(); # === ['foo' => 'bar']

```

Start the HTTP server:

```
$ php -S localhost:9000 example/ServerRequest.php
```

Issue the request:

```
$ php example/ClientRequest.php
```

## Requirements

Following required dependencies:

- PHP 5.6.x
- cURL 7.37.x

## License

This project is licensed under the MIT license -- see the `LICENSE.txt` for the full license details.

## Acknowledgements

Some inspiration has been taken from the following projects:

- [auraphp/Aura.Router](https://github.com/auraphp/Aura.Router)
- [dannyvankooten/AltoRouter](https://github.com/dannyvankooten/AltoRouter)
- [klein/klein.php](https://github.com/klein/klein.php)
- [mrjgreen/phroute](https://github.com/mrjgreen/phroute)
- [nikic/FastRoute](https://github.com/nikic/FastRoute)
- [slimphp/Slim](https://github.com/slimphp/Slim)