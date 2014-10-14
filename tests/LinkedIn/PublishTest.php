<?php

namespace Vegas\Social\Tests\LinkedIn;

use Vegas\Social\LinkedIn\Publish;

class PublishTest extends \PHPUnit_Framework_TestCase
{
    private $config = array(
        'app_key' => '77c863ytgelytp',
        'app_secret' => 'CHHUveJYMda6FzfH',
        'access_token' => 'AQW8quBxpTEJxPsKpb0MzKyrAooElYTsJQ4aH3w8c-OUZmd9kkUWsnJbEZrWYrupRinIvFUSUZhTWmqVb30IXC6bRV1Vx0kJzDK0K2ssWF1c-9MmAn8tG4O7FAA2sZGUdsVl1b6EpjRibboaO4C1E8mdmhTCtKDS2C0jVrI1stOiCdxmEMg'
    );


    public function testPublish()
    {
        $linkedIn = new Publish($this->config);

        $user = $linkedIn->getUserData();
        $this->assertEquals('Vegas', $user['firstName']);


        $post_confiramtion = $linkedIn->postOnWall(true);
        var_dump($post_confiramtion);

        $curl_file = curl_file_create('test_picture.png', 'image/png', 'Test message ' . rand());
        //$post_confiramtion = $linkedIn->postPhoto($curl_file,"message");
        var_dump($post_confiramtion);


    }
}