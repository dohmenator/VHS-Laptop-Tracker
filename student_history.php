<?php
// student_history.php

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
        
        // Query the LaptopData table to get laptop history for the student
        $laptopHistoryQuery = "SELECT serial_number, laptop_checkedout, charger_cord_checkedout, checkout_date, checkin_date, charger_cord_returned 
                              FROM LaptopData WHERE student_number = ?";
        $stmt = $conn->prepare($laptopHistoryQuery);
        $stmt->bind_param("s", $studentNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0){
            $message = "No charger cord history found for the entered student number.";
        }
        
    } else {
        // Student with the entered number doesn't exist
        $message = "Student with the entered number doesn't exist.";
    }

    $stmt->close();
    $conn->close();
}
?>


<!-- Your HTML code for the student_history.php page goes here... -->
<!DOCTYPE html>
<html>
<head>
    <title>Student Laptop History</title>
    <link rel="stylesheet" href="CSS/checkin_out.css">
    <link rel="stylesheet" href="CSS/reports.css">
    
    <script src="JS/checkin.js"></script>
</head>
<body>
    <div class="form-container">
        <h2>Student Laptop History</h2>
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
                <th>Serial Number</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Laptop Checked Out</th>
                <th>Charger Cord Checked Out</th>
                <th>Checkout Date</th>
                <th>Checkin Date</th>
                <th>Charger Cord Returned</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $studentNumber; ?></td>
                <td><?php echo $row['serial_number']; ?></td>
                <td><?php echo $firstName; ?></td>
                <td><?php echo $lastName; ?></td>
                <td><?php echo ($row['laptop_checkedout'] == 1) ? 'Yes' : 'No'; ?></td>
                <td><?php echo ($row['charger_cord_checkedout'] == 1) ? 'Yes' : 'No'; ?></td>
                <td><?php echo formatDate($row['checkout_date']); ?></td>
                <td><?php echo formatDate($row['checkin_date']); ?></td>
                <!-- <td><?php echo $row['checkout_date']; ?></td>
                <td><?php echo $row['checkin_date']; ?></td> -->
                <td><?php echo ($row['charger_cord_returned'] == 1) ? 'Yes' : 'No'; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <?php } ?>
</body>
</html>

