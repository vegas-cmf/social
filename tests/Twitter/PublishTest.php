<?php

namespace Vegas\Social\Tests\Twitter;

use Vegas\Social\Twitter\Publish;

class PublishTest extends \PHPUnit_Framework_TestCase
{
    private $config = [
        'consumer_key' => 'CPX9dOKA3Nga2VskRPcAWWIqr',
        'consumer_secret' => 'SWHIYIqpGfDnM03EFOBkkD26QT3xMIzwqOTwXFyAYZzP44ZFmK',
        'token' => '2831990905-dCAoGfdhYEX60GAG7xsOdIblMdfVerdfF9OoEz7',
        'secret' => '5WoQo1JtO8H61cOUvVPQbs6tTXZSTVB2ukGTou7yXkZOc',
    ];

    //test profile: https://twitter.com/amstdtests

    public function test()
    {
        $twitter = new Publish($this->config);

        //verify credentials
        $userData = $twitter->verifyCredentials();
        $this->assertEquals(2831990905, $userData->id);

        //set tweet text
        $twitter->setMessage("Test post! " . rand());

        //tweet default test post
        $postId = $twitter->post();

        //delete post
        $this->assertEquals($postId, $twitter->deletePost($postId));

        //set photo post
        $curlFile = new \Vegas\Social\CurlFile(dirname(__DIR__) . '/test_picture.png', 'image/png', 'Test message ' . rand());
        $twitter->setPhoto($curlFile)->setTitle("Photo test" . rand())->setMessage("...")->setLink("http://amsterdamstandard.com");

        //get and set params
        $postParams = $twitter->getPostParams();
        $postParamsAfterSet = $twitter->setPostParams($postParams)->getPostParams();
        $this->assertEquals($postParams, $postParamsAfterSet);

        //post custom tweet
        $postId = $twitter->post();

        //delete photo post
        $this->assertEquals($postId, $twitter->deletePost($postId));
    }

    public function test2()
    {
        $twitter = new Publish($this->config);

        //add photo post using photo url
        $twitter->setPhoto('http://www.toughzebra.com/wp-content/uploads/Amsterdam.Standard.Logo_.png', 'image/png')->setMessage("Photo url test" . rand());
        $postId = $twitter->post();

        //delete photo post
        $this->assertEquals($postId, $twitter->deletePost($postId));
    }

    public function testExceptions()
    {
        $publishObject = new Publish($this->config);

        //SET PHOTO - EXCEPTION TEST
        try {
            $publishObject->setPhoto('1234');
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception\InvalidArgumentException', $ex);
        }

        //SET LINK - EXCEPTION TEST
        try {
            $publishObject->setLink('1234');
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception\InvalidLinkException', $ex);
        }

        //POST PHOTO that does not exist - EXCEPTION TEST
        try {
            $publishObject->setPhoto('http://www.fake.com/does_not_exist.png');
            $publishObject->post();
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception', $ex);
        }

        //wrong POST PARAMS - EXCEPTION TEST
        try {
            $publishObject->setPostParams(['fake' => 'fake']);
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception\InvalidPostParamsException', $ex);
        }
    }
}
