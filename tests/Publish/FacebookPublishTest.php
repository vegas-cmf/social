<?php

namespace Vegas\Social\Tests\Publish;

use Vegas\Social\Publish\FacebookPublish;

class FacebookPublishTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        //USER LOGIN TOKEN
        $config['access_token'] = 'CAAJHRc19j50BAEtyLp4hnJPpdw1KnqjVZBR4l3FtIL5fK4iEHS2OZCdlYnKZCKcchiZCr2k8D38XVxiZBqPLd02QJ19HJNvY3NjItSZCGjZC9nGFqYNeJ8Sb2al7shQr7UjsixNxemMfZCxNlR8v8ZBR6vCbm1P3MDZCYAasTZCJioiwLDcygN96LVNKTVMKt00L3bPZC7zzNoPYDl8fknfzNPn3';

        //VEGAS CMF
        $config['appId'] = '641315079294877';
        $config['secret'] = '149317069d91ad668831b3db8f65457e';


        $facebook = new FacebookPublish($config['access_token'],$config['appId'],$config['secret']);

        $post_id = $facebook->publishOnWall();
        //TO DO delete this post
        $this->assertEquals(true, $post_id!=false);


    }
}