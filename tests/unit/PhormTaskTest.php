<?php

/**
 * phorm-generator
 * user: lazos
 * date: 2016.12.17.
 */
class PhormTaskTest extends PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        chdir(__DIR__ . '/../fixture');
        $phorm = new \PhormGenerator\PhormGenerator();
        $phorm->update();
    }
}
