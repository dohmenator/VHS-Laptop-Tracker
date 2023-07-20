// checkin_charger_cord.js

// Function to fetch student information from the "getStudentName.php" file based on the entered student number
function getStudent() {
    var studentNumber = document.getElementById('studentNumber').value;
    if (studentNumber.trim() === '') {
        alert('Please enter a student number');
        return;
    }

    var url = 'getStudentName.php';

    fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'studentNumber=' + encodeURIComponent(studentNumber)
        })
        .then(function(response) {
            if (response.ok) {
                return response.json();
            } else {
                throw new Error('Error: ' + response.status);
            }
        })
        .then(function(data) {
            if (data.firstName && data.lastName) {
                document.getElementById('firstName').value = data.firstName;
                document.getElementById('lastName').value = data.lastName;
                document.getElementById('checkInForm').style.display = 'block';
                document.getElementById('getStudentForm').getElementsByTagName('button')[0].disabled = true;
            } else {
                alert("Student not found in the database because student does not have a laptop checked out. Sending you back to the home page to check out a laptop to the student");
                window.location.href = "index.php";

                // alert("Student not found. Manually enter student's first and last name");
                // document.getElementById('firstName').readOnly = false;
                // document.getElementById('lastName').readOnly = false;
                // document.getElementById('firstName').focus();
            }
        })
        .catch(function(error) {
            alert('Error: ' + error.message);
        });
}


// Function to handle the form submission and check the selected action
function handleFormSubmission(event) {
    event.preventDefault();
    const selectedAction = document.querySelector("input[name='action']:checked");
    if (!selectedAction) {
        showError("Please select an action (Loaner or Replacement).");
        return;
    }

    // Perform the check-in based on the selected action (Loaner or Replacement)
    const action = selectedAction.value;
    const studentNumber = document.getElementById("studentNumber").value;
    const firstName = document.getElementById("firstName").value;
    const lastName = document.getElementById("lastName").value;
    const chargerCheckedIn = action === "loaner" ? 1 : 0;
    const replacementCheckedIn = action === "replacement" ? 1 : 0;

    // Perform AJAX request to update the record in the Replacement_Loaner_Cords table
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "checkin_charger_cord.php");
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Display success message
            const message = `Cord successfully checked in for ${firstName} ${lastName} as a ${action}.`;
            showError(message);
        } else {
            showError("Failed to check in the cord.");
        }
    };
    xhr.send(`studentNumber=${encodeURIComponent(studentNumber)}&action=${encodeURIComponent(action)}&firstName=${encodeURIComponent(firstName)}&lastName=${encodeURIComponent(lastName)}`);
}

// Function to handle the "Home" button click
function goToHome() {
    window.location.href = "index.php";
}

// Function to show error messages
function showError(message) {
    const errorElement = document.getElementById("error");
    errorElement.innerText = message;
    errorElement.style.display = "block";
    setTimeout(() => {
        errorElement.style.display = "none";
    }, 3000);
}

// // Attach event listener to "Check In Cord" button after DOM is fully loaded
// document.addEventListener("DOMContentLoaded", function() {
//     document.getElementById("checkInForm").getElementsByTagName("button")[0].addEventListener("click", handleFormSubmission);
// });

// // Attach event listener to "Home" button after DOM is fully loaded
// document.addEventListener("DOMContentLoaded", function() {
//     document.getElementById("checkInForm").getElementsByTagName("button")[1].addEventListener("click", goToHome);
// });

document.getElementById("checkInForm").addEventListener("submit", handleFormSubmission);
document.getElementById("checkInForm").getElementsByTagName("button")[0].addEventListener("click", handleFormSubmission);