<?php
require "connectToDatabase.php";
$firstName = "";
$lastName = "";
$chargerCheckedOut = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $laptopSerialNumber = $_POST['laptopSerialNumber'];
    $studentNumber = $_POST['studentNumber'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $laptopCheckedOut = 1;
    $chargerCheckedOut = isset($_POST['chargerCheckedOut']) && $_POST['chargerCheckedOut'] === 'yes';
    $haveCharger = (int)$chargerCheckedOut ? "with" : "without";

    // Sanitize the form data
    $laptopSerialNumber = htmlspecialchars($laptopSerialNumber);
    $studentNumber = htmlspecialchars($studentNumber);
    $firstName = htmlspecialchars($firstName);
    $lastName = htmlspecialchars($lastName);

    // Connect to the database
    $conn = connectToDatabase();

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    // Check if the student already exists in the studentdata table
    $sql = "SELECT * FROM studentdata WHERE student_number = '$studentNumber'";
    $result = $conn->query($sql);

    if ($result->num_rows === 0) {
        // Student doesn't exist, insert the student into the studentdata table
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];

        $sql = "INSERT INTO studentdata (student_number, first_name, last_name) VALUES ('$studentNumber', '$firstName', '$lastName')";
        if ($conn->query($sql) !== TRUE) {
            $message = 'Failed to insert student data.';
        }
    }

    // Update or insert the laptop data into the laptopdata table
    $sql = "SELECT * FROM laptopdata WHERE serial_number = '$laptopSerialNumber'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Laptop serial number already exists, update the record
        $sql = "UPDATE laptopdata 
                SET laptop_checkedout = 1,
                    charger_cord_checkedout = " . (int)$chargerCheckedOut . ",
                    checkout_date = NOW(),
                    student_number = '$studentNumber'
                WHERE serial_number = '$laptopSerialNumber'";

        if ($conn->query($sql) === TRUE) {
            //$message = 'Laptop with serial number ' . $laptopSerialNumber . ' has been successfully checked out.';
            $message = "New laptop successfully checked out to {$firstName} {$lastName} {$haveCharger} charger";
        } else {
            $message = 'Failed to update laptop check-out status.';
        }
    } else {
        // Laptop serial number doesn't exist, insert a new record
        $sql = "INSERT INTO laptopdata (serial_number, laptop_checkedout, charger_cord_checkedout, checkout_date, student_number) VALUES ('$laptopSerialNumber', " . (int)$laptopCheckedOut . ", " . (int)$chargerCheckedOut . ", NOW(), '$studentNumber')";
        
        
        if ($conn->query($sql) === TRUE) {
            $message = "New laptop successfully checked out to {$firstName} {$lastName} {$haveCharger} charger";
        } else {
            $message = 'Failed to insert new laptop check-out record. (Student has previous laptop checked out)';
        }

    }

    $conn->close();
} else {
    $message = ''; // Initialize an empty message
}

?>

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
