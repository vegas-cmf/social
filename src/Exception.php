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

    /**
     * @param string $message
     * @param int $code
     */
    public function __construct($message, $code)
    {
        $this->message = $this->message.': '.$message.' ['.$code.']';
    }
}