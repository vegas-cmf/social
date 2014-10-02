<?php

namespace Vegas\Social\Tests\Facebook;

use Vegas\Social\Facebook\Publish;

class PublishTest extends \PHPUnit_Framework_TestCase
{
    //FACEBOOK APP: Vegas CMF - social module test
    private $appId = '704865089606542';
    private $appSecret = '786207332b78fb7819d375d480c1c3cd';

    //TEST USER 1: sicdpmc_goldmanwitz_1411568513@tfbnw.net
    private $access_token1 = 'CAAKBEjFH044BAJlNIgmUetV6IRlj3gp7SUXoyUzlgI8ytKDks4vH7bDKPPShVaNHVCXhWFO5Tmn2vf2964aZAn0ptK2J7OeYoap8H6tQVQYZC4eExhJYkZARl54qZAtGpiyGKAv8RYhQ0i2cgdaZBv4X08K0vxZCRr0xlJi2We2v4fMffM82cZCmZB95Px7UmXM092A06XxsfolChI9EUMue';

    //TEST USER 2: vabldmq_sidhuberg_1411568513@tfbnw.net
    private $access_token2 = 'CAAKBEjFH044BALW1KQZCcXuTDqwYVIncwCYapSZAZBjYjMVCB5Iq4MarBJz6yZBDAx6XGxjcTS3IWPZBe9Nv9MeXFkbXlQtZCrV5eZCkXZCWsZA6FqAZAJbvGZAubGYCLEP68ugfgUygX3bxYdIsqScqZAO3HSRezAI64YbwJUHZBJbKxEbkiDBGkbNy3rpS5kOOtzK8HJZCrAuKfkrQynBjWMDIAN';

    public function testSelfProfile()
    {

        $facebook = new Publish($this->access_token1, $this->appId, $this->appSecret);

        $this->assertEquals(true, $facebook->setDefaultMessage());

        //SETTING POST PARAMS
        $this->assertEquals($facebook, $facebook->setPostParamsArray(array('link' => 'http://amsterdamstandard.com', 'message' => 'test', 'caption' => 'test caption')));
        $this->assertEquals(false, $facebook->setPostParamsArray(array('link' => 'not_a_link', 'message' => 'test')));

        //GETTING POST PARAMS
        $post_params = $facebook->getPostParamsArray();
        $this->assertEquals(true, is_array($post_params));

        //GETTING USER DATA
        $graph_object = $facebook->getUserData();
        $user_id = $graph_object->getId();

        //PUBLISHING ON WALL
        $post_id = $facebook->postOnWall();
        //test post ID
        $this->assertEquals(true, gettype($post_id) == 'string');

        //DELETING POST
        $this->assertEquals(true, $facebook->deletePost($post_id));

        //PUBLISHING ON WALL with setting post params
        $facebook->validateUser($user_id);
        $post_id = $facebook->postOnWall($post_params);

        //DELETING POST
        $this->assertEquals(true, $facebook->deletePost($post_id));

        //PUBLISHING ON WALL with setting post params and target user
        $post_id = $facebook->postOnWall($post_params, $user_id);

        //DELETING POST
        $this->assertEquals(true, $facebook->deletePost($post_id));

        //ADDING PICTURE WITH COMMENT TO PICTURES
        $curl_file = curl_file_create('test_picture.png', 'image/png', 'test_name');
        $post_id = $facebook->postPhoto($curl_file, 'Test picture');

        //DELETING PICTURE POST
        $this->assertEquals(true, $facebook->deletePost($post_id));
    }

    public function testCrossProfile()
    {
        //***TEST CROSS PROFILE ACTIONS***

        //Set session as User1
        $publishUser1 = new Publish($this->access_token1, $this->appId, $this->appSecret);

        //Set session as User2
        $publishUser2 = new Publish($this->access_token2, $this->appId, $this->appSecret);

        //Check User1's ID
        $idUser1 = $publishUser1->getUserData()->getId();
        echo $linkUser1 = $publishUser2->getUserData($idUser1)->getLink();

        //Check User1's link as User2
        $this->assertEquals($linkUser1, $publishUser2->getUserData($idUser1)->getLink());

        //Publish on User1's wall as User2
        //$post_params = $publishUser2->getPostParamsArray( );
        //$post_params['caption'] = "User2's post";
        //$publishUser2->postOnWall($post_params,$idUser1);
        //This is not allowed: (#200) Feed story publishing to other users is disabled for this application [200]
    }
}