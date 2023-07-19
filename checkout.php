<?php
require "connectToDatabase.php";

$studentNumber = "";
$firstName = "";
$lastName = "";
$laptopSerialNumber = "";
$chargerCheckedOut = false;
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentNumber = $_POST['studentNumber'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $laptopSerialNumber = $_POST['laptopSerialNumber'];
    $chargerCheckedOut = isset($_POST['chargerCheckedOut']) && $_POST['chargerCheckedOut'] === 'yes';

    // Sanitize the inputs
    $studentNumber = htmlspecialchars($studentNumber);
    $firstName = htmlspecialchars($firstName);
    $lastName = htmlspecialchars($lastName);
    $laptopSerialNumber = htmlspecialchars($laptopSerialNumber);

    // Connect to the database
    $conn = connectToDatabase();

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    // Check if the student exists in StudentData table
    $checkStudentQuery = "SELECT student_number FROM StudentData WHERE student_number = ?";
    $stmt = $conn->prepare($checkStudentQuery);
    $stmt->bind_param("s", $studentNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the student doesn't exist, insert them into StudentData table
    if ($result->num_rows === 0) {
        $insertStudentQuery = "INSERT INTO StudentData (student_number, first_name, last_name) 
                               VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertStudentQuery);
        $stmt->bind_param("sss", $studentNumber, $firstName, $lastName);
        if ($stmt->execute()) {
            // Display success message for student insert
            $message = "New student record added.";
        } else {
            // Display error message for student insert
            $message = "Failed to insert new student record.";
        }
    }

    // Insert a new record into the LaptopData table
    $insertLaptopQuery = "INSERT INTO LaptopData (student_number, serial_number, laptop_checkedout, charger_cord_checkedout, checkout_date) 
                          VALUES (?, ?, 1, ?, NOW())";
    $stmt = $conn->prepare($insertLaptopQuery);
    $chargerCheckedOutValue = ($chargerCheckedOut) ? 1 : 0;
    print "chargerCheckedOut value: ".$chargerCheckedOutValue;
    // The "ssi" format in bind_param() specifies that the first two placeholders are strings (s) and the third one is an integer (i).
    $stmt->bind_param("ssi", $studentNumber, $laptopSerialNumber, $chargerCheckedOutValue);

    if ($stmt->execute()) {
        // Display success message for laptop checkout
        $message = "New laptop successfully checked out to $firstName $lastName " . ($chargerCheckedOut ? 'with' : 'without') . " charger.";
    } else {
        // Display error message for laptop checkout
        $message = "Failed to insert new laptop check-out record.";
    }

    $conn->close();
}
?>
<!-- Rest of your HTML and form code -->


<!-- HTML for checkout form -->
<!DOCTYPE html>
<html>
<head>
    <title>Checkout Page</title>
    <link rel="stylesheet" href="CSS/checkin_out.css">
    <link rel="icon" href="hawk.jpg" type="image/jpg">
</head>
<body>
    <div class="form-container">
        <h1>Laptop Check Out</h1>

        <?php if (!empty($message)) : ?>
            <div class="message"><p class="messageBox"><?php echo $message; ?></p></div>
        <?php endif; ?>

        <form method="post" action="" onsubmit="return validateForm()">
            <div class="form-field">
                <label for="studentNumber" class="form-label">Student Number:</label>
                <input type="text" id="studentNumber" name="studentNumber" class="form-input" autofocus required placeholder="Scanned student number will appear here">
                <p><button type="button" onclick="getStudent()">Get Student</button></p>
            </div>

            <div class="form-field">
                <label for="firstName" class="form-label">First Name:</label>
                <input type="text" id="firstName" name="firstName" class="form-input" required>
            </div>

            <div class="form-field">
                <label for="lastName" class="form-label">Last Name:</label>
                <input type="text" id="lastName" name="lastName" class="form-input" required>
            </div>

            <div class="form-field">
                <label for="laptopSerialNumber" class="form-label">Laptop Serial Number:</label>
                <input type="text" id="laptopSerialNumber" name="laptopSerialNumber" class="form-input" required placeholder="Scanned serial number will appear here">
            </div>

            <div class="form-field">
                <label class="form-label">Charger Cord Checked Out:</label>
                <div class="form-radio-group-horizontal">
                    <input type="radio" id="chargerYes" name="chargerCheckedOut" value="yes">
                    <label for="chargerYes">Yes</label>

                    <input type="radio" id="chargerNo" name="chargerCheckedOut" value="no">
                    <label for="chargerNo">No</label>
                </div>
            </div>

            <div class="form-field">
                <input type="submit" value="Check Out" class="form-button" id="checkoutButton" disabled>
                <input type="reset" value="Reset" class="form-button">
                <button type="button" class="form-button" onclick="goToHome()">Home</button>
            </div>

            <div id="error" class="error-message"></div>
        </form>
    </div>
    <script src="JS/checkout.js"></script>
</body>
</html>
