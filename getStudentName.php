<?php
// getStudentName.php

// Include the database connection file
require "connectToDatabase.php";

// Initialize variables
$studentNumber = "";
$firstName = "";
$lastName = "";

// Check if the student number is provided in the request
if (isset($_POST['studentNumber'])) {
    // Retrieve the student number from the request
    $studentNumber = $_POST['studentNumber'];

    // Sanitize the input
    $studentNumber = htmlspecialchars($studentNumber);

    // Connect to the database
    $conn = connectToDatabase();

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    // Query the database to fetch the student's first and last name based on the student number
    $query = "SELECT first_name, last_name FROM studentdata WHERE student_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $studentNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the student exists in the database
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $firstName = $row['first_name'];
        $lastName = $row['last_name'];
    }

    $conn->close();
}

// Return the student's first and last name as JSON response
$response = array(
    'firstName' => $firstName,
    'lastName' => $lastName
);

echo json_encode($response);
?>
