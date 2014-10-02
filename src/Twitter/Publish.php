<?php
/**
 * Created by PhpStorm.
 * User: tborodziuk
 * Date: 01.10.14
 * Time: 13:30
 */

namespace Vegas\Social\Twitter;

use Vegas\Social\PublishInterface;

class Publish extends Service implements PublishInterface
{
    public function postOnWall($params = array())
    {
        if (is_string($params)) $message = $params;
        else $message = 'test message ' . rand();

        if (!is_array($params) || count($params) == 0) $params = array(
            'status' => $message
        );

        $code = $this->user_request(array(
            'method' => 'POST',
            'url' => $this->url('1.1/statuses/update'),
            'params' => $params
        ));

        if ($code == 200) {
            $response = json_decode($this->response['response'], true);
            return $response['id'];
        } else throw new \Vegas\Social\Exception("Twitter error: " . $code, '');
    }

    public function postPhoto($curl_file, $message)
    {
        $params = array(
            'media[]' => $curl_file,
            'status' => $message
        );

        $code = $this->user_request(array(
            'method' => 'POST',
            'url' => $this->url("1.1/statuses/update_with_media"),
            'params' => $params,
            'multipart' => true
        ));

        if ($code == 200) {
            $response = json_decode($this->response['response'], true);
            return $response['id'];
        } else throw new \Vegas\Social\Exception("Twitter error: " . $code, '');
    }

    public function deletePost($id)
    {
        $code = $this->user_request(array(
            'method' => 'POST',
            'url' => $this->url("1.1/statuses/destroy/" . $id),
            'params' => array()
        ));

        if ($code == 200) {
            $response = json_decode($this->response['response'], true);
            return $response['id'];
        } else throw new \Vegas\Social\Exception("Twitter error: " . $code, '');
    }
}

?>