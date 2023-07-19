<!DOCTYPE html>
<html>
<head>
    <title>Laptop Check-In/Check-Out</title>
    <link rel="icon" href="hawk.jpg" type="image/jpg">
    <link rel="stylesheet" href="CSS/index.css">    
</head>
<body>
    <!-- Menu Bar -->
    <div class="menu-bar">
        <ul>
            <li>
                <a href="#">History Reports</a>
                <ul class="sub-menu">
                    <li><a href="laptop_history.php">Laptop History</a></li>
                    <li><a href="student_history.php">Student History</a></li>
                </ul>
            </li>
            <li>
                <a href="#">Cord Replacement/Loaner</a>
                <ul class="sub-menu">
                    <li><a href="replace_loanCord.php">Re-Issue Cord</a></li>
                    <li><a href="checkin_cord.php">Charger Cord Checkin</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <div id="mascot-image">
        <img src="hawk.jpg" alt="Hawk Image" class="hawk-image">
    </div>

    <div class="container">   
        <h1>Welcome to the Laptop Check-In/Check-Out System</h1>
        
        <form method="post" action="index.php">
            <div class="radio-container">
                <input type="radio" id="checkIn" name="laptopStatus" value="checkIn">
                <label for="checkIn" class="radio-label">Check In Laptop</label>
            </div>
            
            <div class="radio-container">
                <input type="radio" id="checkOut" name="laptopStatus" value="checkOut">
                <label for="checkOut" class="radio-label">Check Out Laptop</label>
            </div>
            
            <button class="start-button" type="submit" name="submit">Start</button>
        </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        // Check if the radio button is selected
        if (isset($_POST['laptopStatus'])) {
            $selectedOption = $_POST['laptopStatus']; 

            // Redirect based on the selected option
            if ($selectedOption === 'checkIn') {
                header('Location: checkin.php');
                exit();
            } elseif ($selectedOption === 'checkOut') {
                header('Location: checkout.php');
                exit();
            }
        }
    }
    ?>
</body>
</html>
