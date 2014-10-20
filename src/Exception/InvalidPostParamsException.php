<?php
/**
 * Created by PhpStorm.
 * User: tborodziuk
 * Date: 20.10.14
 * Time: 13:11
 */

namespace Vegas\Social\Exception;

class InvalidPostParamsException extends \Vegas\Social\Exception
{
    protected $message = 'Param `%s` is not set';

    public function __construct($param)
    {
        $this->message = sprintf($this->message, $param);
    }
} 