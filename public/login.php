<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery -->
</head>
<body>
    <div class="container">
        <h1>Login</h1><br>
        <form id="loginForm">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <div id="error-message"></div>
            <button type="submit">Login</button>
        </form>
    </div>
    <script>
        // jQuery function to handle form submission
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                // AJAX request
                $.ajax({
                    url: 'http://localhost/online_courses/controllers/LoginController.php', // Update URL as needed
                    type: 'POST',
                    dataType: 'json',
                    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                    data: {
                        action: 'login',
                        email: $('#email').val(),
                        password: $('#password').val()
                    },
                    success: function(response) {
                        if (response.message === 'Login successful!') {
                            alert('Login successful!');
                            window.location.href = 'dashboard.php'; // Redirect to dashboard or home
                        } else {
                            $('#error-message').text(response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Attempt to parse the response
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
