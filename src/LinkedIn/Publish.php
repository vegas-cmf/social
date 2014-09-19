<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Social\LinkedIn;

use Phalcon\DI;
use Vegas\DI\InjectionAwareTrait;
use Vegas\Security\OAuth;

class Publish
{
    public function publish($value) {

/*$linkedin['consumer_key']    = '77yxzhmmzdj0xg';
$linkedin['consumer_secret'] = 'WgSxkb1I8yovu0T1';

try {
    $oauthc = new OAuth($linkedin['consumer_key'],
        $linkedin['consumer_secret'],
        OAUTH_SIG_METHOD_HMACSHA1,
        OAUTH_AUTH_TYPE_AUTHORIZATION);

    $oauthc->enableDebug();
    $oauthc->setNonce(rand());

    // Add a new job
    $url = 'https://api.linkedin.com/v1/jobs';
    $body = file_get_contents('./job.xml');
    $method = OAUTH_HTTP_METHOD_POST;
    $headers = array('Content-Type' => 'text/xml');

    $data = $oauthc->fetch($url, $body, $method, $headers);

    $response = $oauthc->getLastResponse();
    $response_info = $oauthc->getLastResponseInfo();

    print "<pre>";
    print_r($oauthc->debugInfo);
    print_r($response);
    print_r($response_info);
    print "</pre>";

    // Delete a job
    // Get the URL returned by Location header. Or use partner-job-id from XML
    $url = 'https://api.linkedin.com/v1/jobs/XXXXXX';
    $body = false;
    $method = OAUTH_HTTP_METHOD_DELETE;
    $data = $oauthc->fetch($url, $body, $method);

    $response = $oauthc->getLastResponse();
    $response_info = $oauthc->getLastResponseInfo();

    print "<pre>";
    print_r($oauthc->debugInfo);
    print_r($response);
    print_r($response_info);
    print "</pre>";



} catch(OAuthException $e) {
    print_r($oauthc->debugInfo);
    print_r($e);
}
*/

        return $value;
    }
}