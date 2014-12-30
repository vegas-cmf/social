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
use Phalcon\Validation\Validator\Url;
use Phalcon\Validation;

/**
 * Trait PublishHelper
 * @package Vegas\Social
 */
trait PublishHelper
{
    /**
     * @param string $string
     * @return bool
     */
    protected function validateLink($string)
    {
        return (new Validation)
            ->add('link', new Url)
            ->validate(['link' => $string])
            ->count() === 0;
    }
}
