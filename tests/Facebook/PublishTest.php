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

        $post_id = $facebook->publishOnWall();
        $this->assertEquals(true, $post_id!=false);
        $this->assertEquals(true, $facebook->deletePost($post_id));

        $this->assertEquals(true, $facebook->setPostParamsArray(array('link'=>'http://google.com', 'message'=>'test')) );
        $this->assertEquals(false, $facebook->setPostParamsArray(array('link'=>'not_a_link', 'message'=>'test')) );


        $this->assertEquals( "Vegas Cms", $facebook->getUserData('1485092648429699')->getName() );
        //TO DO: add option to set images in post

    }
}