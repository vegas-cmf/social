<?php

namespace Vegas\Social\Twitter;

use tmhOAuth;

class Service extends tmhOAuth
{
    protected $config = array();

    public function __construct($config)
    {
        $this->config = $config;
        parent::__construct($this->config);
    }

    public function verifyCredentials()
    {
        $code = $this->user_request(array(
            'method' => 'GET',
            'url' => $this->url('1.1/account/verify_credentials')
        ));
        if ($code == 200) {
            return json_decode($this->response['response']);
        } else {
            throw new \Vegas\Social\Exception("Twitter error: " . $code, $this->response['errors']);
        }
    }
}