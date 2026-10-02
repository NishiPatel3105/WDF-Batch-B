```javascript
const form = document.getElementById("form");

const name = document.getElementById("name");
const email = document.getElementById("email");
const mobile = document.getElementById("mobile");
const password = document.getElementById("password");
const confirm = document.getElementById("confirm");
const course = document.getElementById("course");
const year = document.getElementById("year");
const terms = document.getElementById("terms");

const nameReg = /^[A-Za-z ]{3,}$/;
const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const mobileReg = /^[6-9]\d{9}$/;
const passReg = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;


function error(id, message) {
    document.getElementById(id).textContent = message;
}

function validate() {
    let valid = true;

    error("nameError", "");
    error("emailError", "");
    error("mobileError", "");
    error("passwordError", "");
    error("confirmError", "");
    error("courseError", "");
    error("yearError", "");
    error("genderError", "");
    error("termsError", "");

    if (!nameReg.test(name.value.trim())) {
        error("nameError", "Enter a valid name.");
        valid = false;
    }

    if (!emailReg.test(email.value.trim())) {
        error("emailError", "Enter a valid email.");
        valid = false;
    }

    if (!mobileReg.test(mobile.value.trim())) {
        error("mobileError", "Enter a valid 10-digit mobile.");
        valid = false;
    }

    if (!passReg.test(password.value)) {
        error("passwordError",
            "Use 8+ chars, uppercase, lowercase, number & symbol.");
        valid = false;
    }

    if (password.value !== confirm.value) {
        error("confirmError", "Passwords do not match.");
        valid = false;
    }

    if (course.value === "") {
        error("courseError", "Select a course.");
        valid = false;
    }

    if (year.value === "") {
        error("yearError", "Select your year.");
        valid = false;
    }

    if (!document.querySelector('input[name="gender"]:checked')) {
        error("genderError", "Select your gender.");
        valid = false;
    }

    if (!terms.checked) {
        error("termsError", "Accept the terms.");
        valid = false;
    }

    return valid;
}


/* Password strength */
password.addEventListener("input", function () {

    let p = password.value;

    if (p.length < 6)
        document.getElementById("strength").textContent =
            "Password strength: Weak";

    else if (p.length < 8)
        document.getElementById("strength").textContent =
            "Password strength: Medium";

    else
        document.getElementById("strength").textContent =
            "Password strength: Strong";
});


/* Real-time validation */
name.addEventListener("input", validate);
email.addEventListener("input", validate);
mobile.addEventListener("input", validate);
password.addEventListener("input", validate);
confirm.addEventListener("input", validate);
course.addEventListener("change", validate);
year.addEventListener("change", validate);


form.addEventListener("submit", function (e) {

    e.preventDefault();

    if (validate()) {

        document.getElementById("success").textContent =
            "Registration successful!";

        form.reset();

    } else {

        document.getElementById("success").textContent = "";

    }
});
```
