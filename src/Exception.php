<?php

namespace Vegas\Social;

use Vegas\Exception as VegasException;

/**
 * Class Exception
 * @package Vegas\Social
 */
class Exception extends VegasException
{
    /**
     * Exception default message
     */
    protected $message = 'Social exception';

    public function __construct($code, $message) {
        $this->message = $this->message.': '.$message.' ['.$code.']';
    }
}