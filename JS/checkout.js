window.addEventListener('DOMContentLoaded', function() {
    document.getElementById('studentNumber').focus();
});

function validateForm() {
    var radios = document.getElementsByName('chargerCheckedOut');
    var isValid = false;

    for (var i = 0; i < radios.length; i++) {
        if (radios[i].checked) {
            isValid = true;
            break;
        }
    }

    if (!isValid) {
        document.getElementById('error').innerHTML = 'Please select an option for Charger Cord Checked Out';
        return false;
    }

    return true;
}

function goToHome() {
    window.location.href = 'index.php';
}

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
                document.getElementById('laptopSerialNumber').focus();
            } else {
                alert("Student not found. Manually enter student's first and last name");
                document.getElementById('firstName').focus();
            }
        })
        .then(function() {
            // Enable the checkout button
            document.getElementById("checkoutButton").disabled = false;
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