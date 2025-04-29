$(document).ready(function () {
    let token = localStorage.getItem("sessionToken");

    console.log("Token Sent in Request:", token); // Debugging

    if (!token) {
        alert("Unauthorized access! Please login again.");
        window.location.href = "login.html";
        return;
    }

    // Fetch the profile when the page loads
    function loadProfile() {
        $.ajax({
            url: "http://localhost/guvi project/php/profile.php",
            type: "POST",
            data: { token: token }, // Send only token for profile fetching
            dataType: "json",
            success: function (data) {
                console.log("Server Response: ", data);
                if (data.success) {
                    $("#age").val(data.profile.age || "");
                    $("#dob").val(data.profile.dob || "");
                    $("#contact").val(data.profile.contact || "");
                    
                    $("#greeting").text(`Hi, ${data.profile.username}!`);
                } else {
                    alert(data.message);
                }
            },
            error: function (xhr) {
                console.error("Profile Fetch Error:", xhr.responseText);
            }
        });
    }

    loadProfile(); // Load profile initially

    // Handle form submission for profile update
    $("#profileForm").submit(function (e) {
        e.preventDefault(); // Prevent default form submission

        // Get the values from the form
        let age = $("#age").val();
        let dob = $("#dob").val();
        let contact = $("#contact").val();

        // Validate form data
        if (!age || !dob || !contact) {
            alert("All fields are required.");
            return;
        }

        // Send update request to the backend
        $.ajax({
            url: "http://localhost/guvi project/php/profile.php", 
            type: "POST",
            data: {
                token: token,
                age: age,
                dob: dob,
                contact: contact
            },
            dataType: "json",
            success: function (data) {
                console.log("Profile Updated Response: ", data);
                if (data.success) {
                    alert("Profile updated successfully!");
                    loadProfile(); // Reload profile data after update
                } else {
                    alert(data.message);
                }
            },
            error: function (xhr) {
                console.error(" Profile Update Error:", xhr.responseText);
            }
        });
    });

    // Handle sign out
    $("#logoutBtn").click(function () {
        localStorage.removeItem("sessionToken"); // Remove session token
        window.location.href = "login.html"; // Redirect to login
    });
});
