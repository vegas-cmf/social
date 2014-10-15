<?php
/**
 * Created by PhpStorm.
 * User: tborodziuk
 * Date: 24.09.14
 * Time: 12:23
 */

namespace Vegas\Social;

interface PublishInterface
{
    public function setDefaultPostParams(); //executed also in __construct

    public function setTitle($string);

    public function setMessage($string);

    public function setLink($string);

    public function setPhoto($photo); //url or curl object

    public function getPostParams();

    public function setPostParams($array);

    public function post();
}