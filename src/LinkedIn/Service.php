<?php
namespace Vegas\Social\LinkedIn;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\CurlClient;
use OAuth\OAuth2\Token\StdOAuth2Token;
use Vegas\Security\OAuth\Storage\Session;

class Service
{
    protected $service;

    public function __construct($config)
    {
        $serviceFactory = new \OAuth\ServiceFactory();

        $serviceFactory->setHttpClient(new CurlClient());

        $token = new StdOAuth2Token();
        $token->setAccessToken($config['access_token']);

        $storage = new Session();
        $storage->storeAccessToken('linkedin', $token);

        $credentials = new Credentials(
            $config['app_key'],
            $config['app_secret'],
            'http://localhost/SLinkedIn-master/examples/request.php'
        );

        $this->service = $serviceFactory->createService('linkedin', $credentials, $storage, array('r_fullprofile', 'r_emailaddress', 'rw_nus'));
    }

    public function getUserData()
    {
        $result = json_decode($this->service->request('/people/~?format=json'), true);

        return $result;
    }
}

?>