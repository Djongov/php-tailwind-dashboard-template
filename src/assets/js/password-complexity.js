
document.addEventListener("DOMContentLoaded", function() {
  const passwordInput = document.getElementById("sign-in-password");

    passwordInput.addEventListener("input", function() {
    const password = passwordInput.value;
    const lengthCheck = password.length >= 8;
    const uppercaseCheck = /[A-Z]/.test(password);
    const lowercaseCheck = /[a-z]/.test(password);
    const digitCheck = /[0-9]/.test(password);
    const specialCharCheck = /[!@#$%^&*]/.test(password);

    const isPasswordValid = lengthCheck && uppercaseCheck && lowercaseCheck && digitCheck && specialCharCheck;

    // Display a message based on password validity
    const passwordStrengthMessage = document.getElementById("password-strength-message");
    if (isPasswordValid) {
        passwordStrengthMessage.textContent = "Password is strong!";
    passwordStrengthMessage.style.color = "green";
    } else {
        passwordStrengthMessage.textContent = "Password must meet the criteria.";
    passwordStrengthMessage.style.color = "red";
    }
  });
});
