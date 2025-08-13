<?php
$servername = "localhost";
$username = "root"; // Your database username
$password = "RobertEric2020##"; // Your database password
$dbname = "kcourse"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
