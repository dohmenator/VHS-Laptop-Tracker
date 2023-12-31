<?php
// replace_loanCord.php

// Include the database connection file
require "connectToDatabase.php";

// Initialize variables
$studentNumber = "";
$firstName = "";
$lastName = "";
$message = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the student number from the form
    $studentNumber = $_POST['studentNumber'];

    // Sanitize the input
    $studentNumber = htmlspecialchars($studentNumber);

    // Retrieve the entered first name and last name
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];

    // Sanitize the input
    $firstName = htmlspecialchars($firstName);
    $lastName = htmlspecialchars($lastName);

    // Connect to the database
    $conn = connectToDatabase();

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    // Check if the student exists in the database
    if (!empty($firstName) && !empty($lastName)) {
        // Get the selected action (Replacement or Loaner)
        $selectedAction = $_POST['action'];

        // Set the appropriate fields based on the selected action
        $loanerCordCheckedOut = 0;
        $loanerCordCheckedIn = 0;
        $replacementCordCheckedOut = 0;
        $replacementCordCheckedIn = 0;

        if ($selectedAction === "loaner") {
            $loanerCordCheckedOut = 1;
        } elseif ($selectedAction === "replacement") {
            $replacementCordCheckedOut = 1;
        }

        // Insert a new record into the Replacement_Loaner_Cords table
        $insertQuery = "INSERT INTO replacement_loaner_cords (student_number, loaner_cord_checkedout, loaner_cord_checkedin, replacement_cord_checkedout, replacement_cord_checkedin, checkout_date) 
                        VALUES (?, ?, ?, ?, ?, NOW())"; // Using NOW() to get the current date and time
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("siiii", $studentNumber, $loanerCordCheckedOut, $loanerCordCheckedIn, $replacementCordCheckedOut, $replacementCordCheckedIn);
        $stmt->execute();

        // Display success message
        $message = "Cord successfully checked out to $firstName $lastName as a $selectedAction on " . date("Y-m-d H:i:s") . ".";
    } else {
        // Student with the entered number doesn't exist
        $message = "Student with the entered number doesn't exist.";
    }

    $conn->close();
}
?>

<!-- Your HTML code for the replace_loanCord.php page goes here... -->

<!DOCTYPE html>
<html>
<head>
    <title>Replace/Lend Cord</title>
    <link rel="stylesheet" href="CSS/checkin_out.css">
    <link rel="icon" href="hawk.jpg" type="image/jpg">
</head>
<body>
    <div class="form-container">
        <h1>Replace/Lend Cord</h1>

        <form id="getStudentForm">
            <div class="form-field">
                <label for="studentNumber" class="form-label">Enter Student Number:</label>
                <input type="text" id="studentNumber" name="studentNumber" class="form-input" required>
                <button type="button" onclick="getStudent()" class="form-button">Get Student</button>
            </div>

            <div class="form-field">
                <label for="firstName" class="form-label">First Name:</label>
                <input type="text" id="firstName" name="firstName" class="form-input" required readonly>
            </div>

            <div class="form-field">
                <label for="lastName" class="form-label">Last Name:</label>
                <input type="text" id="lastName" name="lastName" class="form-input" required readonly>
            </div>
        </form>

        <form id="replaceLoanForm" style="display: none;" method="post" action="replace_loanCord.php">
            <div class="form-field">
                <label class="form-label">Select Action:</label>
                <div class="form-radio-group-horizontal">
                    <input type="radio" id="replacementRadio" name="action" value="replacement" required>
                    <label for="replacementRadio">Replacement</label>

                    <input type="radio" id="loanerRadio" name="action" value="loaner" required>
                    <label for="loanerRadio">Loaner</label>
                </div>
            </div>

            <div class="form-field">
                <input type="submit" value="Check Out" class="form-button" id="checkoutButton">
                <button type="button" class="form-button" onclick="goToHome()">Home</button>
            </div>

            <div id="error" class="error-message"></div>
        </form>
    </div>

    <script src="JS/replace_loanCord.js"></script>
</body>
</html>
