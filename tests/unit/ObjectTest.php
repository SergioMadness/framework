<?php

class BasicObjectTestClass extends pwf\basic\BasicObject
{
    private $field;

    public function setField($field)
    {
        $this->field = $field.'test';
    }

    public function getField()
    {
        return $this->field.'1';
    }
}

class ObjectTest extends \PHPUnit_Framework_TestCase
{

    public function testObject()
    {
        $o        = new BasicObjectTestClass();
        $o->field = 'test';
        $this->assertEquals('testtest1', $o->field);

        try {
            $o->wrongField = '1';
            $this->fail();
        } catch (Exception $ex) {

        }

        try {
            $test = $o->wrongField;
            $this->fail();
        } catch (Exception $ex) {

        }
    }
}