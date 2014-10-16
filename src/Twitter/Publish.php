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
    private $tmp_file = '';

    public function __construct($config)
    {
        parent::__construct($config);
        $this->setDefaultPostParams();
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
        if (gettype($photo) == 'object' && get_class($photo) == 'CURLFile') {
            $this->post_params['params']['media[]'] = $photo;
        } else if (gettype($photo) == 'string' && PublishHelper::validateLink($photo)) {
            $this->tmp_file = './image_tmp' . time();
            file_put_contents($this->tmp_file, file_get_contents($photo));
            $file_type = image_type_to_mime_type(exif_imagetype($this->tmp_file));
            $curl_file = curl_file_create($this->tmp_file, $file_type, $this->post_message);
            $this->post_params['params']['media[]'] = $curl_file;
        } else {
            throw new \Vegas\Social\Exception('Twitter error: ', 'not valid argument in setPhoto');
        }

        $this->post_params['url'] = $this->url("1.1/statuses/update_with_media");
        $this->post_params['multipart'] = true;

        return $this;
    }

    public function getPostParams()
    {
        return $this->post_params;
    }

    public function setPostParams($array)
    {
        if (!isset($array['method']) || $array['method'] != 'POST') throw new \Vegas\Social\Exception("Twitter error: ", 'method is wrong or ot set');
        if (!isset($array['params']['status'])) throw new \Vegas\Social\Exception("Twitter error: ", 'params.status is not set');

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
        try {
            $code = $this->user_request(array(
                'method' => 'POST',
                'url' => $this->url("1.1/statuses/destroy/" . $id),
                'params' => array()
            ));

            if ($code == 200) {
                $response = json_decode($this->response['response'], true);
                return $response['id'];
            }
        } catch (\Exception $ex) {
            throw new \Vegas\Social\Exception("Twitter error: ", var_export($ex, true));
        }
    }

    public function __destruct()
    {
        if ($this->tmp_file != '') unlink($this->tmp_file);
        $this->tmp_file = '';
    }
}

?>