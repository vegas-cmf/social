<?php
/**
 * Created by PhpStorm.
 * User: tborodziuk
 * Date: 24.09.14
 * Time: 12:23
 */

namespace Vegas\Social;


interface PublishInterface {
    public function postOnWall($post_params , $targetUser);
} 