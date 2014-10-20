<?php
/**
 * This file is part of Vegas package
 *
 * @author Tomasz Borodziuk <tomasz.borodziuk@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Social\Facebook;

use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
use Facebook\GraphUser;
use Phalcon\DI;
use Vegas\Security\OAuth;

/**
 * Class Service
 * @package Vegas\Social\Facebook
 */
class Service
{
    /**
     * @var bool
     */
    protected $fbSession = false;
    /**
     * @var array
     */
    protected $fbScope = array();

    /**
     * @param $config
     * @throws \Vegas\Social\Exception
     *
     * param example:
     *
     * $config = array(
     *  'app_key' => '704865089606542',
     *  'app_secret' => '786207332b78fb7819d375d480c1c3cd',
     *  'access_token' => 'CAAKBEjFH044BAIIz2eFnS2sVZA0prvVhUA99rPZC8nP32Xtw6T595YuoDXcjgszFSWPNXLzzvtD7sba6FqX373KoOxIbRZAZCP4ZBD8gBZB6jIRvVvLAsqApQLYMxugmQPYlRfxdWBZA3SIYzqBPflIOEvAhll7ybSfOJtK7c7SZCe9zZAui0DmSuLrtC5gjiUqsZD'
     * );
     *
     */
    public function __construct($config)
    {
        $this->connect($config['access_token'], $config['app_key'], $config['app_secret']);
    }

    /**
     * @param $accessToken
     * @param $appId
     * @param $appSecret
     * @throws \Vegas\Social\Exception
     */
    protected function connect($accessToken, $appId, $appSecret)
    {
        $session = new FacebookSession($accessToken);
        $session->setDefaultApplication($appId, $appSecret);

        if ($session->getToken() == $accessToken) {
            $this->fbSession = $session;
            $this->fbScope = $session->getSessionInfo()->getScopes();

            $this->checkPermissions();

            return $this;
        }

        $this->fbSession = false;
        throw new \Vegas\Social\Exception("could not set Facebook session");
    }

    /**
     * @param $method
     * @param $area
     * @param null $params
     * @return bool|\Facebook\FacebookResponse
     * @throws \Vegas\Social\Exception
     */
    protected function request($method, $area, $params = null)
    {
        $response = false;
        if ($this->fbSession != false) {
            try {
                if ($method == 'POST') {
                    $response = (new FacebookRequest(
                        $this->fbSession, $method, $area, $params
                    ))->execute();
                }
                if ($method == 'DELETE' || $method == 'GET') {
                    $response = (new FacebookRequest(
                        $this->fbSession, $method, $area
                    ))->execute();
                }
            } catch (FacebookRequestException $e) {
                throw new \Vegas\Social\Exception($e->getMessage(), $e->getCode());
            }
        } else {
            throw new \Vegas\Social\Exception('Not valid session!');
        }

        return $response;
    }

    /**
     * @return bool
     * @throws \Vegas\Social\Exception
     */
    protected function checkPermissions()
    {
        $requiredPermissions = array('email', 'user_friends', 'publish_actions');

        if (is_array($this->fbScope)) {
            foreach ($requiredPermissions as $permission) {
                if (!in_array($permission, $this->fbScope)) {
                    throw new \Vegas\Social\Exception("Required facebook permission " . $permission . " was not granted for this app!");
                }
            }
        }

        return true;
    }

    /**
     * @param string $userId
     * @return mixed
     * @throws \Vegas\Social\Exception
     */
    public function getUserData($userId = 'me')
    {
        try {
            $user_profile = $this->request('GET', '/' . $userId)->getGraphObject(GraphUser::className());
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception('GraphObject exception');
        }

        return $user_profile;
    }

    /**
     * @param $userId
     * @return bool
     * @throws \Vegas\Social\Exception
     */
    public function validateUser($userId)
    {
        if ($this->getUserData($userId)->getId() == $userId) {
            if ($userId == $this->getUserData($userId)->getId()) return true;
        }
        return false;
    }
}