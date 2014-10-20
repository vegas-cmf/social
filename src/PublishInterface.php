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
 * Interface PublishInterface
 * @package Vegas\Social
 */
interface PublishInterface
{
    /**
     * @return mixed
     */
    public function setDefaultPostParams(); //executed also in __construct

    /**
     * @param $string
     * @return mixed
     */
    public function setTitle($string);

    /**
     * @param $string
     * @return mixed
     */
    public function setMessage($string);

    /**
     * @param $string
     * @return mixed
     */
    public function setLink($string);

    /**
     * @param $photo
     * @return mixed
     */
    public function setPhoto($photo); //url or curl object

    /**
     * @return mixed
     */
    public function getPostParams();

    /**
     * @param $array
     * @return mixed
     */
    public function setPostParams($array);

    /**
     * @return mixed
     */
    public function post();
}