// Function to fetch student information from CSV file based on the entered student number
function getStudent() {
    const studentNumber = document.getElementById("studentNumber").value;
    if (studentNumber.trim() === "") {
        showError("Please enter a valid student number.");
        return;
    }

    // Perform AJAX request to fetch student information from CSV file
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "../Student Data.csv");
    xhr.onload = function() {
        if (xhr.status === 200) {
            const csvData = xhr.responseText;
            const lines = csvData.split("\n");
            let foundStudent = false;
            let firstName = "";
            let lastName = "";

            // Search for the student in the CSV data
            for (const line of lines) {
                const data = line.split(",");
                if (data[0].trim() === studentNumber) {
                    foundStudent = true;
                    firstName = data[1].trim();
                    lastName = data[2].trim();
                    break;
                }
            }

            if (foundStudent) {
                // Update the first name and last name fields
                document.getElementById("firstName").value = firstName;
                document.getElementById("lastName").value = lastName;
                // Show the form for Replacement/Lend action
                document.getElementById("replaceLoanForm").style.display = "block";
                // Disable the Get Student button
                document.getElementById("getStudentForm").getElementsByTagName("button")[0].disabled = true;
            } else {
                showError("Student with the entered number doesn't exist.");
            }
        } else {
            showError("Failed to fetch student information.");
        }
    };
    xhr.send();
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

// Function to handle form submission and check the selected action
// document.getElementById("replaceLoanForm").addEventListener("submit", function(event) {
//     event.preventDefault();
//     const selectedAction = document.querySelector("input[name='action']:checked");
//     if (!selectedAction) {
//         showError("Please select an action (Replacement or Loaner).");
//         return;
//     }

//     // Perform the checkout based on the selected action (Replacement or Loaner)
//     const action = selectedAction.value;
//     const studentNumber = document.getElementById("studentNumber").value;
//     const firstName = document.getElementById("firstName").value;
//     const lastName = document.getElementById("lastName").value;
//     const chargerCheckedOut = action === "replacement"; // If action is "replacement", charger is checked out.

//     // You can perform the checkout action here using AJAX or other methods as needed.

//     // Display a success message
//     const message = `Cord successfully checked out to ${firstName} ${lastName} as a ${action}.`;
//     showError(message);
// });

// Function to handle form submission and check the selected action
document.getElementById("replaceLoanForm").addEventListener("submit", function(event) {
    event.preventDefault();
    const selectedAction = document.querySelector("input[name='action']:checked");
    if (!selectedAction) {
        showError("Please select an action (Replacement or Loaner).");
        return;
    }

    // Perform the checkout based on the selected action (Replacement or Loaner)
    const action = selectedAction.value;
    const studentNumber = document.getElementById("studentNumber").value;
    const firstName = document.getElementById("firstName").value;
    const lastName = document.getElementById("lastName").value;
    const chargerCheckedOut = action === "replacement"; // If action is "replacement", charger is checked out.

    // Perform AJAX request to insert a new record into the Replacement_Loaner_Cords table
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "replace_loanCord.php");
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Display success message
            const message = `Cord successfully checked out to ${firstName} ${lastName} as a ${action}.`;
            showError(message);
        } else {
            showError("Failed to check out the cord.");
        }
    };
    xhr.send(`studentNumber=${encodeURIComponent(studentNumber)}&action=${encodeURIComponent(action)}&firstName=${encodeURIComponent(firstName)}&lastName=${encodeURIComponent(lastName)}`);
});

// Function to handle the "Home" button click
function goToHome() {
    window.location.href = "index.php";
}


// Attach event listener to "Get Student" button after DOM is fully loaded
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("getStudentForm").getElementsByTagName("button")[0].addEventListener("click", getStudent);
});

// Attach event listener to "Home" button after DOM is fully loaded
document.addEventListener("onload", function() {
    document.getElementById("replaceLoanForm").getElementsByTagName("button")[1].addEventListener("click", goToHome);
});