<?php

namespace FunctionalPHP\Trampoline;

class Pool
{
    /** @var  callable|\Closure $f */
    private $f;

    /** @var  array $arguments_pool */
    private $arguments_pool = [];

    /** @var bool currently recursing */
    private $recursing = false;

    /**
     * @param callable $f
     */
    protected function __construct(callable $f)
    {
        if($f instanceof \Closure) {
            $this->f = $f->bindTo($this);
        } elseif(method_exists('\Closure','fromCallable')) {
            $this->f = \Closure::fromCallable($f)->bindTo($this);
        } else {
            throw new \RuntimeException('Using anything else than a callable is only possible for PHP >= 7.1.');
        }
    }

    /**
     * Invoke the stored function with the stored arguments.
     *
     * @return mixed
     */
    public function __invoke()
    {
        $result = null;
        $this->arguments_pool[] = func_get_args();

        if($this->recursing === false) {
            $this->recursing = true;

            while(! empty($this->arguments_pool)) {
                $result = call_user_func_array($this->f, array_shift($this->arguments_pool));
            }

            $this->recursing = false;
        }

        return $result;
    }

    /**
     * @param callable $f
     * @return callable
     */
    public static function get(callable $f)
    {
        return new static($f);
    }
}
