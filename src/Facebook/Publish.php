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

    public function __construct($config)
    {
        parent::__construct($config);
        $this->setDefaultPostParams();
    }

    public function postOnWall($post_params = array(), $targetUser = 'me')
    {
        $post_id = false;
        if (count($post_params) > 0) {
            $this->setPostParamsArray($post_params);
        }

        try {
            $post_id = $this->request('POST', '/' . $targetUser . '/feed', $this->post_params)->getGraphObject()->getProperty('id');
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception('SE7', 'GraphObject exception');
        }

        return $post_id;
    }

    public function postPhoto($curl_file, $message, $targetUser = 'me')
    {
        $post_id = false;

        $params = array(
            'source' => $curl_file,
            'message' => $message
        );

        try {
            $post_id = $this->request('POST', '/' . $targetUser . '/photos', $params)->getGraphObject()->getProperty('id');
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception($e->getCode(), $e->getMessage());
        }

        return $post_id;
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
            return true;
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

    public function setPhoto($url_string_or_curl_object)
    {
        // TODO: Implement setPhoto() method.
    }

    public function getPostParams()
    {
        return $this->post_params;
    }

    public function setPostParams($array)
    {
        if (isset($array['link'])
            && (PublishHelper::validateLink($array['link'])
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
            $post_id = $this->request('POST', '/' . $targetUser . '/feed', $this->post_params)->getGraphObject()->getProperty('id');
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception('SE7', 'GraphObject exception');
        }

        return $post_id;
    }

    public function deletePost($post_id)
    {
        if ($post_id != '') {
            $this->request('DELETE', '/' . $post_id);
            return true;
        }
        throw new \Vegas\Social\Exception('SE6', 'Not valid post id!');
    }
}

?>