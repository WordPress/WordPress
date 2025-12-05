# Invoker

Generic and extensible callable invoker.

[![Build Status](https://img.shields.io/travis/PHP-DI/Invoker.svg?style=flat-square)](https://travis-ci.org/PHP-DI/Invoker)
[![Coverage Status](https://img.shields.io/coveralls/PHP-DI/Invoker/master.svg?style=flat-square)](https://coveralls.io/r/PHP-DI/Invoker?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/PHP-DI/Invoker.svg?style=flat-square)](https://scrutinizer-ci.com/g/PHP-DI/Invoker/?branch=master)
[![Latest Version](https://img.shields.io/github/release/PHP-DI/invoker.svg?style=flat-square)](https://packagist.org/packages/PHP-DI/invoker)

## Why?

Who doesn't need an over-engineered `call_user_func()`?

### Named parameters

Does this [Silex](http://silex.sensiolabs.org) example look familiar:

```php
$app->get('/project/{project}/issue/{issue}', function ($project, $issue) {
    // ...
});
```

Or this command defined with [Silly](https://github.com/mnapoli/silly#usage):

```php
$app->command('greet [name] [--yell]', function ($name, $yell) {
    // ...
});
```

Same pattern in [Slim](http://www.slimframework.com):

```php
$app->get('/hello/:name', function ($name) {
    // ...
});
```

You get the point. These frameworks invoke the controller/command/handler using something akin to named parameters: whatever the order of the parameters, they are matched by their name.

**This library allows to invoke callables with named parameters in a generic and extensible way.**

### Dependency injection

Anyone familiar with AngularJS is familiar with how dependency injection is performed:

```js
angular.controller('MyController', ['dep1', 'dep2', function(dep1, dep2) {
    // ...
}]);
```

In PHP we find this pattern again in some frameworks and DI containers with partial to full support. For example in Silex you can type-hint the application to get it injected, but it only works with `Silex\Application`:

```php
$app->get('/hello/{name}', function (Silex\Application $app, $name) {
    // ...
});
```

In Silly, it only works with `OutputInterface` to inject the application output:

```php
$app->command('greet [name]', function ($name, OutputInterface $output) {
    // ...
});
```

[PHP-DI](http://php-di.org/doc/container.html) provides a way to invoke a callable and resolve all dependencies from the container using type-hints:

```php
$container->call(function (Logger $logger, EntityManager $em) {
    // ...
});
```

**This library provides clear extension points to let frameworks implement any kind of dependency injection support they want.**

### TL/DR

In short, this library is meant to be a base building block for calling a function with named parameters and/or dependency injection.

## Installation

```sh
$ composer require PHP-DI/invoker
```

## Usage

### Default behavior

By default the `Invoker` can call using named parameters:

```php
$invoker = new Invoker\Invoker;

$invoker->call(function () {
    echo 'Hello world!';
});

// Simple parameter array
$invoker->call(function ($name) {
    echo 'Hello ' . $name;
}, ['John']);

// Named parameters
$invoker->call(function ($name) {
    echo 'Hello ' . $name;
}, [
    'name' => 'John'
]);

// Use the default value
$invoker->call(function ($name = 'world') {
    echo 'Hello ' . $name;
});

// Invoke any PHP callable
$invoker->call(['MyClass', 'myStaticMethod']);

// Using Class::method syntax
$invoker->call('MyClass::myStaticMethod');
```

Dependency injection in parameters is supported but needs to be configured with your container. Read on or jump to [*Built-in support for dependency injection*](#built-in-support-for-dependency-injection) if you are impatient.

Additionally, callables can also be resolved from your container. Read on or jump to [*Resolving callables from a container*](#resolving-callables-from-a-container) if you are impatient.

### Parameter resolvers

Extending the behavior of the `Invoker` is easy and is done by implementing a [`ParameterResolver`](https://github.com/PHP-DI/Invoker/blob/master/src/ParameterResolver/ParameterResolver.php).

This is explained in details the [Parameter resolvers documentation](doc/parameter-resolvers.md).

#### Built-in support for dependency injection

Rather than have you re-implement support for dependency injection with different containers every time, this package ships with 2 optional resolvers:

- [`TypeHintContainerResolver`](https://github.com/PHP-DI/Invoker/blob/master/src/ParameterResolver/Container/TypeHintContainerResolver.php)

    This resolver will inject container entries by searching for the class name using the type-hint:

    ```php
    $invoker->call(function (Psr\Logger\LoggerInterface $logger) {
        // ...
    });
    ```

    In this example it will `->get('Psr\Logger\LoggerInterface')` from the container and inject it.

    This resolver is only useful if you store objects in your container using the class (or interface) name. Silex or Symfony for example store services under a custom name (e.g. `twig`, `db`, etc.) instead of the class name: in that case use the resolver shown below.

- [`ParameterNameContainerResolver`](https://github.com/PHP-DI/Invoker/blob/master/src/ParameterResolver/Container/ParameterNameContainerResolver.php)

    This resolver will inject container entries by searching for the name of the parameter:

    ```php
    $invoker->call(function ($twig) {
        // ...
    });
    ```

    In this example it will `->get('twig')` from the container and inject it.

These resolvers can work with any dependency injection container compliant with [PSR-11](http://www.php-fig.org/psr/psr-11/).

Setting up those resolvers is simple:

```php
// $container must be an instance of Psr\Container\ContainerInterface
$container = ...

$containerResolver = new TypeHintContainerResolver($container);
// or
$containerResolver = new ParameterNameContainerResolver($container);

$invoker = new Invoker\Invoker;
// Register it before all the other parameter resolvers
$invoker->getParameterResolver()->prependResolver($containerResolver);
```

You can also register both resolvers at the same time if you wish by prepending both. Implementing support for more tricky things is easy and up to you!

### Resolving callables from a container

The `Invoker` can be wired to your DI container to resolve the callables.

For example with an invokable class:

```php
class MyHandler
{
    public function __invoke()
    {
        // ...
    }
}

// By default this doesn't work: an instance of the class should be provided
$invoker->call('MyHandler');

// If we set up the container to use
$invoker = new Invoker\Invoker(null, $container);
// Now 'MyHandler' is resolved using the container!
$invoker->call('MyHandler');
```

The same works for a class method:

```php
class WelcomeController
{
    public function home()
    {
        // ...
    }
}

// By default this doesn't work: home() is not a static method
$invoker->call(['WelcomeController', 'home']);

// If we set up the container to use
$invoker = new Invoker\Invoker(null, $container);
// Now 'WelcomeController' is resolved using the container!
$invoker->call(['WelcomeController', 'home']);
// Alternatively we can use the Class::method syntax
$invoker->call('WelcomeController::home');
```

That feature can be used as the base building block for a framework's dispatcher.

Again, any [PSR-11](http://www.php-fig.org/psr/psr-11/) compliant container can be provided.
