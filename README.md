# Trampoline 

[![Build Status](https://travis-ci.org/functional-php/trampoline.svg)](https://travis-ci.org/functional-php/trampoline)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/functional-php/trampoline/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/functional-php/trampoline/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/functional-php/trampoline/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/functional-php/trampoline/?branch=master)
[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/functional-php/trampoline.svg)](http://isitmaintained.com/project/functional-php/trampoline "Average time to resolve an issue")
[![Percentage of issues still open](http://isitmaintained.com/badge/open/functional-php/trampoline.svg)](http://isitmaintained.com/project/functional-php/trampoline "Percentage of issues still open")
[![Chat on Gitter](https://img.shields.io/gitter/room/gitterHQ/gitter.svg)](https://gitter.im/functional-php)

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

We need to simply replace the recursive call by a call to the `bounce` function:

``` php
<?php

use FunctionalPHP\Trampoline as T;

function factorial($n, $acc = 1) {
    return $n <= 1 ? $acc : T\bounce('factorial', $n - 1, $n * $acc);
};

echo T\trampoline('factorial', 5);
// 120

```

The `bounce` and `trampoline` functions accepts anything that is deemed a valid [`callable`](http://php.net/manual/en/language.types.callable.php) by PHP.

The `trampoline` function will also accept an instance of `Trampoline` created by `bounce` but will ignore any arguments in this case.

## Helpers

You can also call statically any function from the global namespace on the `Trampoline` class if you prefer this style:

``` php
<?php

use FunctionalPHP\Trampoline\Trampoline;

echo Trampoline::factorial(5);
// 120

echo Trampoline::strtoupper('Hello!');
// HELLO!

```

This will however not work for functions inside a namespace.

If you want to have a ready to call function when using a trampoline, you can use the `trampoline_wrapper` helper. It will create a wrapper function that will call `trampoline` for you and return the result.

``` php
<?php

use FunctionalPHP\Trampoline as T;

function factorial($n, $acc = 1) {
    return $n <= 1 ? $acc : T\bounce('factorial', $n - 1, $n * $acc);
};

$fact = T\trampoline_wrapper('factorial');

echo $fact(5);
// 120

```

## Alternative method

The library also contain an alternative implementation to run tail recursive function without risking a stack overflow based on an argument queue instead of a trampoline.

You will need to use the `$this` variable as the recursive function. Here is the factorial example using this second method.

``` php
<?php

use FunctionalPHP\Trampoline as T;

$fact = T\pool(function($n, $acc = 1) {
    return $n <= 1 ? $acc : $this($n - 1, $n * $acc);
});

echo $fact(5);
// 120

```

At this time, only anonymous functions (ie instance of the `Closure` class) are supported. But as soon as PHP 7.1 is released you will be able to use any callable.

From a performance standpoint, there is no measurable difference between the two approaches.

## Testing

You can run the test suite for the library using:

    composer test
    
A test report will be available in the `reports` directory.

## Contributing

There shouldn't be much to contribute except the potential bug but any kind of contribution are welcomed! Do not hesitate to open an issue or submit a pull request.
