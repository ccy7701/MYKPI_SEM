<?php

namespace Tests;

class LoginTest extends BaseTest
{
    private const STANDARD_PASSWORD = "Test@1234";

    /**
     * @runInSeparateProcess
     * @testdox Login with valid credentials
     */
    public function testValidLogin()
    {
        // First create a dummy account
        $this->createDummyAccount(
            'BI21110001',
            'valid_login_bi21@iluv.ums.edu.my',
            self::STANDARD_PASSWORD
        );

        // Fetch the test account by matric number
        $result = $this->fetchAccount('BI21110001');

        // Assert that the test account was created
        $this->assertEquals(1, $result->num_rows);

        // Now, simulate login with valid credentials
        $loginResult = $this->attemptLogin('BI21110001', self::STANDARD_PASSWORD);

        // Assert session values are set correctly
        $this->assertNotEmpty($loginResult['session']["UID"]);
        $this->assertEquals('BI21110001', $loginResult['session']['userName']);

        // Clean up session
        session_unset();
        session_destroy();
    }

    /**
     * @runInSeparateProcess
     * @testdox Login with invalid credentials
     */
    public function testInvalidLogin()
    {
        $this->createDummyAccount(
            'BI21110002',
            'invalid_login_bi21@iluv.ums.edu.my',
            self::STANDARD_PASSWORD
        );

        // Assert that the account was created, then attempt the login
        $result = $this->fetchAccount('BI21110002');
        $this->assertEquals(1, $result->num_rows);
        $loginResult = $this->attemptLogin('BI2111002', "Invalid@1234");

        // Since login failed, assert that no session values are set
        $this->assertEmpty($loginResult['session']);

        // Clean up session
        session_unset();
        session_destroy();
    }
}