<?php

namespace FunctionalPHP\Trampoline\tests\units;

use atoum;
use FunctionalPHP\Trampoline\Trampoline as T;

function test_function_for_trampoline()
{
    return 'hello';
}

class Trampoline extends atoum
{
    public function testBounce()
    {
        $bounce = T::bounce(function() { return 'hello'; });

        $this->object($bounce)->isInstanceOf('FunctionalPHP\Trampoline\Trampoline');
        $this->string($bounce())->isEqualTo('hello');
    }

    public function testBounceWithArgs()
    {
        $bounce = T::bounce(function($text) { return $text; }, 'hello');

        $this->string($bounce())->isEqualTo('hello');
    }

    public function testRunWithCallable()
    {
        $this->string(T::run(function () { return 'hello'; }))
             ->isEqualTo('hello');
    }

    public function testRunWithCallableAndArgs()
    {
        $this->string(T::run(function($text) { return $text; }, 'hello'))
             ->isEqualTo('hello');
    }

    public function testRunWithTrampoline()
    {
        $bounce = T::bounce(function() { return 'hello'; });

        $this->string(T::run($bounce))->isEqualTo('hello');
    }

    public function testRunWithTrampolineAndArgs()
    {
        $bounce = T::bounce(function($text) { return $text; }, 'hello');

        $this->string(T::run($bounce))->isEqualTo('hello');
    }

    public function testRunException()
    {
        $this->exception(function() { T::run('hello'); })
             ->hasMessage('Expected a callable or an instance of Trampoline.')
             ->isInstanceOf('\RuntimeException');
    }

    public function testStatic()
    {
        $this->string(T::strtoupper('hello'))->isEqualTo('HELLO');
    }

    public function testRecursiveCall()
    {
        $fact = function($n, $acc = 1) use(&$fact) {
            return $n <= 1 ? $acc : T::bounce($fact, $n - 1, $n * $acc);
        };

        $this->integer(T::run($fact, 5))->isEqualTo(120);
    }
}

