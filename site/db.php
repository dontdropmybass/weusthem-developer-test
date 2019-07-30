<?php
$servername = "localhost";
$username = "devtest";
global $conn;
$password = "securepassword";

try {
    $conn = new PDO("mysql:host=$servername;dbname=devtest", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
    die();
}
