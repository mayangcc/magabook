// ============================================
// ACCOUNT CREATION
// ============================================
function createAccFunc(event) {
    event.preventDefault();
    const usernameValue = document.getElementById("txtUsername").value;
    const usernamePattern = /^[a-zA-Z0-9._]{4,15}$/;

    if (!usernamePattern.test(usernameValue)) {
        Swal.fire({
            title: "Invalid Username",
            text: "Username must be 4–15 characters and only contain letters, numbers, dot (.) and underscore (_).",
            icon: "error",
            color: "#f06292",
            background: "#fff0f6"
        });
        return;
    }

    const passwordValue = document.getElementById("txtPassword").value.trim();
    const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;

    if (!passwordValue) {
        Swal.fire({
            title: "Missing Password",
            text: "Please enter your password.",
            icon: "warning",
            color: "#f06292",
            background: "#fff0f6"
        });
        return;
    }

    if (!passwordPattern.test(passwordValue)) {
        Swal.fire({
            title: "Weak Password",
            text: "Password must be at least 8 characters and include uppercase, lowercase, a number, and a special character (like _ or !).",
            icon: "error",
            color: "#f06292",
            background: "#fff0f6"
        });
        return;
    }

    const firstNameValue = document.getElementById("txtFirstName").value;
    const lastNameValue = document.getElementById("txtLastName").value;
    const emailValue = document.getElementById("txtEmail").value;
    let fullAddressValue = document.getElementById("txtFullAddress").value;

    if (!fullAddressValue.trim()) {
        Swal.fire("Error", "Please enter your full address.", "error");
        return;
    }

    const provinceValue = document.getElementById("txtProvince").selectedOptions[0].text;
    const cityValue = document.getElementById("txtCity").selectedOptions[0].text;
    const barangayValue = document.getElementById("txtBarangay").selectedOptions[0].text;

    fullAddressValue = `${fullAddressValue}, Brgy. ${barangayValue}, ${cityValue}, ${provinceValue}`;

    const dateOfBirthValue = document.getElementById("txtDateOfBirth").value;
    const genderValue = document.getElementById("txtGender").value;

    // Calculate age
    const birthdate = new Date(dateOfBirthValue);
    const today = new Date();
    let age = today.getFullYear() - birthdate.getFullYear();
    const m = today.getMonth() - birthdate.getMonth();

    if (m < 0 || (m === 0 && today.getDate() < birthdate.getDate())) {
        age--;
    }

    if (age < 18) {
        Swal.fire({
            title: "Error",
            text: "You must be at least 18 years old.",
            icon: "error",
            color: "#f06292",
            background: "#fff0f6"
        });
        return;
    }

    if (age > 120) {
        Swal.fire("Invalid Birthdate", "Age cannot be more than 120 years.", "error");
        return;
    }

    // Submit form via AJAX
    $.ajax({
        url: "../controllers/user-controller.php",
        type: "POST",
        data: {
            username: usernameValue,
            password: passwordValue,
            firstName: firstNameValue,
            lastName: lastNameValue,
            email: emailValue,
            fullAddress: fullAddressValue,
            birthdate: dateOfBirthValue,
            gender: genderValue
        },
        success: function(returnedData) {
            if (returnedData.includes("successfully")) {
                Swal.fire({
                    title: "Thank You!",
                    text: "Your account has been created!",
                    icon: "success",
                    confirmButtonText: "Continue",
                    color: "#f06292",
                    background: "#fff0f6"
                }).then((result) => {
                    if (result.isConfirmed) {
                        redirectfunc(1);
                    }
                });
            } else {
                Swal.fire("Error", returnedData, "error");
            }
        },
        error: function(xhr) {
            Swal.fire({
                title: "Error",
                text: xhr.responseText,
                icon: "error",
                color: "#f06292",
                background: "#fff0f6"
            });
        }
    });
}

// ============================================
// PASSWORD TOGGLE
// ============================================
document.addEventListener("DOMContentLoaded", function() {
    const toggle = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("txtPassword");

    if (toggle) {
        toggle.addEventListener("click", () => {
            passwordInput.type = passwordInput.type === "password" ? "text" : "password";
            toggle.classList.toggle("fa-eye");
            toggle.classList.toggle("fa-eye-slash");
        });
    }
});

