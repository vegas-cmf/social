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
    protected $config = [];

    /**
     * @param array $config
     *
     * param example:
     *
     * $config = array(
     *   'consumer_key' => 'CONSUMER KEY',
     *   'consumer_secret' => 'CONSUMER SECRET',
     *   'token' => 'TOKEN',
     *   'secret' => 'SECRET',
     * );
     *
     */
    public function __construct($config)
    {
        $this->config = $config;
        parent::__construct($this->config);
    }

    /**
     * @throws \Vegas\Social\UnexpectedResponseException
     */
    public function verifyCredentials()
    {
        $code = $this->user_request([
            'method' => 'GET',
            'url' => $this->url('1.1/account/verify_credentials')
        ]);
        if ($code == 200) {
            return json_decode($this->response['response']);
        } else {
            throw new \Vegas\Social\UnexpectedResponseException($this->response['errors']);
        }
    }
}
