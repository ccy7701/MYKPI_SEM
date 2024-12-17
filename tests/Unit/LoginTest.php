<?php

use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    public function testLoginPageLoads()
    {
        $content = file_get_contents(__DIR__ . '/../../login.php');
        $this->assertStringContainsString('<form id="loginform"', $content);
    }
}
