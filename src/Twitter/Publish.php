<?php
/**
 * Created by PhpStorm.
 * User: tborodziuk
 * Date: 01.10.14
 * Time: 13:30
 */

namespace Vegas\Social\Twitter;

use Vegas\Social\PublishHelper;
use Vegas\Social\PublishInterface;

class Publish extends Service implements PublishInterface
{
    private $post_params;
    private $post_title = '';
    private $post_message = '';
    private $post_link = '';

    public function __construct($config)
    {
        parent::__construct($config);
        $this->setDefaultPostParams();
    }

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

    public function setDefaultPostParams()
    {
        $this->post_params = array(
            'method' => 'POST',
            'url' => $this->url("1.1/statuses/update"),
            'params' => array(
                'status' => ''
            )
        );

        $this->setTitle('Test Tweet ' . rand());
        $this->setMessage('Test, test, test, test... ' . rand());

        return $this;
    }

    public function setTitle($string)
    {
        $this->post_title = $string;
        $this->updateStatus();

        return $this;
    }

    public function setMessage($string)
    {
        $this->post_message = $string;
        $this->updateStatus();

        return $this;
    }

    public function setLink($url)
    {
        if (!is_string($url) || !PublishHelper::validateLink($url)) {
            throw new \Vegas\Social\Exception("Twitter error", "setLink - url is not valid");
        }

        $this->post_link = $url;
        $this->updateStatus();

        return $this;
    }

    private function updateStatus()
    {
        if ($this->post_title != '') $this->post_params['params']['status'] = $this->post_title . "\n\n" . $this->post_message;
        else $this->post_params['params']['status'] = $this->post_message;

        if ($this->post_link != '') $this->post_params['params']['status'] = $this->post_params['params']['status'] . "\n" . $this->post_link;

        return $this;
    }

    public function setPhoto($photo)
    {
        $this->post_params['url'] = $this->url("1.1/statuses/update_with_media");
        $this->post_params['params']['media[]'] = $photo;
        $this->post_params['multipart'] = true;

        return $this;
    }

    public function getPostParams()
    {
        return $this->post_params;
    }

    public function setPostParams($array)
    {
        $this->post_params = $array;
        return $this;
    }

    public function post()
    {
        $code = $this->user_request($this->post_params);

        if ($code != 200) {
            throw new \Vegas\Social\Exception("Twitter error: " . $code, '');
        }

        $response = json_decode($this->response['response'], true);
        return $response['id'];
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