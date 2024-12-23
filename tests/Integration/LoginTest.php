<?php

namespace Tests;

class LoginTest extends BaseTest
{
    private const REGISTRATION_ACTION_FILE = __DIR__ . "/../../action_scripts/registration_action.php";
    private const LOGIN_ACTION_FILE = __DIR__ . "/../../action_scripts/login_action.php";
    private const STANDARD_PASSWORD = "Test@1234";

    // Create dummy account for testing
    private function createDummyAccount($matricNumber, $accountEmail, $accountPassword)
    {
        // Data for successful registration
        $_POST = [
            "matricNumber" => $matricNumber,
            "accountEmail" => $accountEmail,
            "accountPassword" => $accountPassword,
            "reenterPassword" => $accountPassword
        ];

        // Mock the HTTP request method
        $_SERVER["REQUEST_METHOD"] = "POST";

        global $conn;
        $conn = $this->conn;

        // Push the data to the database
        ob_start();
        require_once self::REGISTRATION_ACTION_FILE;
        ob_get_clean();
    }

    // Check if an account with the given matric number exists
    private function fetchByMatricNumber($matricNumber)
    {
        $stmt = $this->conn->prepare("SELECT * FROM account WHERE matricNumber = ?");
        $stmt->bind_param("s", $matricNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows;
    }

    // Attempt to log in with received credentials
    private function attemptLogin($loginMatric, $loginPassword)
    {
        $_POST = [
            "loginmatric" => $loginMatric,
            "loginpassword" => $loginPassword
        ];

        // Mock the HTTP request method
        $_SERVER['REQUEST_METHOD'] = 'POST';
        global $conn;
        $conn = $this->conn;

        // Capture the output for verification
        ob_start();
        require_once self::LOGIN_ACTION_FILE;
        ob_get_clean();

        // Return session and output for assertions
        return ['session' => $_SESSION];
    }

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

        // Assert that the test account was created
        $this->assertEquals(1, $this->fetchByMatricNumber('BI21110001'));

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
        $this->assertEquals(1, $this->fetchByMatricNumber('BI21110002'));
        $loginResult = $this->attemptLogin('BI2111002', "Invalid@1234");

        // Since login failed, assert that no session values are set
        $this->assertEmpty($loginResult['session']);

        // Clean up session
        session_unset();
        session_destroy();
    }
}