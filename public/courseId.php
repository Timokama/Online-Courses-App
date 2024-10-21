<?php
// Include necessary files for database connection
include_once '../config/database.php';

// Check if course ID is set
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Sanitize the course ID
    $courseId = intval($_GET['id']); // Cast to integer to prevent SQL injection

    // Prepare the SQL statement to fetch course details
    $stmt = $connection->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->bind_param("i", $courseId); // "i" specifies the type of the parameter (integer)
    
    // Execute the statement
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Fetch course details
            $course = $result->fetch_assoc();
            // Respond with course details in JSON format
            echo json_encode(['success' => true, 'course' => $course]);
        } else {
            // Course not found
            echo json_encode(['success' => false, 'error' => 'Course not found.']);
        }
    } else {
        // SQL execution error
        echo json_encode(['success' => false, 'error' => 'Database query failed.']);
    }
    
    // Close the statement
    $stmt->close();
} else {
    // Course ID not provided
    echo json_encode(['success' => false, 'error' => 'Course ID is required.']);
}

// Close the database connection
$connection->close();
?>
