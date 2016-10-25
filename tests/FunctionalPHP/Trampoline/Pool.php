<?php

namespace FunctionalPHP\Trampoline\tests\units;

use atoum;
use FunctionalPHP\Trampoline\Pool as P;

function test_function_for_queue($n, $acc = 1)
{
    return $n <= 1 ? $acc : $this($n - 1, $n * $acc);
}

class Pool extends atoum
{
    public function testGet()
    {
        $pool = P::get(function() { return 'hello'; });

        $this->object($pool)->isInstanceOf('FunctionalPHP\Trampoline\Pool');
        $this->string($pool())->isEqualTo('hello');
    }

    public function testGetWithArgs()
    {
        $pool = P::get(function($text) { return $text; });

        $this->string($pool('hello'))->isEqualTo('hello');
    }

    public function testRecursiveCall()
    {
        $fact = P::get(function($n, $acc = 1) {
            /** @var P $this */
            return $n <= 1 ? $acc : $this($n - 1, $n * $acc);
        });


        $this->integer($fact(5))->isEqualTo(120);
    }

    public function testNonClosure()
    {
        if(method_exists('\Closure','fromCallable')) {
            $fact = P::get('FunctionalPHP\Trampoline\tests\units\test_function_for_queue');
            $this->integer($fact(5))->isEqualTo(120);
        } else {
            $this->exception(function() {
                P::get('FunctionalPHP\Trampoline\tests\units\test_function_for_queue');
            })->isInstanceOf('\RuntimeException')
              ->message->contains('PHP >= 7.1');
        }
    }
}

