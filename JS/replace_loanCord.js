// Function to fetch student information from CSV file based on the entered student number
function getStudent() {
    var studentNumber = document.getElementById('studentNumber').value;
    if (studentNumber.trim() === '') {
        alert('Please enter a student number');
        return;
    }

    var url = '../Student Data.csv';

    fetch(url)
        .then(function(response) {
            if (response.ok) {
                return response.text();
            } else {
                throw new Error('Error: ' + response.status);
            }
        })
        .then(function(csvData) {
            var students = processData(csvData);
            var student = findStudent(students, studentNumber);

            if (student) {
                document.getElementById('firstName').value = student.firstName;
                document.getElementById('lastName').value = student.lastName;
                document.getElementById('replaceLoanForm').style.display = 'block';
                document.getElementById('getStudentForm').getElementsByTagName('button')[0].disabled = true;

                // Disable the input fields for first and last name
                document.getElementById('firstName').disabled = true;
                document.getElementById('lastName').disabled = true;
            } else {
                // Show the alert message and enable manual entry
                alert("Student with the entered number doesn't exist. Please manually enter the student's first and last name.");
                document.getElementById('firstName').value = '';
                document.getElementById('lastName').value = '';
                document.getElementById('replaceLoanForm').style.display = 'block';
                document.getElementById('getStudentForm').getElementsByTagName('button')[0].disabled = true;

                // Enable the input fields for first and last name
                document.getElementById('firstName').readOnly = false;
                document.getElementById('lastName').readOnly = false;
                document.getElementById('firstName').focus();
            }
        })
        .then(function() {
            // Enable the checkout button
            document.getElementById('checkoutButton').disabled = false;
        })
        .catch(function(error) {
            alert('Error: ' + error.message);
        });
}


function processData(csvData) {
    var lines = csvData.split('\n');
    var students = [];

    for (var i = 1; i < lines.length; i++) {
        var fields = lines[i].split(',');
        var student = {
            studentNumber: fields[0],
            firstName: fields[1],
            lastName: fields[2]
        };
        students.push(student);
    }

    return students;
}

function findStudent(students, studentNumber) {
    for (var i = 0; i < students.length; i++) {
        if (students[i].studentNumber === studentNumber) {
            return students[i];
        }
    }

    return null;
}

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