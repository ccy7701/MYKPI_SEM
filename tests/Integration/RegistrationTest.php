<?php

use PHPUnit\Framework\TestCase;

class RegistrationTest extends TestCase
{
    private const REGISTRATION_ACTION_FILE = "/../../action_scripts/registration_action.php";
    private const STANDARD_PASSWORD = "Test@1234";

    private $conn;

    // Database connection setup
    protected function setUp(): void
    {
        // Initialise database configuration
        $databaseHost = getenv('DB_HOST') ?: 'localhost';
        $databaseUsername = getenv('DB_USERNAME') ?: 'root';
        $databasePassword = getenv('DB_PASSWORD') ?: '';
        $databaseName = getenv('DB_DATABASE') ?: 'mystudykpi';

        // Create database connection
        $this->conn = new mysqli($databaseHost, $databaseUsername, $databasePassword, $databaseName);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        // Clear the account and profile table for consistent testing
        $this->conn->query("DELETE FROM account");
        $this->conn->query("DELETE FROM profile");
    }

    // Close database connection
    protected function tearDown(): void
    {
        $this->conn->close();
    }

    // Test case for successful registration
    public function testValidRegistration()
    {
        // Data for successful registration
        $_POST = [
            "matricNumber" => "BI21110235",
            "accountEmail" => "chiew_cheng_bi21@iluv.ums.edu.my",
            "accountPassword" => self::STANDARD_PASSWORD,
            "reenterPassword" => self::STANDARD_PASSWORD
        ];

        // Mock the HTTP request method
        $_SERVER["REQUEST_METHOD"] = "POST";

        // Provide the database connection object expected by registration_action.php
        global $conn;
        $conn = $this->conn;

        // Start buffering to capture script output
        ob_start();
        require __DIR__ . self::REGISTRATION_ACTION_FILE;
        ob_get_clean();

        // Push the POST data to the database, then verify it was actually inserted
        $stmt = $this->conn->prepare("SELECT * FROM account WHERE matricNumber = ?");
        $matricNumber = 'BI21110235';
        $stmt->bind_param("s", $matricNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        // Assertions
        $this->assertEquals(1, $result->num_rows);
    }

    // Test case for matric number already taken
    public function testDuplicateMatricNumber()
    {
        // Insert an existing record into the database
        $stmt = $this->conn->prepare("INSERT INTO account (matricNumber, accountEmail, accountPwd) VALUES (?, ?, ?)");
        $matricNumber = "BI21110236";
        $email = "existinguser@iluv.ums.edu.my";
        $passwordHash = password_hash(self::STANDARD_PASSWORD, PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $matricNumber, $email, $passwordHash);
        $stmt->execute();

        // Attempt to register with the same matric number
        $_POST = [
            "matricNumber" => "BI21110236",
            "accountEmail" => "newuser@iluv.ums.edu.my",
            "accountPassword" => self::STANDARD_PASSWORD,
            "reenterPassword" => self::STANDARD_PASSWORD
        ];
        $_SERVER["REQUEST_METHOD"] = "POST";
        global $conn;
        $conn = $this->conn;

        ob_start();
        require __DIR__ . self::REGISTRATION_ACTION_FILE;
        $output = ob_get_clean();

        // Assert duplicate matric number error message
        $this->assertStringContainsString("ERROR: A user with this Matric Number already exists", $output);
    }

    // Test case for password mismatch
    public function testPasswordMismatch()
    {
        $_POST = [
            "matricNumber" => "BI21110237",
            "accountEmail" => "pwmismatch@iluv.ums.edu.my",
            "accountPassword" => self::STANDARD_PASSWORD,
            "reenterPassword" => "Wrong@1234"
        ];
        $_SERVER["REQUEST_METHOD"] = "POST";
        global $conn;
        $conn = $this->conn;

        ob_start();
        require __DIR__ . self::REGISTRATION_ACTION_FILE;
        $output = ob_get_clean();

        $this->assertStringContainsString(
            "ERROR: Passwords do not match. Please try again.",
            $output,
            "The error message for mismatched passwords was not found."
        );
    }
}
