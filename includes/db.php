<?php
$host = "localhost";  
$user = "root";     
$pass = "Derrick2017";               
$dbname = "lostfound_db";  


$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//echo "Connected successfully!";
?>

