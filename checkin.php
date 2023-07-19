<?php
require "connectToDatabase.php";
$laptopSerialNumber = "";
$firstName = "";
$lastName = "";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['serialNumber'])) {
        $laptopSerialNumber = $_GET['serialNumber'];

        // Sanitize the serial number
        $laptopSerialNumber = htmlspecialchars($laptopSerialNumber);

        // Connect to the database
        $conn = connectToDatabase();

        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }

        // Perform the inner join query to get the student's first and last name
        $sql = "SELECT LaptopData.serial_number, StudentData.first_name, StudentData.last_name
                FROM LaptopData
                INNER JOIN StudentData ON LaptopData.student_number = StudentData.student_number
                WHERE LaptopData.serial_number = '$laptopSerialNumber' AND LaptopData.checkin_date IS NULL";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $firstName = $row['first_name'];
            $lastName = $row['last_name'];
        } else {
            // No student found with the given serial number
            $message = "No student found associated with that serial number";
        }

        $conn->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $laptopSerialNumber = $_POST['laptopSerialNumber'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $chargerCheckedIn = 0;
    
    if (isset($_POST['chargerRadio'])) {
        //var_dump($_POST['chargerRadio']); //for debugging purposes only
        $chargerCheckedIn = $_POST['chargerRadio'] === 'yes' ? 1 : 0;
    }
    //var_dump($chargerCheckedIn);  //for debugging purposes only
    
    // Connect to the database
    $conn = connectToDatabase();

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    // Check if the laptop exists in the LaptopData table
    $sql = "SELECT * FROM LaptopData WHERE serial_number = '$laptopSerialNumber'AND LaptopData.checkin_date IS NULL";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Laptop exists, update the check-in status and student data

        // Update the LaptopData table based on the laptop serial number
        $sql = "UPDATE LaptopData SET laptop_checkedout = 0, charger_cord_returned = " . (int)$chargerCheckedIn . ", checkin_date = NOW() WHERE serial_number = '$laptopSerialNumber'AND LaptopData.checkin_date IS NULL";
        $conn->query($sql);

        // Retrieve the student number based on the first name and last name
        $sql = "SELECT student_number FROM StudentData WHERE first_name = '$firstName' AND last_name = '$lastName'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $studentNumber = $row['student_number'];

            // Update the student number in the LaptopData table ??? do I need AND LaptopData.checkin_date IS NULL
            $sql = "UPDATE LaptopData SET student_number = '$studentNumber' WHERE serial_number = '$laptopSerialNumber'AND LaptopData.checkin_date IS NULL";
            $conn->query($sql);

            // Display success message
            $message = 'Laptop with serial number ' . $laptopSerialNumber . ' has been successfully checked in.';
        } else {
            // No student found with the given first name and last name
            $message = 'Student not found.';
        }
    } else {
        // Laptop doesn't exist in the LaptopData table
        $message = 'No laptop found with the given serial number.';
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Check-In Page</title>
    <link rel="stylesheet" href="CSS/checkin_out.css">
    <script src="JS/checkin.js"></script>
</head>
<body>
    <div class="form-container">
        <h1>Laptop Check In</h1>
        <?php if (!empty($message)) : ?>
            <div class="message"><p class="messageBox"><?php echo $message; ?></p></div>
        <?php endif; ?>

        <form method="get" action="checkin.php" id="getStudentForm">
            <div class="form-field">
                <label for="serialNumber">Laptop Serial Number:</label>
                <input type="text" id="serialNumber" name="serialNumber" required placeholder="Scanned serial number will appear here">
            </div>

            <div class="form-field">
                <p>
                    <input type="submit" value="Get Student">
                    <button type="button" onclick="goToHome()">Home</button>
            </p>
            </div>
        </form>
    </div>

    <div class="form-container">
        <!-- <?php if ($message !== "") : ?>
            <div class="message"><p class='messageBox'><?php echo $message; ?></p></div>
        <?php else : ?> -->
            <form method="post" action="checkin.php" id="checkInForm">
                <input type="hidden" id="laptopSerialNumber" name="laptopSerialNumber" class="form-input" value="<?php echo $laptopSerialNumber; ?>">
                <div class="form-field">
                    <label for="firstName" class="form-label">First Name:</label>
                    <input type="text" id="firstName" name="firstName" class="form-input" required value="<?php echo $firstName; ?>">
                </div>
                
                <div class="form-field">
                    <label for="lastName" class="form-label">Last Name:</label>
                    <input type="text" id="lastName" name="lastName" class="form-input" required value="<?php echo $lastName; ?>">
                </div>
                
                <div class="form-field">
                    <label class="form-label">Charger Cord Check-In:</label>
                    <div class="form-radio-group-horizontal">
                        <input type="radio" id="chargerYes" name="chargerRadio" value="yes">
                        <label for="chargerYes">Yes</label>
                        
                        <input type="radio" id="chargerNo" name="chargerRadio" value="no">
                        <label for="chargerNo">No</label>
                    </div>
                </div>
                
                <div class="form-field">
                    <input type="submit" value="Check In" class="form-button">
                    <input type="reset" value="Reset" class="form-button">
                    <button type="button" class='form-button' onclick="goToHome()">Home</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
    
    
</body>
</html>
