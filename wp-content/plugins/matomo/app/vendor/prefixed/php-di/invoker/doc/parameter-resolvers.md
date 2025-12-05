# Parameter resolvers

Extending the behavior of the `Invoker` is easy and is done by implementing a [`ParameterResolver`](https://github.com/PHP-DI/Invoker/blob/master/src/ParameterResolver/ParameterResolver.php):

```php
interface ParameterResolver
{
    public function getParameters(
        ReflectionFunctionAbstract $reflection,
        array $providedParameters,
        array $resolvedParameters
    );
}
```

- `$providedParameters` contains the parameters provided by the user when calling `$invoker->call($callable, $parameters)`
- `$resolvedParameters` contains parameters that have already been resolved by other parameter resolvers

An `Invoker` can chain multiple parameter resolvers to mix behaviors, e.g. you can mix "named parameters" support with "dependency injection" support. This is why a `ParameterResolver` should skip parameters that are already resolved in `$resolvedParameters`.

Here is an implementation example for dumb dependency injection that creates a new instance of the classes type-hinted:

```php
class MyParameterResolver implements ParameterResolver
{
    public function getParameters(
        ReflectionFunctionAbstract $reflection,
        array $providedParameters,
        array $resolvedParameters
    ) {
        foreach ($reflection->getParameters() as $index => $parameter) {
            if (array_key_exists($index, $resolvedParameters)) {
                // Skip already resolved parameters
                continue;
            }

            $class = $parameter->getClass();

            if ($class) {
                $resolvedParameters[$index] = $class->newInstance();
            }
        }

        return $resolvedParameters;
    }
}
```

To use it:

```php
$invoker = new Invoker\Invoker(new MyParameterResolver);

$invoker->call(function (ArticleManager $articleManager) {
    $articleManager->publishArticle('Hello world', 'This is the article content.');
});
```

A new instance of `ArticleManager` will be created by our parameter resolver.

## Chaining parameter resolvers

The fun starts to happen when we want to add support for many things:

- named parameters
- dependency injection for type-hinted parameters
- ...

This is where we should use the [`ResolverChain`](https://github.com/PHP-DI/Invoker/blob/master/src/ParameterResolver/ResolverChain.php). This resolver implements the [Chain of responsibility](http://en.wikipedia.org/wiki/Chain-of-responsibility_pattern) design pattern.

For example the default chain is:

```php
$parameterResolver = new ResolverChain([
    new NumericArrayResolver,
    new AssociativeArrayResolver,
    new DefaultValueResolver,
]);
```

It allows to support even the weirdest use cases like:

```php
$parameters = [];

// First parameter will receive "Welcome"
$parameters[] = 'Welcome';

// Parameter named "content" will receive "Hello world!"
$parameters['content'] = 'Hello world!';

// $published is not defined so it will use its default value
$invoker->call(function ($title, $content, $published = true) {
    // ...
}, $parameters);
```

We can put our custom parameter resolver in the list and created a super-duper invoker that also supports basic dependency injection:

```php
$parameterResolver = new ResolverChain([
    new MyParameterResolver, // Our resolver is at the top for highest priority
    new NumericArrayResolver,
    new AssociativeArrayResolver,
    new DefaultValueResolver,
]);

$invoker = new Invoker\Invoker($parameterResolver);
```
