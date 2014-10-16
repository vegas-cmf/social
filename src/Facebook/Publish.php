<?php
/**
 * Created by PhpStorm.
 * User: tborodziuk
 * Date: 19.09.14
 * Time: 12:09
 */

namespace Vegas\Social\Facebook;

use Vegas\Social\PublishHelper;
use Vegas\Social\PublishInterface;

class Publish extends Service implements PublishInterface
{
    private $post_params = array();
    private $publish_area;
    private $target_user;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->setDefaultPostParams();
    }

    public function setDefaultPostParams()
    {
        $userToken = $this->_fbsession->getToken();
        $userName = $this->getUserData()->getName();

        if ($userName) {
            $this->post_params = array(
                'access_token' => $userToken,
                'name' => $userName,
                'link' => 'http://amsterdamstandard.nl/',
                'caption' => 'Test caption',
                'message' => 'Test message',
            );

            $this->publish_area = 'feed';
            $this->target_user = 'me';

            return $this;
        }
        throw new \Vegas\Social\Exception('SE4', 'post_params error');
    }

    public function setTitle($string)
    {
        $this->post_params['caption'] = $string;
        return $this;
    }

    public function setMessage($string)
    {
        $this->post_params['message'] = $string;
        return $this;
    }

    public function setLink($string)
    {
        if (PublishHelper::validateLink($string)) {
            $this->post_params['link'] = $string;
            return $this;
        }
        throw new \Vegas\Social\Exception('SE3', 'not valid link');
    }

    public function setPhoto($photo)
    {
        $this->publish_area = 'photos';
        $message = $this->post_params['message'];
        $this->post_params = array(
            'message' => $message
        );

        if (gettype($photo) == 'object' && get_class($photo) == 'CURLFile') {
            $this->post_params['source'] = $photo;
        } else if (gettype($photo) == 'string' && PublishHelper::validateLink($photo)) {
            $this->post_params['url'] = $photo;
        } else {
            throw new \Vegas\Social\Exception('SE3', 'not valid argument in setPhoto');
        }

        return $this;
    }

    public function getPostParams()
    {
        return $this->post_params;
    }

    public function setPostParams($array)
    {
        if (isset($array['url'])
            && (PublishHelper::validateLink($array['url'])
                && isset($array['message']))
        ) {
            $this->post_params = $array;
            return $this;
        }
        throw new \Vegas\Social\Exception('SE9', 'not valid post params');
    }

    public function post()
    {
        $post_id = false;

        try {
            $post_id = $this->request('POST', '/' . $this->target_user . '/' . $this->publish_area, $this->post_params)->getGraphObject()->getProperty('id');
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception('SE7', 'GraphObject exception');
        }

        return $post_id;
    }

    public function deletePost($post_id)
    {
        try {
            $this->request('DELETE', '/' . $post_id);
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception('SE6', 'Could not delete post.');
        }

        return $post_id;
    }
}

?>