<?php

namespace Vegas\Social\Tests\Facebook;

use Vegas\Social\Facebook\Publish;

class PublishTest extends \PHPUnit_Framework_TestCase
{
    private $config = array(
        'app_key' => '704865089606542',
        'app_secret' => '786207332b78fb7819d375d480c1c3cd',
        'access_token' => 'CAAKBEjFH044BAFYbyFgTS5cXxs5cGcsqmsRTGjr0i6ZAuvhjYh0uOZCU1rKSrIalr2OvDWUGMyz5Bskd4mynO7ijuZAeIeIhofd8B8yiYvtD5pmyVTE7nLnDkqPjp4NvjP02AJStfibbezadHPyR3amEoca2wFwGUJchR70fSS7dZAeEzw9d1qOQMZBxQjX6CvybmPcwmKin7ynzqbZAMwg6rjnooOxHIZD'
    );

    public function test()
    {

        $facebook = new Publish($this->config);

        //GETTING USER DATA
        $graph_object = $facebook->getUserData();
        $user_id = $graph_object->getId();

        //PUBLISHING ON WALL
        $post_id = $facebook->postOnWall();
        //test post ID
        $this->assertEquals(true, gettype($post_id) == 'string');

        //DELETING POST
        $this->assertEquals(true, $facebook->deletePost($post_id));

        /*
                //SETTING POST PARAMS
                $this->assertEquals($facebook, $facebook->setPostParamsArray(array('link' => 'http://amsterdamstandard.com', 'message' => 'test', 'caption' => 'test caption')));
                $this->assertEquals(false, $facebook->setPostParamsArray(array('link' => 'not_a_link', 'message' => 'test')));

                //GETTING POST PARAMS
                $post_params = $facebook->getPostParamsArray();
                $this->assertEquals(true, is_array($post_params));





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
                $curl_file = curl_file_create('../test_picture.png', 'image/png', 'test_name');
                $post_id = $facebook->postPhoto($curl_file, 'Test picture');

                //DELETING PICTURE POST
                $this->assertEquals(true, $facebook->deletePost($post_id));*/
    }
}