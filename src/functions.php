<?php

namespace FunctionalPHP\Trampoline;

/**
 * This method should be used inside your recursive functions
 * when you need to make a tail recursive call.
 *
 * Instead of calling the function directly it will give back
 * the control to the trampoline which will in turn call your
 * function again in order to avoid a stack overflow.
 *
 * @param callable $f a tail recursive function
 * @param array ...$args arguments for the function
 * @return callable a callable for the Trampoline
 */
function bounce(callable $f, ...$args)
{
    return Trampoline::bounce($f, ...$args);
}

/**
 * Launch a trampoline. The given callable should be a tail
 * recursive function.
 *
 * All recursive calls inside the function should be made
 * using the bounce function so that the trampoline can
 * correctly avoid stack overflows.
 *
 * @param callable $f a tail recursive function
 * @param array ...$args arguments for the function
 * @return mixed The final result of your function
 */
function trampoline(callable $f, ...$args)
{
    return Trampoline::run($f, ...$args);
}

function trampoline_wrapper(callable $f)
{
    return function(...$args) use($f) {
        return Trampoline::run($f, ...$args);
    };
}

/**
 * Alternative method to get a tail recursive function
 * without risk of stack overflows.
 *
 * All recursive calls inside the function should be made
 * by using the `$this` directly as a function.
 *
 * @param callable $f a tail recursive function
 * @return callable equivalent function without stack overflow risk
 */
function pool(callable $f)
{
    return Pool::get($f);
}
