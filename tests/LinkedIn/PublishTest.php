<?php

namespace Vegas\Social\Tests\LinkedIn;

use Vegas\Social\LinkedIn\Publish;

class PublishTest extends \PHPUnit_Framework_TestCase
{
    public function testPublish()
    {
        $linkedIn = new Publish();

        $this->assertEquals(true, $linkedIn->publish(true));
        $this->assertEquals(false, $linkedIn->publish(false));
    }
}