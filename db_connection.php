<?php
// Database configuration
$host = 'localhost';
$dbname = 'quiz_craft';
$husername = 'root';
$password = '';

// Attempt to connect to the database
$conn = new mysqli($host, $husername, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
