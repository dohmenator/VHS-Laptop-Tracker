<!DOCTYPE html>
<html>
<head>
    <title>Laptop History Report</title>
    <link rel="stylesheet" href="CSS/checkin_out.css">
    <link rel="stylesheet" href="CSS/reports.css">
   
    <script src="JS/checkin.js"></script>
</head>
<body>
    <div class="form-container">
        <h2>Laptop History Report</h2>
        <form method="post" action="">
            <div class="form-field">
                <label for="serialNumber" class="form-label">Enter Laptop Serial Number:</label>
                <input type="text" id="serialNumber" name="serialNumber" class="form-input" required>
            </div>
            <div class="form-field">
                <input type="submit" value="Run Report" class="form-button">
                <button type="button" class='form-button' onclick="goToHome()">Home</button>
            </div>
        </form>
    </div>

    <?php
    require "connectToDatabase.php";
    
    function formatDate($dateTimeString) {
        $dateTime = new DateTime($dateTimeString);
        return $dateTime->format('F j, Y, g:i A');
    }
    
    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Rest of your PHP code for generating the report...
         // Retrieve the form data
         $laptopSerialNumber = $_POST['serialNumber'];
            
         // Sanitize the serial number
         $laptopSerialNumber = htmlspecialchars($laptopSerialNumber);
         
         // Connect to the database      
         $conn = connectToDatabase();
         
         if ($conn->connect_error) {
             die('Connection failed: ' . $conn->connect_error);
         }
         
         // Perform the inner join query to get the laptop history report
        //  $sql = "SELECT LaptopData.laptop_checkedout, LaptopData.charger_cord_checkedout, 
        //                 LaptopData.checkout_date, LaptopData.checkin_date, LaptopData.charger_cord_returned, 
        //                 StudentData.student_number, StudentData.first_name, StudentData.last_name
        //          FROM LaptopData
        //          INNER JOIN StudentData ON LaptopData.student_number = StudentData.student_number
        //          WHERE LaptopData.serial_number = '$laptopSerialNumber'
        //          GROUP BY StudentData.student_number";
        $sql = "SELECT LaptopData.laptop_checkedout, LaptopData.charger_cord_checkedout, 
            LaptopData.checkout_date, LaptopData.checkin_date, LaptopData.charger_cord_returned, 
            StudentData.student_number, StudentData.first_name, StudentData.last_name
            FROM LaptopData
            INNER JOIN StudentData ON LaptopData.student_number = StudentData.student_number
            WHERE LaptopData.serial_number = '$laptopSerialNumber'";

         $result = $conn->query($sql);
    ?>
    <div class="table-container">
        <?php if ($result->num_rows > 0) : ?>
        <table class="report-table">
            <tr>
                <th>Serial Number</th>
                <th>Student Number</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Laptop Checked Out</th>
                <th>Charger Cord Checked Out</th>
                <th>Checkout Date</th>
                <th>Checkin Date</th>
                <th>Charger Cord Returned</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $laptopSerialNumber; ?></td>
                <td><?php echo $row['student_number']; ?></td>
                <td><?php echo $row['first_name']; ?></td>
                <td><?php echo $row['last_name']; ?></td>
                <td><?php echo ($row['laptop_checkedout'] == 1) ? 'Yes' : 'No'; ?></td>
                <td><?php echo ($row['charger_cord_checkedout'] == 1) ? 'Yes' : 'No'; ?></td>
                <td><?php echo formatDate($row['checkout_date']); ?></td>
                <td><?php echo formatDate($row['checkin_date']); ?></td>
                <td><?php echo ($row['charger_cord_returned'] == 1) ? 'Yes' : 'No'; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else : ?>
        <p>No laptop history found for the entered serial number.</p>
        <?php endif; ?>
    </div>
    <?php
    }
    ?>
   
</body>
</html>