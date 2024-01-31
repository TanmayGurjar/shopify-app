<!-- inc/connect.php -->

<?php

$host = "localhost";
$username = "root";
$password = "";
$dbname = "tnmy";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("connect was not successful:" . mysqli_connect_error());
}
