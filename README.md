# Trampoline [![Build Status](https://travis-ci.org/functional-php/trampoline.svg)](https://travis-ci.org/functional-php/trampoline)

Trampolines are a technique used to avoid blowing the call stack when doing recursive calls. This is needed because PHP does not perform tail-call optimization.

For more information about what is tail-call optimization (or TCO), you can read : http://stackoverflow.com/questions/310974/what-is-tail-call-optimization#answer-310980

For a more in depth definition of trampolines and recursion as a whole, I can recommend you read http://blog.moertel.com/posts/2013-06-12-recursion-to-iteration-4-trampolines.html which is using Python but should be easy enough to understand.

## Installation

    composer require functional-php/trampoline

## Basic Usage

If we have the following recursive function:

```php
<?php

function factorial($n, $acc = 1) {
    return $n <= 1 ? $acc : factorial($n - 1, $n * $acc);
};

echo factorial(5);
// 120

```

We need to simply replace the recursive call by a call to the `bounce` static method:

``` php
<?php

use PHPFunctional\Trampoline\Trampoline;

function factorial($n, $acc = 1) {
    return $n <= 1 ? $acc : Trampoline::bounce('factorial', $n - 1, $n * $acc);
};

echo Trampoline::run('factorial', 5);
// 120

```

The `bounce` and `run` method accepts anything that is deemed a valid [`callable`](http://php.net/manual/en/language.types.callable.php) by PHP.

The `run` method will also accept an instance of `Trampoline` created by `bounce` but will ignore any arguments in this case.

## Helpers

You can also call statically any function from the global namespace on the `Trampoline` class if you prefer this style:

``` php
<?php

use PHPFunctional\Trampoline\Trampoline;

echo Trampoline::factorial(5);
// 120

echo Trampoline::strtoupper('Hello!');
// HELLO!

```

This will however not work for functions inside a namespace.

## Testing

You can run the test suite for the library using:

    composer test
    
A rest report will be available in the `reports` directory.

## Contributing

There shouldn't be much to contribute except the potential bug but any kind of contribution are welcomed! Do not hesitate to open an issue or submit a pull request.