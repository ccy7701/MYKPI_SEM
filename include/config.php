<?php
    // mysqli_connect is used for database connection
    $databaseHost = getenv('DB_HOST') ?: 'localhost';
    $databaseUsername = getenv('DB_USERNAME') ?: 'root';
    $databasePassword = getenv('DB_PASSWORD') ?: '';
    $databaseName = getenv('DB_DATABASE') ?: 'mystudykpi';

    $conn = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);

    // check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: ".mysqli_connect_error();
    }
?>