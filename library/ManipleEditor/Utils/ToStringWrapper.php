<?php

class ManipleEditor_Utils_ToStringWrapper
{
    /**
     * @var Zefram_Stdlib_CallbackHandler
     */
    protected $_callback;

    /**
     * @param callable $callback
     */
    public function __construct($callback)
    {
        $this->_callback = new Zefram_Stdlib_CallbackHandler($callback);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        try {
            return (string) $this->_callback->call();
        } catch (Exception $e) {
            return (string) $e;
        }
    }
}
