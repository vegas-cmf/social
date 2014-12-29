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

namespace Vegas\Social\Exception;

/**
 * Class InvalidLinkException
 * @package Vegas\Social\Exception
 */
class InvalidLinkException extends \Vegas\Social\Exception
{
    /**
     * @var string
     */
    protected $message = 'Link `%s` is not valid';

    /**
     * @param string $link
     */
    public function __construct($link)
    {
        $this->message = sprintf($this->message, $link);
    }
}
