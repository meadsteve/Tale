<?php

namespace MeadSteve\Tale\Tests;

use MeadSteve\Tale\HelloWorld;
use PHPUnit\Framework\TestCase;

class HelloWorldTest extends TestCase
{
    public function testHello()
    {
        $this->assertTrue(HelloWorld::getTruth());
    }
}
