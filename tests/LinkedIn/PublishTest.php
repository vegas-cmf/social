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


    public function test()
    {
        $linkedIn = new Publish($this->config);

        $user = $linkedIn->getUserData();
        $this->assertEquals(true, is_string($user['firstName']));


        $post_confirmation = $linkedIn->post();
        $this->assertEquals(2, count($post_confirmation));
        //var_dump($post_confirmation);

        $linkedIn->setPhoto('http://www.toughzebra.com/wp-content/uploads/Amsterdam.Standard.Logo_.png')->setTitle('Test title' . rand())->setMessage('Test picture' . rand())->setLink('https://github.com/vegas-cmf');
        $post_confirmation = $linkedIn->post();
        $this->assertEquals(2, count($post_confirmation));
        //var_dump($post_confirmation);
    }
}