// ============================================
// DATE PICKER & LOCATION DROPDOWNS
// ============================================
document.addEventListener("DOMContentLoaded", function() {
    loadProvinces();

    flatpickr("#txtDateOfBirth", {
        dateFormat: "Y-m-d",
        maxDate: "today",
        allowInput: true
    });

    const dateInput = document.getElementById("txtDateOfBirth");
    document.querySelector(".calendar-icon").addEventListener("click", () => {
        dateInput._flatpickr.open();
    });

    dateInput.addEventListener("input", function() {
        let value = this.value.replace(/[^0-9]/g, "");

        if (value.length > 4 && value.length <= 6) {
            value = value.slice(0, 4) + "-" + value.slice(4);
        } else if (value.length > 6) {
            value = value.slice(0, 4) + "-" + value.slice(4, 6) + "-" + value.slice(6, 8);
        }

        this.value = value;
    });

    function loadProvinces() {
        fetch("https://psgc.gitlab.io/api/provinces/")
            .then(res => res.json())
            .then(data => {
                const provinceSelect = document.getElementById("txtProvince");
                provinceSelect.innerHTML = '<option value="" disabled selected>Select Province</option>';
                data.forEach(province => {
                    provinceSelect.innerHTML += `<option value="${province.code}">${province.name}</option>`;
                });
            });
    }

    document.getElementById("txtProvince").addEventListener("change", function() {
        const provinceCode = this.value;
        fetch(`https://psgc.gitlab.io/api/provinces/${provinceCode}/cities-municipalities/`)
            .then(res => res.json())
            .then(data => {
                const citySelect = document.getElementById("txtCity");
                citySelect.innerHTML = '<option value="" disabled selected>Select City</option>';
                data.forEach(city => {
                    citySelect.innerHTML += `<option value="${city.code}">${city.name}</option>`;
                });
                document.getElementById("txtBarangay").innerHTML = '<option value="" disabled selected>Select Barangay</option>';
            });
    });

    document.getElementById("txtCity").addEventListener("change", function() {
        const cityCode = this.value;
        fetch(`https://psgc.gitlab.io/api/cities-municipalities/${cityCode}/barangays/`)
            .then(res => res.json())
            .then(data => {
                const brgySelect = document.getElementById("txtBarangay");
                brgySelect.innerHTML = '<option value="" disabled selected>Select Barangay</option>';
                data.forEach(brgy => {
                    brgySelect.innerHTML += `<option value="${brgy.name}">${brgy.name}</option>`;
                });
            });
    });
});

// ============================================
// LOGIN & LOGOUT
// ============================================
function loginFunc(event) {
    if (event) event.preventDefault();

    const username = document.getElementById("txtUsername").value.trim();
    const password = document.getElementById("txtPassword").value.trim();

    if (!username || !password) {
        Swal.fire({
            title: "Missing Fields",
            text: "Please enter username and password.",
            icon: "warning",
            color: "#f06292",
            background: "#fff0f6"
        });
        return;
    }

    $.ajax({
        url: "../controllers/user-controller.php",
        type: "POST",
        data: { logUsername: username, logPassword: password },
        success: function(response) {
            if (response === "success") {
                Swal.fire({
                    title: "Welcome!",
                    text: "Login successful.",
                    icon: "success",
                    color: "#f06292",
                    background: "#fff0f6"
                }).then(() => {
                    redirectfunc(1);
                });
            } else {
                Swal.fire({
                    title: "Login Failed",
                    text: "Invalid username or password.",
                    icon: "error",
                    color: "#f06292",
                    background: "#fff0f6"
                });
            }
        },
        error: function(xhr) {
            Swal.fire("Error", xhr.responseText, "error");
        }
    });
}

function logoutFunc() {
    $.ajax({
        url: "../controllers/user-controller.php",
        type: "POST",
        data: { logout: true },
        success: function(response) {
            if (response === "logout") {
                location.reload(); // ✅ instant refresh, no alert
            }
        }
    });
}

