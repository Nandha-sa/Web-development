
$(document).ready(function () {
    console.log("Signup script is running");

    // Register form submission
    $('#registerForm').submit(function (event) {
        event.preventDefault();
        console.log("Register button clicked");

        let username = $('#username').val().trim();
        let email = $('#email').val().trim();
        let password = $('#password').val().trim();

        console.log("Username:", username);
        console.log("Email:", email);


        // Basic validation
        if (!username || !email || !password) {
            alert('Please fill in all fields.');
            return;
        }

        $.ajax({
            url: 'http://localhost/guvi project/php/signup.php', 
            type: 'POST',
            data: { username: username, email: email, password: password },
            dataType: 'json', 
            success: function (response) {
                if (response.success) {
                    alert('Registration successful! Redirecting to login...');
                    window.location.href = 'login.html';
                } else {
                    alert('❌ ' + response.message); // Show backend error messages
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
                alert('Registration failed. Please try again.');
            }
        });
    });
});
