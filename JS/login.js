$(document).ready(function () {
    $('#loginForm').submit(function (event) {
        event.preventDefault(); // Prevent form from reloading the page

        let email = $('#email').val().trim();
        let password = $('#password').val().trim();

        if (!email || !password) {
            alert('Please enter both email and password.');
            return;
        }

        $.ajax({
            url: 'http://localhost/guvi project/php/login.php', // ✅ Fixed URL
            type: 'POST',
            data: { email: email, password: password },
            dataType: 'json', // Expect JSON response
            success: function (data) {
                console.log("Server Response: ", data); // Debug response

                if (data.success) {
                    // Store token & email in localStorage
                    localStorage.setItem('sessionToken', data.token);
                    localStorage.setItem('email', data.email);

                    console.log("Token Saved: ", localStorage.getItem('sessionToken'));
                    console.log("Email Saved: ", localStorage.getItem('email'));

                    window.location.href = 'profile.html'; // Redirect to profile page
                } else {
                    alert(data.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
                alert('Login failed. Please try again.');
            }
        });
    });
});
