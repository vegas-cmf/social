<?php

namespace Vegas\Social\Tests\Facebook;

use Vegas\Social\Facebook\Publish;

class PublishTest extends \PHPUnit_Framework_TestCase
{
    private $config = array(
        'app_key' => '704865089606542',
        'app_secret' => '786207332b78fb7819d375d480c1c3cd',
        'access_token' => 'CAAKBEjFH044BAAguBMog0rLxQrm1tx4xZBTqtUKIvLoQRl8OqkPmY78x30aPTG8GzAwVGbT4GqoBchmL6OZAEVeaEHK9FQsD0YS1FA5rwDykfzrs28pb8nOulmyZAdIZCLyarsA4WM5bbZBRdF9AQFb0jHdASUJeUDGpfsPuN2ZB3uJVaQqy6iHeZBZAbAluwArXEbozpsvt6ohkmNJ6WBxO'
    ); //the wall of this test profile: https://www.facebook.com/profile.php?id=100007822967538

    public function test()
    {

        $facebook = new Publish($this->config);

        //GETTING USER DATA
        $graph_object = $facebook->getUserData();
        $user_id = $graph_object->getId();

        //VALIDATE USER
        $this->assertEquals(true, $facebook->validateUser($user_id));

        //PUBLISHING ON WALL
        $post_id = $facebook->setMessage("Testing, testing..." . rand())->setTitle("Testing, testing..." . rand())->setLink("http://amsterdamstandard.com/")->post();
        //test post ID
        $this->assertEquals(true, gettype($post_id) == 'string');

        //DELETING POST
        $this->assertEquals($post_id, $facebook->deletePost($post_id));

        //ADDING CURL PICTURE WITH COMMENT TO PICTURES
        $curl_file = curl_file_create(dirname(__DIR__) . '/test_picture.png', 'image/png', 'test_name');
        $post_id = $facebook->setPhoto($curl_file)->post();

        //DELETING PICTURE POST
        $this->assertEquals($post_id, $facebook->deletePost($post_id));

        $facebook = new Publish($this->config);

        //ADDING PICTURE TO PHOTOS BY URL
        $facebook->setPhoto('http://www.toughzebra.com/wp-content/uploads/Amsterdam.Standard.Logo_.png');

        //SET POST TEXTS
        $facebook->setMessage("Testing, testing..." . rand())->setTitle("Testing, testing..." . rand())->setLink("http://amsterdamstandard.com/");

        //GET POST PARAMS
        $post_params = $facebook->getPostParams();
        $this->assertEquals('array', gettype($post_params));

        //SET POST PARAMS
        $facebook->setPostParams($post_params);
        $this->assertEquals('array', gettype($post_params));

        //POST
        $post_id = $facebook->post();

        //DELETING PICTURE POST
        $this->assertEquals($post_id, $facebook->deletePost($post_id));
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
            $facebook->setPostParams(array('fake' => 'fake'));
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception\InvalidPostParamsException', $ex);
        }
    }
}