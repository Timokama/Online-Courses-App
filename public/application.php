<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Form</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container"> <!-- Added container class for styling -->
    <h1>Apply for a Course</h1>

    <form id="applicationForm">
        <label for="applicant_id">Applicant ID:</label>
        <input type="number" id="applicant_id" name="applicant_id" placeholder="Your Applicant ID" required><br><br>

        <label for="course_id">Course:</label>
        <select id="course_id" name="course_id" required>
            <option value="">Select a Course</option>
            <option value="1">Course 1</option>
            <option value="2">Course 2</option>
            <option value="3">Course 3</option>
        </select><br><br>

        <button type="submit">Submit Application</button>
    </form>

    <div id="responseMessage"></div>
</div>

<script>
    // Handle form submission via AJAX
    $('#applicationForm').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        // AJAX call to submit the form data
        $.ajax({
            url: 'http://localhost/online_courses/controllers/ApplicationsController.php', // Adjust this URL as necessary
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'apply',
                applicant_id: $('#applicant_id').val(),
                course_id: $('#course_id').val()
            },
            success: function(response) {
                if (response.message) {
                    $('#responseMessage').text(response.message).css('color', 'green');
		    window.location.href = 'dashboard.php'
		    
                } else if (response.error) {
                    $('#responseMessage').text(response.error).css('color', 'red');
                }
            },
            error: function(xhr, status, error) {
                $('#responseMessage').text('Error: Unable to submit application.').css('color', 'red');
            }
        });
    });
</script>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }
    .container {
        max-width: 400px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    label {
        display: block;
        margin: 10px 0 5px;
    }
    input, select {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    button {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px 15px;
        cursor: pointer;
        border-radius: 4px;
    }
    button:hover {
        background-color: #45a049;
    }
    #responseMessage {
        margin-top: 15px;
        font-weight: bold;
    }
</style>
</body>
</html>
