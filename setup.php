<?php
include "database.php";

// Create connection
$conn = new mysqli($servername, $username, $password);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$conn->query("DROP DATABASE IF EXISTS bgl");

// Create database
$sql = "CREATE DATABASE $dbname";

$conn->query($sql);

$conn->close();

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// sql to create  product
$sql = "CREATE TABLE product (
    code CHAR(2) PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    price decimal(13,2),
    qty int(1) unsigned DEFAULT 1
    ); ";

$conn->query($sql);

// sql to create packaging options
$sql = "CREATE TABLE packaging_option (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code CHAR(2),
    qty int(5) unsigned,
    price decimal(13,2)
    );";

$conn->query($sql);

// populating table product with known data
$sql = "INSERT INTO product VALUES ('CH', 'Cheese',5.95,1),('HM', 'Ham',7.95,1), ('SS', 'Soy Sauce',11.95,1);";

$conn->query($sql);
// populating table packaging_option with known data
$sql = "INSERT INTO packaging_option VALUES (NULL,'CH', 3,14.95), (NULL,'CH', 5,20.95), (NULL,'HM', 2,13.95), (NULL,'HM', 5,29.95), (NULL,'HM', 9,40.95);";

$conn->query($sql);

$conn->close();
?>