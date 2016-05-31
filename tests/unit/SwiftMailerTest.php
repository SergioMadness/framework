<?php

class SwiftMailerTest extends \PHPUnit_Framework_TestCase
{
    private $component;

    protected function setUp()
    {
        $this->component = new \pwf\components\swiftmailer\SwiftMailer();
        $this->component->loadConfiguration([])->init();
    }

    protected function tearDown()
    {
        $this->component = null;
    }

    public function testIt()
    {
        $this->component
            ->addMail($this->component->createMail()->addTo('test@test.ru')->setBody('Test'))
            ->send();
    }
}