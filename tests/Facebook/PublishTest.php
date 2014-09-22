<?php

namespace Vegas\Social\Tests\Facebook;

use Vegas\Social\Facebook\Publish;

class PublishTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        //USER LOGIN TOKEN
        $config['access_token'] = 'CAAJHRc19j50BAEtyLp4hnJPpdw1KnqjVZBR4l3FtIL5fK4iEHS2OZCdlYnKZCKcchiZCr2k8D38XVxiZBqPLd02QJ19HJNvY3NjItSZCGjZC9nGFqYNeJ8Sb2al7shQr7UjsixNxemMfZCxNlR8v8ZBR6vCbm1P3MDZCYAasTZCJioiwLDcygN96LVNKTVMKt00L3bPZC7zzNoPYDl8fknfzNPn3';

        //VEGAS CMF
        $config['appId'] = '641315079294877';
        $config['appSecret'] = '149317069d91ad668831b3db8f65457e';


        $facebook = new Publish($config['access_token'],$config['appId'],$config['appSecret']);

        $this->assertEquals(true, $facebook->setDefaultMessage());

        //SETTING POST PARAMS
        $this->assertEquals($facebook, $facebook->setPostParamsArray(array('link'=>'http://amsterdamstandard.com', 'message'=>'test', 'caption'=>'test caption')) );
        $this->assertEquals(false, $facebook->setPostParamsArray(array('link'=>'not_a_link', 'message'=>'test')) );

        //GETTING POST PARAMS
        $post_params = $facebook->getPostParamsArray();
        $this->assertEquals( true, is_array($post_params) );

        //GETTING USER DATA
        $graph_object = $facebook->getUserData('1485092648429699');
        $this->assertEquals( "Vegas Cms", $graph_object->getName() );

        //PUBLISHING ON WALL
        $post_id = $facebook->publishOnWall();
        //test post ID
        $this->assertEquals(true, gettype($post_id)=='string');

        //DELETING POST
        $this->assertEquals(true, $facebook->deletePost($post_id));

        //PUBLISHING ON WALL with setting post params
        $facebook->setTargetUser('1485092648429699');
        $post_id = $facebook->publishOnWall($post_params);

        //DELETING POST
        $this->assertEquals(true, $facebook->deletePost($post_id));

        //PUBLISHING ON WALL with setting post params and target user
        $post_id = $facebook->publishOnWall($post_params,'1485092648429699');

        //DELETING POST
        $this->assertEquals(true, $facebook->deletePost($post_id));

        //ADDING PICTURE WITH COMMENT TO PICTURES
        $curl_file = curl_file_create('test_picture.png','image/png','test_name');
        $post_id = $facebook->publishInPhotos($curl_file, 'Test picture');

        //DELETING PICTURE POST
        $this->assertEquals(true, $facebook->deletePost($post_id));
    }
}