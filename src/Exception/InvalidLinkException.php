<?php
/**
 * Created by PhpStorm.
 * User: tborodziuk
 * Date: 20.10.14
 * Time: 13:11
 */

namespace Vegas\Social\Exception;

class InvalidLinkException extends \Vegas\Social\Exception
{
    protected $message = 'Link `%s` is not valid';

    public function __construct($link)
    {
        $this->message = sprintf($this->message, $link);
    }
} 