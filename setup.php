<?php
include "database.php";

$db = new database();
$db->drop_db();
$db->create_db();

$db = new database();

// sql to create  product
$sql = "CREATE TABLE product (
    code CHAR(2) PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    price decimal(13,2),
    qty int(1) unsigned DEFAULT 1
    ); ";

$db->create_table($sql);

// sql to create packaging options
$sql = "CREATE TABLE packaging_option (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code CHAR(2),
    qty int(5) unsigned,
    price decimal(13,2)
    );";

$db->create_table($sql);

// populating table product with known data
$db->insert("product",array('code'=>'CH','name'=>'Cheese','price'=>5.95));
$db->insert("product",array('code'=>'HM','name'=>'Ham','price'=>7.95));
$db->insert("product",array('code'=>'SS','name'=>'Soy Sauce','price'=>11.95));

// populating table packaging_option with known data
$db->insert("packaging_option",array('code'=>'CH','qty'=>3,'price'=>14.95));
$db->insert("packaging_option",array('code'=>'CH','qty'=>5,'price'=>20.95));
$db->insert("packaging_option",array('code'=>'HM','qty'=>2,'price'=>13.95));
$db->insert("packaging_option",array('code'=>'HM','qty'=>5,'price'=>29.95));
$db->insert("packaging_option",array('code'=>'HM','qty'=>8,'price'=>40.95));

header("Location: http://localhost/bgl");
?>