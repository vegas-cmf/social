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
 * Class NotGrantedPermissionException
 * @package Vegas\Social\Exception
 */
class NotGrantedPermissionException extends \Vegas\Social\Exception
{
    /**
     * @var string
     */
    protected $message = 'Required permission `%s` was not granted for this app';

    /**
     * @param string $link
     */
    public function __construct($perm)
    {
        $this->message = sprintf($this->message, $perm);
    }
} 