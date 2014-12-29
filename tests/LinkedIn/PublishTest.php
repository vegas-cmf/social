<?php

namespace Vegas\Social\Tests\LinkedIn;

use Vegas\Social\LinkedIn\Publish;

class PublishTest extends \PHPUnit_Framework_TestCase
{
    private $config = [
        'app_key' => '77c863ytgelytp',
        'app_secret' => 'CHHUveJYMda6FzfH',
        'access_token' => 'AQV3FqOmzEj84mQc5s-qUcoJaNZ0neY9SwyEWG1OrWlBR0nTxDeGbVEtfcPUSq9xkvplN7iijlZL9ZWGDlHKzm6CuvSigxtCgrstZLzyJr3kGOEtRWMOcp2kyNwFPXfJDp1hhVcdlvCx2QRWmvBEi-9rHpkvNr513i8w36sk4N9Eau5n6GQ'
    ];


    public function test()
    {
        $linkedIn = new Publish($this->config);

        $user = $linkedIn->getUserData();
        $this->assertTrue(is_array($user));
        $this->assertTrue(is_string($user['firstName']));


        $post_confirmation = $linkedIn->setTitle('Test title' . rand())->setMessage('Test picture' . rand())->setLink('https://github.com/vegas-cmf')->post();
        $this->assertEquals(2, count($post_confirmation));
        //var_dump($post_confirmation);

        $post_params = $linkedIn->getPostParams();
        $post_params['content']['title'] = "New test " . rand();
        $post_params_after_set = $linkedIn->setPostParams($post_params)->getPostParams();
        $this->assertEquals($post_params, $post_params_after_set);

        $linkedIn->setPhoto('http://www.toughzebra.com/wp-content/uploads/Amsterdam.Standard.Logo_.png')->setTitle('Test title' . rand())->setMessage('Test picture' . rand())->setLink('https://github.com/vegas-cmf');
        $post_confirmation = $linkedIn->post();
        $this->assertEquals(2, count($post_confirmation));
        //var_dump($post_confirmation);
    }

    public function testExceptions()
    {
        $publishObject = new Publish($this->config);

        //SET PHOTO - EXCEPTION TEST
        try {
            $publishObject->setPhoto(['fake' => 'fake']);
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception\InvalidArgumentException', $ex);
        }

        //SET PHOTO - EXCEPTION2 TEST
        try {
            $publishObject->setPhoto('1234');
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception\InvalidLinkException', $ex);
        }

        //SET LINK - EXCEPTION TEST
        try {
            $publishObject->setLink('1234');
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception\InvalidLinkException', $ex);
        }

        //wrong POST PARAMS - EXCEPTION TEST
        try {
            $publishObject->setPostParams(['fake' => 'fake']);
            throw new \Exception('Not this exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('\Vegas\Social\Exception\InvalidPostParamsException', $ex);
        }
    }
}
