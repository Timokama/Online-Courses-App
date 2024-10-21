<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery -->
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <form id="registerForm">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <div id="error-message" style="color: red;"></div>
            <button type="submit">Register</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('#registerForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                // Log input values for debugging
                console.log('Username:', $('#username').val());
                console.log('Email:', $('#email').val());
                console.log('Password:', $('#password').val());

                // AJAX request
                $.ajax({
                    url: 'http://localhost/online_courses/controllers/ApplicantController.php',
                    type: 'POST',
                    dataType: 'json',
                    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                    data: {
                        action: 'register',
                        username: $('#username').val(),
                        email: $('#email').val(),
                        password: $('#password').val()
                    },
                    success: function(response) {
                        if (response.message === 'Registration successful!') {
                            alert('Registration successful!');
                            window.location.href = 'login.php'; // Redirect to login
                        } else {
                            $('#error-message').text(response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Attempt to parse the response
                        console.log('XHR:', xhr); // Log the entire XHR object
                        console.log('Status:', status); // Log the status
                        console.log('Error:', error); // Log the error message

                        try {
                            const response = JSON.parse(xhr.responseText);
                            $('#error-message').text('Error: ' + response.error);
                        } catch (e) {
                            $('#error-message').text('Error: Unable to parse server response.');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
