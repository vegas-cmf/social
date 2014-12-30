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
    protected $fbScope = [];

    /**
     * @param array $config
     * @throws \Vegas\Social\Exception
     *
     * param example:
     *
     * $config = [
     *  'app_key' => 'APP ID',
     *  'app_secret' => 'APP SECRET',
     *  'access_token' => 'USER TOKEN'
     * ];
     *
     */
    public function __construct($config)
    {
        $this->connect($config['access_token'], $config['app_key'], $config['app_secret']);
    }

    /**
     * @param string $accessToken
     * @param string $appId
     * @param string $appSecret
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
        throw new \Vegas\Social\Exception\InvalidSessionException();
    }

    /**
     * @param string $method
     * @param string $area
     * @param array|null $params
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
                throw new \Vegas\Social\Exception\UnexpectedResponseException($e->getMessage());
            }
        } else {
            throw new \Vegas\Social\Exception\InvalidSessionException();
        }

        return $response;
    }

    /**
     * @return bool
     * @throws \Vegas\Social\Exception
     */
    protected function checkPermissions()
    {
        $requiredPermissions = ['email', 'user_friends', 'publish_actions'];

        if (is_array($this->fbScope)) {
            foreach ($requiredPermissions as $permission) {
                if (!in_array($permission, $this->fbScope)) {
                    throw new \Vegas\Social\Exception\NotGrantedPermissionException($permission);
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
            $userProfile = $this->request('GET', '/' . $userId)->getGraphObject(GraphUser::className());
        } catch (FacebookRequestException $e) {
            throw new \Vegas\Social\Exception\UnexpectedResponseException($e);
        }

        return $userProfile;
    }

    /**
     * @param string $userId
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
