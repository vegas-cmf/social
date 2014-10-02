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
    public function postOnWall($params_array_or_message);

    public function postPhoto($curl_file, $message);

    public function deletePost($post_id);
}