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

namespace Vegas\Social\LinkedIn;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\CurlClient;
use OAuth\OAuth2\Token\StdOAuth2Token;
use Vegas\Security\OAuth\Storage\Session;

/**
 * Class Service
 * @package Vegas\Social\LinkedIn
 */
class Service
{
    /**
     * @var \OAuth\Common\Service\ServiceInterface
     */
    protected $service;

    /**
     * @param $config
     */
    public function __construct($config)
    {
        $serviceFactory = new \OAuth\ServiceFactory();

        $serviceFactory->setHttpClient(new CurlClient());

        $token = new StdOAuth2Token();
        $token->setAccessToken($config['access_token']);

        $storage = new Session();
        $storage->storeAccessToken('linkedin', $token);

        $redirectUrl = '';
        if (isset($_SERVER['HTTP_HOST'])) $redirectUrl = $_SERVER['HTTP_HOST'];

        $credentials = new Credentials(
            $config['app_key'],
            $config['app_secret'],
            $redirectUrl
        );

        $this->service = $serviceFactory->createService('linkedin', $credentials, $storage, array('r_fullprofile', 'r_emailaddress', 'rw_nus'));
    }

    /**
     * @return mixed
     */
    public function getUserData()
    {
        $result = json_decode($this->service->request('/people/~?format=json'), true);

        return $result;
    }
}

?>