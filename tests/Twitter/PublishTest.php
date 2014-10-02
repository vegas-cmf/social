<?php

namespace Vegas\Social\Tests\Twitter;

use Vegas\Social\Twitter\Publish;

class PublishTest extends \PHPUnit_Framework_TestCase
{
    private $config = array(
        'consumer_key' => 'CPX9dOKA3Nga2VskRPcAWWIqr',
        'consumer_secret' => 'SWHIYIqpGfDnM03EFOBkkD26QT3xMIzwqOTwXFyAYZzP44ZFmK',
        'token' => '2831990905-dCAoGfdhYEX60GAG7xsOdIblMdfVerdfF9OoEz7',
        'secret' => '5WoQo1JtO8H61cOUvVPQbs6tTXZSTVB2ukGTou7yXkZOc',
    );

    public function test()
    {
        $twitter = new Publish($this->config);

        $userData = $twitter->verifyCredentials();
        count($userData);


        //add text post
        $post_id = $twitter->postOnWall("Test post! " . rand());

        //delete post
        $this->assertEquals($post_id, $twitter->deletePost($post_id));

        //add photo post
        $curl_file = curl_file_create('test_picture.png', 'image/png', 'Test message ' . rand());
        $post_id = $twitter->postPhoto($curl_file, 'Test picture ' . rand());

        //delete photo post
        $this->assertEquals($post_id, $twitter->deletePost($post_id));
    }
}