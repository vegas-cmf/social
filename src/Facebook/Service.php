<?php
/**
 * Created by PhpStorm.
 * User: tborodziuk
 * Date: 24.09.14
 * Time: 12:58
 */

namespace Vegas\Social\Facebook;

use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
use Facebook\GraphUser;
use Phalcon\DI;
use Vegas\Security\OAuth;

class Service
{
    protected $_fbsession = false;
    protected $_fbscope = array();

    public function __construct($config)
    {
        $this->connect($config['access_token'], $config['app_key'], $config['app_secret']);
    }

    protected function connect($access_token, $app_id, $app_secret)
    {
        $session = new FacebookSession($access_token);
        $session->setDefaultApplication($app_id, $app_secret);

        if ($session->getToken() == $access_token) {
            $this->_fbsession = $session;
            $this->_fbscope = $session->getSessionInfo()->getScopes();

            $this->checkPermissions();

            return $this;
        }

        $this->_fbsession = false;
        throw new \Vegas\Social\Exception("SE0", "could not set Facebook session");
    }

    protected function request($method, $area, $params = null)
    {
        $response = false;
        if ($this->_fbsession != false) {
            try {
                if ($method == 'POST') {
                    $response = (new FacebookRequest(
                        $this->_fbsession, $method, $area, $params
                    ))->execute();
                }
                if ($method == 'DELETE' || $method == 'GET') {
                    $response = (new FacebookRequest(
                        $this->_fbsession, $method, $area
                    ))->execute();
                }
            } catch (FacebookRequestException $e) {
                throw new \Vegas\Social\Exception($e->getCode(), $e->getMessage());
            }
        } else {
            throw new \Vegas\Social\Exception('SE1', 'Not valid session!');
        }

        return $response;
    }

    protected function checkPermissions()
    {
        $required_permissions = array('email', 'user_friends', 'publish_actions');

        if (is_array($this->_fbscope)) foreach ($required_permissions as $permission) {
            if (!in_array($permission, $this->_fbscope)) {
                throw new \Vegas\Social\Exception("SE10", "Required facebook permission " . $permission
                    . " was not granted for this app!");
            }
        }

        return true;
    }

    public function getUserData($userId = 'me')
    {
        try {
            $user_profile = $this->request('GET', '/' . $userId)->getGraphObject(GraphUser::className());
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception('SE7', 'GraphObject exception');
        }

        return $user_profile;
    }

    public function validateUser($userId)
    {
        if ($this->getUserData($userId)->getId() == $userId) {
            echo gettype($this->getUserData($userId));
            return true;
        }
        false;
    }
}