// ============================================
// NAVIGATION & REDIRECT
// ============================================
function redirectfunc(redirectID) {
    const routes = {
        1: "../views/Index.php",
        2: "../views/Log-In.php",
        3: "../views/Registration.php",
        4: "../views/Dashboard.php"
    };
    if (routes[redirectID]) {
        window.location.href = routes[redirectID];
    }
}

function toggleDropdown() {
    const profileDropdown = document.getElementById("dropdownMenu");
    const notifDropdown = document.getElementById("notifDropdown");

    // ✅ CLOSE notif dropdown
    notifDropdown.style.display = "none";

    // ✅ TOGGLE profile dropdown
    profileDropdown.style.display =
        profileDropdown.style.display === "block" ? "none" : "block";
}

window.onclick = function(event) {
    if (!event.target.closest('.profile')) {
        document.getElementById("dropdownMenu").style.display = "none";
    }

    if (!event.target.closest('.notification')) {
        document.getElementById("notifDropdown").style.display = "none";
    }
};

document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".login-btn").forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            redirectfunc(2);
        });
    });

    document.querySelectorAll(".register-btn").forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            redirectfunc(3);
        });
    });
});

// ============================================
// SEARCH
// ============================================
function searchFunc() {
    const keyword = document.getElementById("searchInput").value.trim();

    if (!keyword) {
        Swal.fire({
            title: "Empty Search",
            text: "Please enter something to search.",
            icon: "warning"
        });
        return;
    }

    $.ajax({
        url: "../controllers/user-controller.php",
        type: "POST",
        data: { searchTerm: keyword },
        success: function(response) {
            document.querySelector(".card-grid").innerHTML = response;
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });
}

document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("searchInput");
    const cardGrid = document.querySelector(".card-grid");
    const originalContent = cardGrid.innerHTML;
    let debounceTimer;

    searchInput.addEventListener("input", function() {
        const keyword = this.value.trim();
        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            if (keyword.length === 0) {
                cardGrid.innerHTML = originalContent;
                return;
            }

            $.ajax({
                url: "../controllers/user-controller.php",
                type: "POST",
                data: { searchTerm: keyword },
                success: function(response) {
                    cardGrid.innerHTML = response;
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }, 300);
    });
});
// ============================================
// NOTIFICATION TOGGLE
// ============================================
function toggleNotifDropdown() {
    const notifDropdown = document.getElementById("notifDropdown");
    const profileDropdown = document.getElementById("dropdownMenu");

    // close profile
    profileDropdown.style.display = "none";

    // toggle notif
    const isOpening = notifDropdown.style.display !== "block";
    notifDropdown.style.display = isOpening ? "block" : "none";

    if (isOpening) {
        // ✅ CALL BACKEND → mark as read
        $.ajax({
            url: "../controllers/user-controller.php",
            type: "POST",
            data: { markNotificationsRead: true },
            success: function () {
                // ✅ REMOVE DOT AFTER DB UPDATE
                const dot = document.querySelector(".notification-dot");
                if (dot) dot.remove();

                // ✅ OPTIONAL: remove unread highlight
                document.querySelectorAll(".notif-item.unread")
                    .forEach(el => el.classList.remove("unread"));
            }
        });
    }



}


document.addEventListener("DOMContentLoaded", function () {

    const dataElement = document.getElementById('subscriptionData');

    if (!dataElement) {
        console.error("❌ subscriptionData not found");
        return;
    }

    const rawData = dataElement.textContent;
    const subscriptionStats = JSON.parse(rawData);

    console.log("✅ DATA:", subscriptionStats); // DEBUG

    const subscriptionLabels = subscriptionStats.map(item => item.subscriptions_name);
    const subscriptionData = subscriptionStats.map(item => item.total);

    const ctx = document.getElementById('subscriptionChart');

    if (!ctx) {
        console.error("❌ Canvas not found");
        return;
    }

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: subscriptionLabels,
            datasets: [{
                data: subscriptionData,
                backgroundColor: [
                    '#fbc2eb',
                    '#b1ebf3',
                    '#ffe8ef'
                ]
            }]
        }
    });

});