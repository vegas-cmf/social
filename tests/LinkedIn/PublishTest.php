<?php

namespace Vegas\Social\Tests\LinkedIn;

use Vegas\Social\LinkedIn\Publish;

class PublishTest extends \PHPUnit_Framework_TestCase
{
    private $config = [
        'app_key' => '77c863ytgelytp',
        'app_secret' => 'CHHUveJYMda6FzfH',
        'access_token' => 'AQXyPz0H7jhFw1iw6LM7SosVSaIFHtYy4w5mCzgKh0E1Yl7eJfyU5H0eEsalmAl5p5KLuxBuV9TGZ6lCcOuQQ6-RIstVcl0QuRKGurMOEgMMpUPhqJX46awuoLe-5eNmHWpo0NbrfGzojNc3TkrDoC6-Vt0d09l-Ujox9Ti05g-F-klwwnk'
    ];


    public function test()
    {
        $linkedIn = new Publish($this->config);

        $user = $linkedIn->getUserData();
        $this->assertTrue(is_array($user));
        $this->assertTrue(is_string($user['firstName']));


        $postConfirmation = $linkedIn->setTitle('Test title' . rand())->setMessage('Test picture' . rand())->setLink('https://github.com/vegas-cmf')->post();
        $this->assertEquals(2, count($postConfirmation));
        //var_dump($post_confirmation);

        $postParams = $linkedIn->getPostParams();
        $postParams['content']['title'] = "New test " . rand();
        $postParamsAfterSet = $linkedIn->setPostParams($postParams)->getPostParams();
        $this->assertEquals($postParams, $postParamsAfterSet);

        $linkedIn->setPhoto('http://www.toughzebra.com/wp-content/uploads/Amsterdam.Standard.Logo_.png')->setTitle('Test title' . rand())->setMessage('Test picture' . rand())->setLink('https://github.com/vegas-cmf');
        $postConfirmation = $linkedIn->post();
        $this->assertEquals(2, count($postConfirmation));
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
