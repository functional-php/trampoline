<?php

namespace tests\units;

use atoum;
use FunctionalPHP\Trampoline as T;

class stdClass extends atoum
{
    public function testRecursiveCall()
    {
        $fact = function($n, $acc = 1) use(&$fact) {
            return $n <= 1 ? $acc : T\bounce($fact, $n - 1, $n * $acc);
        };

        $this->integer(T\trampoline($fact, 5))->isEqualTo(120);
    }
}
