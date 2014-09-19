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

    public function __construct($access_token, $app_id, $app_secret)
    {
        $this->connect($access_token, $app_id, $app_secret);
        $this->setDefaultMessage();
    }


    public function publishOnWall($post_params = null)
    {
        $post_id = false;

        if ($post_params == null) $post_params = $this->_post_params;

        if ($this->_fbsession != false) {
            $session = $this->_fbsession;
            $request = new FacebookRequest($session, 'GET', '/me');
            $response = $request->execute();
            $graphObject = $response->getGraphObject()->asArray();
            echo $graphObject['updated_time'];
        }

        try {
            $response = (new FacebookRequest(
                $session, 'POST', '/me/feed', $post_params
            ))->execute()->getGraphObject();

            $post_id = $response->getProperty('id');

        } catch (FacebookRequestException $e) {
            //echo "Exception occured, code: " . $e->getCode();
            //echo " with message: " . $e->getMessage();
            return false;

        }

        return $post_id;
    }

    public function deletePost($post_id)
    {
        if ($post_id != '' && $this->_fbsession != false) {
            try {
                $response = (new FacebookRequest(
                    $this->_fbsession, 'DELETE', '/' . $post_id
                ))->execute()->getGraphObject();

                var_dump($response);
                return true;
            } catch (FacebookRequestException $e) {
                //echo "Exception occured, code: " . $e->getCode();
                //echo " with message: " . $e->getMessage();
                return false;
            }
        }
        return false;
    }

    public function setDefaultMessage()
    {
        $userToken = $this->_fbsession->getToken();
        $userName = $this->getUserData($userId = 'me')->getName();

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

        return false;
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
            return true;
        }
        return false;
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
            return true;
        }
        return false;
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
            $user_profile = (new FacebookRequest(
                $this->_fbsession, 'GET', '/' . $userId
            ))->execute()->getGraphObject(GraphUser::className());
        } catch (FacebookRequestException $e) {
            //echo "Exception occured, code: " . $e->getCode();
            //echo " with message: " . $e->getMessage();
            throw new \Vegas\Social\Exception($e->getMessage());
        }

        return $user_profile;
    }

    public function connect($access_token, $app_id, $app_secret)
    {
        $session = new FacebookSession($access_token);
        $session->setDefaultApplication($app_id, $app_secret);

        if ($session->getToken() == $access_token) {
            $this->_fbsession = $session;
            return true;
        }

        $this->_fbsession = false;
        throw new \Vegas\Social\Exception("could not set Facebook session");
    }
}

?>