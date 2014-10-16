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

    //test profile: https://twitter.com/amstdtests

    public function test()
    {
        $twitter = new Publish($this->config);

        //verify credentials
        $user_data = $twitter->verifyCredentials();
        $this->assertEquals(2831990905, $user_data->id);

        //set tweet text
        $twitter->setMessage("Test post! " . rand());

        //tweet default test post
        $post_id = $twitter->post();

        //delete post
        $this->assertEquals($post_id, $twitter->deletePost($post_id));

        //set photo post
        $curl_file = curl_file_create(dirname(__DIR__) . '/test_picture.png', 'image/png', 'Test message ' . rand());
        $twitter->setPhoto($curl_file)->setTitle("Photo test" . rand())->setMessage("...")->setLink("http://amsterdamstandard.com");
        $post_id = $twitter->post();

        //delete photo post
        $this->assertEquals($post_id, $twitter->deletePost($post_id));
    }

    public function test2()
    {
        $twitter = new Publish($this->config);

        //add photo post using photo url
        $twitter->setPhoto('http://www.toughzebra.com/wp-content/uploads/Amsterdam.Standard.Logo_.png', 'image/png')->setMessage("Photo url test" . rand());
        $post_id = $twitter->post();

        //delete photo post
        $this->assertEquals($post_id, $twitter->deletePost($post_id));
    }

    public function testExceptions()
    {
        $publishObject = new Publish($this->config);

        //SET PHOTO - EXCEPTION TEST
        try {
            $publishObject->setPhoto('1234');
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception', $ex);
        }

        //SET LINK - EXCEPTION TEST
        try {
            $publishObject->setLink('1234');
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception', $ex);
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
            $publishObject->setPostParams(array('fake' => 'fake'));
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception', $ex);
        }
    }
}