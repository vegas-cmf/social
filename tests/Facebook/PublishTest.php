<?php

namespace Vegas\Social\Tests\Facebook;

use Vegas\Social\Facebook\Publish;

class PublishTest extends \PHPUnit_Framework_TestCase
{
    private $config = [
        'app_key' => '704865089606542',
        'app_secret' => '786207332b78fb7819d375d480c1c3cd',
        'access_token' => 'CAAKBEjFH044BAGuXpxmr60BwT3hIj8SzK5mcClG7oskNu2G9Hxt8ElJMaZA71UTDW9uaN6LsQhFkqccp9AZBihT6QwHPD80162cJhtP1py6LfhZCmH0QGMAjHnZA3yEoq7roqQZBoIHpZCe8G8b5lPPDAs7o4lrWSfdMimvruZBlMRVwoTpGcdLp6ZChI0QSqdon5SsE1zE0zEOlLZBfAJfW9'
    ]; //the wall of this test profile: https://www.facebook.com/profile.php?id=100007822967538

    public function test()
    {

        $facebook = new Publish($this->config);

        //GETTING USER DATA
        $graphObject = $facebook->getUserData();
        $userId = $graphObject->getId();

        //VALIDATE USER
        $this->assertEquals(true, $facebook->validateUser($userId));

        //PUBLISHING ON WALL
        $postId = $facebook->setMessage("Testing, testing..." . rand())->setTitle("Testing, testing..." . rand())->setLink("http://amsterdamstandard.com/")->post();
        //test post ID
        $this->assertInternalType('string', $postId);
        $this->assertNotEmpty($postId);

        //DELETING POST
        $this->assertEquals($postId, $facebook->deletePost($postId));

        //ADDING CURL PICTURE WITH COMMENT TO PICTURES
        $curlFile = new \Vegas\Social\CurlFile(dirname(__DIR__) . '/test_picture.png', 'image/png', 'test_name');
        $postId = $facebook->setPhoto($curlFile)->post();

        //DELETING PICTURE POST
        $this->assertEquals($postId, $facebook->deletePost($postId));

        $facebook = new Publish($this->config);

        //ADDING PICTURE TO PHOTOS BY URL
        $facebook->setPhoto('http://www.toughzebra.com/wp-content/uploads/Amsterdam.Standard.Logo_.png');

        //SET POST TEXTS
        $facebook->setMessage("Testing, testing..." . rand())->setTitle("Testing, testing..." . rand())->setLink("http://amsterdamstandard.com/");

        //GET POST PARAMS
        $postParams = $facebook->getPostParams();
        $this->assertInternalType('array', $postParams);

        //SET POST PARAMS
        $facebook->setPostParams($postParams);
        $this->assertInternalType('array', $postParams);

        //POST
        $postId = $facebook->post();

        //DELETING PICTURE POST
        $this->assertEquals($postId, $facebook->deletePost($postId));
    }

    public function testExceptions()
    {
        $facebook = new Publish($this->config);

        //DELETING POST - EXCEPTION TEST
        try {
            $facebook->deletePost('fake');
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception', $ex);
        }

        //SET PHOTO - EXCEPTION TEST
        try {
            $facebook->setPhoto('1234');
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception', $ex);
        }

        //SET LINK - EXCEPTION TEST
        try {
            $facebook->setLink('1234');
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception\InvalidLinkException', $ex);
        }

        //POST PHOTO that does not exist - EXCEPTION TEST
        try {
            $facebook->setPhoto('http://www.fake.com/does_not_exist.png');
            $facebook->post();
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception', $ex);
        }

        //wrong POST PARAMS - EXCEPTION TEST
        try {
            $facebook->setPostParams(['fake' => 'fake']);
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception\InvalidPostParamsException', $ex);
        }
    }
}
