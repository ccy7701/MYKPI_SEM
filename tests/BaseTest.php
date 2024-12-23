<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
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
}