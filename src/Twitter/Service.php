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

namespace Vegas\Social\Twitter;

use tmhOAuth;

/**
 * Class Service
 * @package Vegas\Social\Twitter
 */
class Service extends tmhOAuth
{
    /**
     * @var array|string
     */
    protected $config = array();

    /**
     * @param array $config
     *
     * param example:
     *
     * $config = array(
     *   'consumer_key' => 'XXX9dOKA3Nga2VskRPcAWWIqr',
     *   'consumer_secret' => 'XXXIYIqpGfDnM03EFOBkkD26QT3xMIzwqOTwXFyAYZzP44ZFmK',
     *   'token' => '2831990805-dCAoGfdhYEX60GAG7xsOdIblMdfVerdfF9OoEv8',
     *   'secret' => '5WoQo1JtO8H61cOUvVPQbs6tTXZSTVB2ukGTou7yXkZOx',
     * );
     */
    public function __construct($config)
    {
        $this->config = $config;
        parent::__construct($this->config);
    }

    /**
     * @throws \Vegas\Social\Exception
     */
    public function verifyCredentials()
    {
        $code = $this->user_request(array(
            'method' => 'GET',
            'url' => $this->url('1.1/account/verify_credentials')
        ));
        if ($code == 200) {
            return json_decode($this->response['response']);
        } else {
            throw new \Vegas\Social\Exception("Error: " . $code . " " . $this->response['errors'], 'SET0');
        }
    }
}