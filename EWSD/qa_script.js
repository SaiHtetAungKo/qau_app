// password visibility for login and change password

function passwordVisibility(inputId, eyeIcon) {
    var input = document.getElementById(inputId);

    if (input.type === "password") {
        input.type = "text";
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    } else {
        input.type = "password";
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    }
} 

//check email format
function validateEmailFormat() {
    let email = document.getElementById("txtEmail").value;
    let validation = document.getElementById("emailValidation");

    let emailFormat = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{3,}$/;

    if (emailFormat.test(email)) {
        validation.textContent = "✔ Valid Email Format!";
        validation.className = "valid";
    } else {
        validation.textContent = "✖ Invalid Email Format! Eg. user@example.com";
        validation.className = "invalid";
    }
}

function checkPasswordStrength() {
    let password = document.getElementById("txtPassword").value;
    let strengthText = document.getElementById("passwordStrength");
    let registerBtn = document.getElementById("registerBtn");

    // Conditions
    let hasLength = password.length >= 8;
    let hasUppercase = /[A-Z]/.test(password);
    let hasNumber = /[0-9]/.test(password);
    let hasSpecial = /[\W_]/.test(password);

    // Update checklist
    document.getElementById("length").className = hasLength ? "valid" : "invalid";
    document.getElementById("uppercase").className = hasUppercase ? "valid" : "invalid";
    document.getElementById("number").className = hasNumber ? "valid" : "invalid";
    document.getElementById("special").className = hasSpecial ? "valid" : "invalid";

    // Check overall strength
    let strength = hasLength + hasUppercase + hasNumber + hasSpecial;

    if (strength === 0) {
        strengthText.textContent = "Password is too weak!";
        strengthText.className = "weak";
        registerBtn.disabled = true;
    } else if (strength < 3) {
        strengthText.textContent = "Weak password! Improve it.";
        strengthText.className = "weak";
        registerBtn.disabled = true;
    } else if (strength === 3) {
        strengthText.textContent = "Medium password! Add more security.";
        strengthText.className = "medium";
        registerBtn.disabled = false;
    } else {
        strengthText.textContent = "Strong password!";
        strengthText.className = "strong";
        registerBtn.disabled = false;
    }
}