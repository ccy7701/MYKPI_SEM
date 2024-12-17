<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../math.php';

class MathTest extends TestCase
{
    // Test case for the add function
    public function testAdd()
    {
        $this->assertEquals(5, add(2, 3));
        $this->assertEquals(0, add(-2, 2));
        $this->assertEquals(-5, add(-2, -3));
    }
}
