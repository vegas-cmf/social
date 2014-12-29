<?php
/**
 * This file is part of Vegas package
 *
 * @author Tomasz Borodziuk <tomasz.borodziuk@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Social;

/**
 * Class PublishHelper
 * @package Vegas\Social
 */
class PublishHelper
{
    /**
     * @param $string
     * @return bool
     */
    public function validateLink($string)
    {

        $pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
        preg_match($pattern, $string, $matches);

        if (count($matches) == 1 && $matches[0] == $string) {
            return true;
        }

        return false;
    }
}
