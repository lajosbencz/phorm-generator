<?php

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    public function testSchema()
    {
        chdir(__DIR__ . '/../fixture/');
        $di = new \Phalcon\Di();
        $phorm = new \PhormGenerator\PhormGenerator;
        $this->assertEquals(1,count($phorm->getSchemas()));
    }
}
