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

function resetForms() {
    // Reset all form fields except for specific fields
    var fieldsToExclude = ['laptopSerialNumber', 'firstName', 'lastName'];
    var forms = document.getElementsByTagName('form');

    for (var i = 0; i < forms.length; i++) {
        var form = forms[i];
        var inputs = form.getElementsByTagName('input');

        for (var j = 0; j < inputs.length; j++) {
            var input = inputs[j];

            if (fieldsToExclude.indexOf(input.id) === -1) {
                input.value = '';
            }
        }
    }
}