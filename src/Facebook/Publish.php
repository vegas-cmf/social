<?php
/**
 * Created by PhpStorm.
 * User: tborodziuk
 * Date: 19.09.14
 * Time: 12:09
 */

namespace Vegas\Social\Facebook;

use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
use Facebook\GraphUser;
use Phalcon\DI;
use Vegas\DI\InjectionAwareTrait;
use Vegas\Security\OAuth;

//facebook SDK4

//oauth

class Publish
{
    private $_fbsession = false;
    private $_post_params = array();
    private $_target_user = 'me';

    public function __construct($access_token, $app_id, $app_secret)
    {
        $this->connect($access_token, $app_id, $app_secret);
        $this->setDefaultMessage();
    }

    private function facebookRequest($method,$area) {
        $response = false;
        if ($this->_fbsession != false) {
            try {
                if($method=='POST') {
                    $response = (new FacebookRequest(
                        $this->_fbsession, $method, $area, $this->_post_params
                    ))->execute();
                }
                if($method=='DELETE' || $method=='GET') {
                    $response = (new FacebookRequest(
                        $this->_fbsession, $method, $area
                    ))->execute();
                }
                return $response;

            } catch (FacebookRequestException $e) {
                throw new \Vegas\Social\Exception($e->getCode(), $e->getMessage());
            }
        }
        else {
            throw new \Vegas\Social\Exception('SE1', 'Not valid session!');
        }
    }

    public function setTargetUser($userId='me') {
        if($this->getUserData($userId) != false) {
            $this->_target_user = $userId;
            return $this;
        }
        throw new \Vegas\Social\Exception('SE8', 'Target user not found!');
    }

    public function publishOnWall($post_params = array() , $targetUser='me')
    {
        $this->setTargetUser($targetUser);

        $post_id=false;
        if(count($post_params)>0) {
            $this->setPostParamsArray($post_params);
        }

        try {
            $post_id = $this->facebookRequest('POST','/'.$this->_target_user.'/feed')->getGraphObject()->getProperty('id');
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception('SE7', 'GraphObject exception');
        }

        return $post_id;
    }

    public function publishInPhotos($curl_file, $message)
    {
        try {
            $response = (new FacebookRequest(
                $this->_fbsession, 'POST', '/'.$this->_target_user.'/photos', array(
                    'source' => $curl_file,
                    'message' => $message
                )
            ))->execute()->getGraphObject()->getProperty('id');
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception($e->getCode(), $e->getMessage());
        }
        return $response;
    }

    public function deletePost($post_id)
    {
        if($post_id != '') {
            $this->facebookRequest('DELETE','/'.$post_id);
            return true;
        }
        throw new \Vegas\Social\Exception('SE6', 'Not valid post id!');
    }

    public function setDefaultMessage()
    {
        $userToken = $this->_fbsession->getToken();
        $userName = $this->getUserData()->getName();

        if ($userName) {
            $this->_post_params = array(
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

    public function setPostCaption($string)
    {
        $this->_post_params['caption'] = $string;
        return true;
    }

    public function setPostMessage($string)
    {
        $this->_post_params['message'] = $string;
        return true;
    }

    public function setPostLink($string)
    {
        if (validateLink($string)) {
            $this->_post_params['link'] = $string;
            return $this;
        }
        throw new \Vegas\Social\Exception('SE3', 'not valid link');
    }

    public function getPostParamsArray()
    {
        return $this->_post_params;
    }

    public function setPostParamsArray($array)
    {
        if (isset($array['link'])
            && ($this->validateLink($array['link'])
                && isset($array['message']))
        ) {
            $this->_post_params = $array;
            return $this;
        }
        return false;
        //throw new \Vegas\Social\Exception('SE9', 'not valid post params');
    }

    private function validateLink($string)
    {
        $pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
        preg_match($pattern, $string, $matches);
        if (count($matches) == 1 && $matches[0] == $string) return true;
        return false;
    }

    public function getUserData($userId = 'me')
    {
        try {
            $user_profile = $this->facebookRequest('GET','/'.$userId)->getGraphObject(GraphUser::className());
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception('SE7', 'GraphObject exception');
        }

        return $user_profile;
    }

    private function connect($access_token, $app_id, $app_secret)
    {
        $session = new FacebookSession($access_token);
        $session->setDefaultApplication($app_id, $app_secret);

        if ($session->getToken() == $access_token) {
            $this->_fbsession = $session;
            return true;
        }

        $this->_fbsession = false;
        throw new \Vegas\Social\Exception("SE0", "could not set Facebook session");
    }
}

?>