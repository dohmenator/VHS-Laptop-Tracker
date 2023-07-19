window.addEventListener('DOMContentLoaded', function() {
    // Set focus to the laptop serial number input field
    document.getElementById('serialNumber').focus();
});

function validateForm(event) {
    var chargerRadios = document.getElementsByName('chargerRadio');
    var isChargerChecked = false;
    // alert(chargerRadios);
    for (var i = 0; i < chargerRadios.length; i++) {
        if (chargerRadios[i].checked) {
            isChargerChecked = true;
            break;
        }
    }

    if (!isChargerChecked) {
        document.getElementById('error').innerHTML = 'Please select an option for Charger Cord Check-In';
        event.preventDefault(); // Prevent form submission
        return false;
    }

    return true;
}

function goToHome() {
    window.location.href = 'index.php';
}