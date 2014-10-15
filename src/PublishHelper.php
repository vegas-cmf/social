<?php

namespace Vegas\Social;

class PublishHelper
{
    public function validateLink($string)
    {

        $pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
        preg_match($pattern, $string, $matches);

        if (count($matches) == 1 && $matches[0] == $string) return true;

        return false;
    }
}

?>