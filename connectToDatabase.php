<?php

function connectToDatabase() {
    $servername = 'localhost';
    $username = 'LaptopTracker';
    $password = 'myLaptopTracker01';
    $dbname = 'laptoptrackerdata';
   
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    return $conn;
}

?>
