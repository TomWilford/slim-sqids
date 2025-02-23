# slim-sqids
This package helps you implement [Sqids](https://github.com/sqids/sqids-php) in [Slim](https://www.slimframework.com/). 
It provides a ~~sqiddleware~~ middleware that automatically decodes Sqids from URL parameters and a trait that adds a 
getter to return a Sqid-encoded value of a designated property.

## Install
Install via [Composer](https://getcomposer.org/):
```bash
composer require tomwilford/slim-sqids
```

## Usage
Once slim-sqids is configured (see the Configuration section below), you can use it in your Slim application as follows:
#### Routing / Middleware
1. **Register the Middleware:** 

   Add the slim-sqids middleware before the routing middleware. This ensures that any 
   route arguments containing "sqid" (case-insensitive) are decoded automatically.
```php
$app = new Slim\App();

// Register slim-sqids middleware
$app->add(new \TomWilford\SlimSqids\SqidsMiddleware());
$app->addRoutingMiddleware();
```
2. **Define Routes with Sqid Parameters:**

   For any route that uses a Sqid, include the string "sqid" in the argument 
name. For example:
```php
$app->get('/foos/{fooSqid}', \App\Action\Foo\Page\ShowAction::class);
```
In the above example, if a request is made to `/foos/UKkLWZg9DA`, the middleware decodes the parameter into `123`. Your 
controller then receives the decoded value:
```php
public function __invoke(
    Request $request,
    Response $response,
    array $arguments = []
) {
    // 'fooSqid' now holds the decoded value (e.g., 123)
    $id = $arguments['fooSqid'];

    $foo = $this->repository->ofId($id);
    // ...
}
```
#### Sqid Getter
To automatically add a `getSqid()` method to your class:
1. **Use the Trait:** 

   Compose your class with the `HasSqidablePropertyTrait`:
```php
class Foo
{
    use \TomWilford\SlimSqids\HasSqidablePropertyTrait;
    // ...
}
```
2. **Decorate the Property:**

   Use the PHP attribute `#[SqidableProperty]` to mark the property you want to encode. Note: if multiple properties 
   are decorated, only the first one will be used by `getSqid()`.
```php

class Foo
{
    use \TomWilford\SlimSqids\HasSqidablePropertyTrait;

    #[\TomWilford\SlimSqids\SqidableProperty]
    private int $id;
    // ...
}
```
Now, calling `getSqid()` on an instance of `Foo` returns the Sqid-encoded value of `$id`:
```php
class Foo {
    // ...

    public function jsonSerialize(): mixed
    {
        return [
            'id'  => $this->getSqid(), // returns the encoded value (e.g., UKkLWZg9DA)
            'bar' => $this->bar,
        ];
    }
}
```

## Configuration
There are two primary ways to configure slim-sqids, plus a hybrid approach if you wish to combine features.

#### Dependency Injection
If your Slim application uses a dependency injection container:
1. **Register a Sqids Instance:**

   Add a new instance of Sqids to your container. See the
[Sqids documentation](https://github.com/sqids/sqids-php?tab=readme-ov-file#-examples) for configuration examples.
```php
// Example uses php-di/php-di
Sqids::class => function (ContainerInterface $container) {
    return new \Sqids\Sqids(
        minLength: 10
    );
},
```
2. **Inject into Your Classes:**
   
   Include Sqids in the constructor of any class that needs to encode a property:
```php
class Foo 
{
    use \TomWilford\SlimSqids\HasSqidablePropertyTrait;
    
    public function __construct(\Sqids\Sqids $sqids)
    {
        $this->sqids = $sqids;
    }
}
```

#### Global Configuration Class
If you are not using a dependency injection container, you can set a global configuration. A good location for this 
is your application's `bootstrap.php` file.
1. **Set the Global Configuration:**
```php
\TomWilford\SlimSqids\GlobalSqidConfiguration::set(new \Sqids\Sqids(minLength: 10));
```

The global configuration is immutable - attempting to overwrite it will throw a `RuntimeException`. It will also throw an 
exception if accessed before being set.


#### Hybrid Approach
If you use a container but prefer not to inject Sqids directly into your classes (e.g., for models), you can combine 
the approaches:
1. **Register a Sqids Instance in Your Container:**
```php
// Example uses php-di/php-di
Sqids::class => function (ContainerInterface $container) {
    return new \Sqids\Sqids(
        minLength: 10
    );
},
```
2. **Set the Global Configuration Using the Container:**
```php
\TomWilford\SlimSqids\GlobalSqidConfiguration::set($container->get(\Sqids\Sqids::class));
```

## Testing
The package includes tests using `PHPUnit`, `PHPStan`, and `PHP_CodeSniffer`. You can run all tests at once or 
individually via Composer:
```bash
composer test:all
```
```bash
# Run PHPUnit tests
composer test

# Run PHPStan (static analysis)
composer stan

# Run PHP_CodeSniffer (coding standards check)
composer sniffer:check
```

## Contributing
Contributions are welcome!

## License
The [MIT License (MIT)](LICENSE).

