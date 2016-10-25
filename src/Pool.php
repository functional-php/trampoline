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
     * @param \Closure $f
     */
    protected function __construct(\Closure $f)
    {
        $this->f = $f->bindTo($this);
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
     * @param \Closure $f
     * @return callable
     */
    public static function get(\Closure $f)
    {
        return new static($f);
    }
}
