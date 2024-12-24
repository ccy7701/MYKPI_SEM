<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected const REGISTRATION_ACTION_FILE = __DIR__ . "/../action_scripts/registration_action.php";
    protected const LOGIN_ACTION_FILE = __DIR__ . "/../action_scripts/login_action.php";

    protected const STANDARD_PASSWORD = "Test@1234";
    protected $conn;

    // Database connection setup
    protected function setUp(): void
    {
        // Initialise database configuration
        $databaseHost = getenv('DB_HOST') ?: 'localhost';
        $databaseUsername = getenv('DB_USERNAME') ?: 'root';
        $databasePassword = getenv('DB_PASSWORD') ?: '';
        $databaseName = getenv('DB_DATABASE') ?: 'mystudykpi';

        // Create database connection
        $this->conn = new \mysqli($databaseHost, $databaseUsername, $databasePassword, $databaseName);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        // Clear the account table for consistent testing
        $this->conn->query("DELETE FROM account");
    }

    // Close database connection
    protected function tearDown(): void
    {
        $this->conn->close();
    }

    // Create dummy account for testing
    protected function createDummyAccount($matricNumber, $accountEmail, $accountPassword)
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

    // Attempt to log in with received credentials
    protected function attemptLogin($loginMatric, $loginPassword)
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

    // Clean up session
    protected function cleanUpSession()
    {
        session_unset();
        session_destroy();
    }

    // Fetch account with the given matric number
    protected function fetchAccount($matricNumber)
    {
        $stmt = $this->conn->prepare("SELECT * FROM account WHERE matricNumber = ?");
        $stmt->bind_param("s", $matricNumber);
        $stmt->execute();
        
        return $stmt->get_result();
    }

    // Fetch profile by accountID
    protected function fetchProfile($accountID)
    {
        $stmt = $this->conn->prepare("SELECT * FROM profile WHERE accountID = ?");
        $stmt->bind_param("i", $accountID);
        $stmt->execute();

        return $stmt->get_result();
    }

    // Fetch activity by accountID and activity type
    protected function fetchActivityByType($accountID, $activityType)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM activity WHERE accountID = ? AND activityType = ?"
        );
        $stmt->bind_param("ii", $accountID, $activityType);
        $stmt->execute();
        
        return $stmt->get_result();
    }

    // Fetch activity by accountID and activity details
    protected function fetchActivityByDetails($accountID, $activityDetails)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM activity WHERE accountID = ? AND activityDetails = ?"
        );
        $stmt->bind_param("is", $accountID, $activityDetails);
        $stmt->execute();

        return $stmt->get_result();
    }
}
