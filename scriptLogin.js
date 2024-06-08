function validateForm() {
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var errorMessage = "";

    if (!emailRegex.test(email)) {
        errorMessage += "Please enter a valid email address.\n";
    }

    if (errorMessage !== "") {
        alert(errorMessage);
        return false;
    }

    // Other validations can be added here

    return true;
}
