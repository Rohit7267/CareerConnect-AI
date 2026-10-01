<?php

$host = "127.0.0.1";
$username = "root";
$password = "rohit@123";
$database = "careerconnect";
$port = 3307;

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>