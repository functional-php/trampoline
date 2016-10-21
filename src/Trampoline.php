<?php

namespace PHPFunctional\Trampoline;

class Trampoline
{
    /** @var  callable $f */
    private $f;

    /** @var  array $args */
    private $args;

    /**
     * @param callable $f
     * @param array $args
     */
    protected function __construct(callable $f, array $args = array())
    {
        $this->f = $f;
        $this->args = $args;
    }

    /**
     * Invoke the stored function with the stored arguments.
     *
     * @return mixed
     */
    public function __invoke()
    {
        return call_user_func_array($this->f, $this->args);
    }

    /**
     * Create a new trampoline instance for the given function and arguments.
     *
     * @param callable $f
     * @param array ...$args
     * @return static|callable
     */
    public static function bounce(callable $f, ...$args)
    {
        return new static($f, $args);
    }

    /**
     * Run a callable or a Trampoline until it gets to the final result
     * (ie: not a Trampoline instance)
     *
     * @param callable|Trampoline $f
     * @param array ...$args
     * @return mixed
     */
    public static function run($f, ...$args)
    {
        if($f instanceof self) {
            $return = $f;
        } else if(is_callable($f)) {
            $return = call_user_func_array($f, $args);
        } else  {
            throw new \RuntimeException("Expected a callable or an instance of Trampoline.");
        }

        while($return instanceof self) {
            $return = $return();
        }

        return $return;
    }

    /**
     * Helper function to easily run a callable as a Trampoline.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::run($name, ...$arguments);
    }
}