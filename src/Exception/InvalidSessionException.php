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
 * Class InvalidSessionException
 * @package Vegas\Social\Exception
 */
class InvalidSessionException extends \Vegas\Social\Exception
{
    /**
     * @var string
     */
    protected $message = 'Session is not valid';

    /**
     * @param string $link
     */
    public function __construct()
    {
        $this->message = sprintf($this->message);
    }
} 