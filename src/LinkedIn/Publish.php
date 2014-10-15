<?php

namespace Vegas\Social\LinkedIn;

use Vegas\Social\PublishHelper;
use Vegas\Social\PublishInterface;

class Publish extends Service implements PublishInterface
{
    private $post_params = array();

    public function __construct($config)
    {
        parent::__construct($config);
        $this->setDefaultPostParams();
    }

    public function post()
    {
        $extra_headers = array('Content-type' => 'application/json');
        $result = json_decode($this->service->request('/people/~/shares?format=json', 'POST', json_encode($this->post_params), $extra_headers), true);
        return $result;
    }

    public function setDefaultPostParams()
    {
        $post = array(
            'comment' => 'Test message' . rand(),
            'content' => array(
                'title' => 'Test' . rand(),
                'description' => 'Linkedin',
                'submitted-url' => 'http://amsterdamstandard.com?q=' . rand()
            ),
            'visibility' => array('code' => 'anyone')
        );

        $this->post_params = $post;

        return $this;
    }

    public function setTitle($string)
    {
        $this->post_params['content']['title'] = $string;

        return $this;
    }

    public function setMessage($string)
    {
        $this->post_params['content']['description'] = $string;

        return $this;
    }

    public function setLink($url)
    {
        if (!is_string($url) || !PublishHelper::validateLink($url)) {
            throw new \Vegas\Social\Exception("LinkedIn error", "setLink - url is not valid");
        }

        $this->post_params['content']['submitted-url'] = $url;

        return $this;
    }

    public function setPhoto($url)
    {
        if (!is_string($url)) {
            throw new \Vegas\Social\Exception("LinkedIn error", "setPhoto - argument is not a string");
        }

        if (!PublishHelper::validateLink($url)) {
            throw new \Vegas\Social\Exception("LinkedIn error", "setPhoto - url is not valid" . $url);
        }

        $this->post_params['content']['submitted-image-url'] = $url;

        return $this;
    }

    public function getPostParams()
    {
        return $this->post_params;
    }

    public function setPostParams($post_params)
    {
        //check required params
        if (!isset($post_params['content']) || !isset($post_params['content']['title']) || !isset($post_params['content']['submitted-url'])) {
            throw new \Vegas\Social\Exception("LinkedIn error", "setPostParams - required params content/title or content/submitted-url are not set");
        } else $this->setLink($post_params['content']['submitted-url']);

        $this->post_params = $post_params;

        return $this;
    }

    /*public function deletePost($post_id)
    {
        //2014.10.14 deleting shares is not supported in LinkedIn Share API
        return false;
    }*/
}