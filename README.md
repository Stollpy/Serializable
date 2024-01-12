## Features

This php package implements a serialization feature. This serialization trait, 
named "Serializable", allows you to serialize any php object into an array or json. 
It also enables any object implementing this trait to become a serializer in its own right.

## Getting started

```bash
    composer require laradev/serializable
```

## Usage

Properties must be public or have an associated getter to be serialized.

### Basic Usage
```php
use Laraved\Serializable\Serializable;

class MyObject 
{
    use Serializable;
    
    public string $foo = "foo";
    public string $bar = "bar";
}

var_dump((new MyObject())->toArray());
// output
// ["foo" => "foo", "bar" => "bar"]

var_dump((new MyObject())->toJson());
// output
// "{"foo": "foo", "bar": "bar"}"
```
#

### Used with of getters
```php
use Laraved\Serializable\Serializable;

class MyObject 
{
    use Serializable;
    
    private string $foo = "foo";
    protected string $bar = "bar";
    
    public function getFoo(): string 
    {
        return $this->foo;
    }
}

var_dump((new MyObject())->toArray());
// output
// ["foo" => "foo"]

var_dump((new MyObject())->toJson());
// output
// "{"foo": "foo"}"
```

#

### Used with a whitelist and blacklist strategy
You can apply a whitelist or blacklist strategy to your serializer properties using a property or function.

#### With a property:

##### Whitelist 
```php
use Laraved\Serializable\Serializable;

class MyObject 
{
    use Serializable;
    
    private array $whitelist = [
        "foo"
    ];
    
    public string $foo = "foo";
    public string $bar = "bar";
}
var_dump((new MyObject())->toArray());
// output
// ["foo" => "foo"]

var_dump((new MyObject())->toJson());
// output
// "{"foo": "foo"}"
```

##### Blacklist
```php
use Laraved\Serializable\Serializable;

class MyObject 
{
    use Serializable;
    
    private array $blacklist = [
        "foo"
    ];
    
    public string $foo = "foo";
    public string $bar = "bar";
}
var_dump((new MyObject())->toArray());
// output
// ["bar" => "bar"]

var_dump((new MyObject())->toJson());
// output
// "{"bar": "bar"}"
```

#### With a function:

##### Whitelist
```php
use Laraved\Serializable\Serializable;

class MyObject 
{
    use Serializable;
    
    public string $foo = "foo";
    public string $bar = "bar";
    
    protected function whitelist(): array 
    {
        // your logic
        return [
            "foo"
        ];
    }
}
var_dump((new MyObject())->toArray());
// output
// ["foo" => "foo"]

var_dump((new MyObject())->toJson());
// output
// "{"foo": "foo"}"
```

##### Blacklist
```php
use Laraved\Serializable\Serializable;

class MyObject 
{
    use Serializable;
    
    public string $foo = "foo";
    public string $bar = "bar";
    
    protected function blacklist(): array 
    {
        // your logic
        return [
            "foo"
        ];
    }
}
var_dump((new MyObject())->toArray());
// output
// ["bar" => "bar"]

var_dump((new MyObject())->toJson());
// output
// "{"bar": "bar"}"
```

## Additional information

If you encounter a bug or have any ideas for improvement, don't hesitate to send me a PR 
or contact me via email at florian@laradev.ca :)
