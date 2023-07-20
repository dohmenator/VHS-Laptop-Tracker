<?php
// charger_cord_history.php

// Include the database connection file
require "connectToDatabase.php";

function formatDate($dateTimeString) {
    $dateTime = new DateTime($dateTimeString);
    return $dateTime->format('F j, Y, g:i A');
}

// Initialize variables
$studentNumber = "";
$firstName = "";
$lastName = "";
$message = "";
$result = null;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the student number from the form
    $studentNumber = $_POST['studentNumber'];

    // Sanitize the input
    $studentNumber = htmlspecialchars($studentNumber);

    // Connect to the database
    $conn = connectToDatabase();

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    // Query the StudentData table to get student's first name and last name
    $studentQuery = "SELECT first_name, last_name FROM StudentData WHERE student_number = ?";
    $stmt = $conn->prepare($studentQuery);
    $stmt->bind_param("s", $studentNumber);
    $stmt->execute();
    $stmt->store_result();

    // Check if the student exists in the database
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($firstName, $lastName);
        $stmt->fetch();
        
        // Query the LaptopData table to get charger cord history for the student
        $cordHistoryQuery = "SELECT * FROM replacement_loaner_cords WHERE student_number = ?";


        $stmt = $conn->prepare($cordHistoryQuery);
        $stmt->bind_param("s", $studentNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0){
            $message = "No charger cord history found for student with ID of $studentNumber.";
        }
       
    } else {
        // Student with the entered number doesn't exist
        $message = "Student with the entered number doesn't exist.";
    }

    $stmt->close();
    $conn->close();
}
?>


<!-- Your HTML code for the charger_cord_history.php page goes here... -->
<!DOCTYPE html>
<html>
<head>
    <title>Student Charger Cord History</title>
    <link rel="stylesheet" href="CSS/checkin_out.css">
    <link rel="stylesheet" href="CSS/reports.css">
    
    <script src="JS/checkin.js"></script>
</head>
<body>
    <div class="form-container">
        <h2>Student Charger Cord History</h2>
        <form method="post" action="">
            <div class="form-field">
                <label for="studentNumber" class="form-label">Enter Student Number:</label>
                <input type="text" id="studentNumber" name="studentNumber" class="form-input" required>
            </div>
            <div class="form-field">
                <input type="submit" value="Show History" class="form-button">
                <button type="button" class='form-button' onclick="goToHome()">Home</button>
            </div>
        </form>
    </div>

    <?php
    // Display the message if there is any
    if (!empty($message)) {
        echo "<p>$message</p>";
    }

    // Display the table if the query results are available
    if ($result && $result->num_rows > 0) {
    ?>
    <div class="table-container">
        <table class="report-table">
            <tr>
                <th>Student Number</th>               
                <th>First Name</th>
                <th>Last Name</th>
                <th>Loaner Cord Checked Out</th>
                <th>Loaner Cord Checked In</th>
                <th>Replacement Cord Checked Out</th>
                <th>Replacement Cord Checked In</th>
                <th>Checkout Date</th>
                <th>Checkin Date</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $studentNumber; ?></td>                
                <td><?php echo $firstName; ?></td>
                <td><?php echo $lastName; ?></td>
                <td><?php echo ($row['loaner_cord_checkedout'] == 1) ? 'Yes' : 'No'; ?></td>
                <td><?php echo ($row['loaner_cord_checkedin'] == 1) ? 'Yes' : 'No'; ?></td>
                <td><?php echo ($row['replacement_cord_checkedout'] == 1) ? 'Yes' : 'No'; ?></td>
                <td><?php echo ($row['replacement_cord_checkedin'] == 1) ? 'Yes' : 'No'; ?></td>
                <td><?php echo formatDate($row['checkout_date']); ?></td>
                <td><?php echo formatDate($row['checkin_date']); ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <?php } ?>
</body>
</html>
