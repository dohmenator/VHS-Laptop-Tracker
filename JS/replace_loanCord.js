// replace_loanCord.js

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
                document.getElementById('replaceLoanForm').style.display = 'block';
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

// Rest of the existing code in replace_loanCord.js
// ...

// Function to handle the form submission and check the selected action
function handleFormSubmission(event) {
    event.preventDefault();
    var selectedAction = document.querySelector("input[name='action']:checked");
    if (!selectedAction) {
        showError("Please select an action (Replacement or Loaner).");
        return;
    }

    var action = selectedAction.value;
    var studentNumber = document.getElementById("studentNumber").value;
    var firstName = document.getElementById("firstName").value;
    var lastName = document.getElementById("lastName").value;
    var chargerCheckedOut = action === "replacement";

    // Perform AJAX request to insert a new record into the Replacement_Loaner_Cords table
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "replace_loanCord.php");
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (xhr.status === 200) {
            var message = "Cord successfully checked out to " + firstName + " " + lastName + " as a " + action + ".";
            showError(message);
        } else {
            showError("Failed to check out the cord.");
        }
    };
    xhr.send("studentNumber=" + encodeURIComponent(studentNumber) + "&action=" + encodeURIComponent(action) + "&firstName=" + encodeURIComponent(firstName) + "&lastName=" + encodeURIComponent(lastName));
}

// Function to handle the "Home" button click
function goToHome() {
    window.location.href = "index.php";
}

// Function to show error messages
function showError(message) {
    var errorElement = document.getElementById("error");
    errorElement.innerText = message;
    errorElement.style.display = "block";
    setTimeout(function() {
        errorElement.style.display = "none";
    }, 3000);
}


document.getElementById("replaceLoanForm").addEventListener("submit", handleFormSubmission);