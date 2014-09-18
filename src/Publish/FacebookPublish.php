<?php
namespace Vegas\Social\Publish;

use Phalcon\DI;
use Vegas\DI\InjectionAwareTrait;
use Vegas\Security\OAuth;

//facebook SDK4
use Facebook\HttpClients\FacebookHttpable;
use Facebook\HttpClients\FacebookCurl;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\Entities\AccessToken;
use Facebook\Entities\SignedRequest;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookPermissionException;
use Facebook\FacebookOtherException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphSessionInfo;

//oauth
use OAuth\OAuth2\Service\Facebook;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use OAuth\ServiceFactory;

class FacebookPublish
{
    private $_fbsession = false;

    public function __construct($access_token, $app_id, $app_sig) {
        $this->connect($access_token, $app_id, $app_sig);
    }


    public function publishOnWall() {
        $post_id = false;

        if ( $this->_fbsession != false ) {
            $session = $this->_fbsession;
            $request = new FacebookRequest( $session, 'GET', '/me' );
            $response = $request->execute();
            $graphObject = $response->getGraphObject()->asArray();
            echo $graphObject['updated_time'];
        }

        try {

            $post_params = array(
                'access_token' => $session->getToken(),
                'name' => 'Test name',
                'link' => 'http://amsterdamstandard.nl/',
                'caption' => 'Test caption',
                'message' => 'Test message',
            );

            $response = (new FacebookRequest(
                $session, 'POST', '/me/feed', $post_params
            ))->execute()->getGraphObject();

            $post_id =  $response->getProperty('id');

        } catch(FacebookRequestException $e) {

            //echo "Exception occured, code: " . $e->getCode();
            //echo " with message: " . $e->getMessage();
            return false;

        }

        return $post_id;
    }

    public function getUserData() {
        //user data are needed to get name for post
    }

    public function connect($access_token, $app_id, $app_secret) {

        $session = new FacebookSession($access_token);
        $session->setDefaultApplication($app_id, $app_secret);

        if($session->getToken() == $access_token) {
            $this->_fbsession = $session;
            return true;
        }

        $this->_fbsession = false;
        return false;
    }
}