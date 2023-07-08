<?php
error_reporting(1);
$servername = "localhost";
$username = "shrinkcomrupali";
$password = "rupali@123";
$database = "vobuzz_weekly";
$conn = new mysqli($servername, $username, $password,$database);
$conn->set_charset("utf8mb4");
if($conn->connect_error)
{
	die("Connection failed: " .$conn->connect_error);
}else{
    // echo "connected";
}
?>