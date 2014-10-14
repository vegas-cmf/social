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
use Vegas\DI\InjectionAwareTrait;
use Vegas\Security\OAuth;

class Service
{
    protected $_fbsession = false;
    protected $_fbscope = array();

    public function __construct($access_token, $app_id, $app_secret)
    {
        $this->connect($access_token, $app_id, $app_secret);
        $this->setDefaultMessage();
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

    public function refreshToken()
    {
        /*    $params = array(
                'grant_type'        =>  'fb_exchange_token',
                'fb_exchange_token' =>  $accessToken,
                'client_id'         =>  $appid,
                'client_secret'     =>  $appsecret
            );



            $fb = new FacebookRequest();
            $dynamicResult = $fb->get("oauth/acess_token", new {
                client_id         = "app_id",
                client_secret     = "app_secret",
                grant_type        = "fb_exchange_token",
                fb_exchange_token = "EXISTING_ACCESS_TOKEN"
            });

            var_dump();


            $token = curl('https://graph.facebook.com/oauth/access_token?client_id='.$this->app_id.'&client_secret='.
                $app_secret.'&grant_type=fb_exchange_token&fb_exchange_token='.$this->_fbsession->getAccessToken());
        }*/

        /*private function testOauth() {
            $_SERVER['REQUEST_URI'] = '/login';
            $di = DI::getDefault();
            $oauth = new OAuth($di);
            $service = $oauth->obtainServiceInstance('facebook');
        }*/
    }

    public function curlRequest($url)
    {

        $c = curl_init($url);

        curl_setopt($c, CURLOPT_HTTPGET, true);
        curl_setopt($c, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($c, CURLOPT_CAINFO, FALSE);

        $output = curl_exec($c);

        if ($output === false) {
            curl_close($c);
            return false;
        }

        curl_close($c);
        return $output;
    }
}