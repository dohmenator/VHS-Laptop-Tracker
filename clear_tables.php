<?php
// Include the database connection file
require "connectToDatabase.php";

// Function to execute SQL query
function executeQuery($conn, $query) {
    if ($conn->query($query) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Connect to the database
$conn = connectToDatabase();

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Check if the "Clear" button is clicked
if (isset($_POST['clear'])) {
    // Clear the LaptopData table
    $clearLaptopDataQuery = "DELETE FROM LaptopData";
    $resultLaptopData = executeQuery($conn, $clearLaptopDataQuery);

    // Clear the StudentData table
    $clearStudentDataQuery = "DELETE FROM StudentData";
    $resultStudentData = executeQuery($conn, $clearStudentDataQuery);

    // Clear the Replacement_Loaner_Cords table
    $clearReplacementLoanerCordsQuery = "DELETE FROM Replacement_Loaner_Cords";
    $resultReplacementLoanerCords = executeQuery($conn, $clearReplacementLoanerCordsQuery);

    if ($resultLaptopData && $resultStudentData && $resultReplacementLoanerCords) {
        $message = "All tables cleared successfully.";
    } else {
        $message = "An error occurred while clearing the tables.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Clear Tables</title>
</head>
<body>
    <h1>Clear Tables</h1>
    <form method="post">
        <input type="submit" name="clear" value="Clear Tables">
    </form>

    <?php if (isset($message)) { ?>
        <p><?php echo $message; ?></p>
    <?php } ?>
</body>
</html>
