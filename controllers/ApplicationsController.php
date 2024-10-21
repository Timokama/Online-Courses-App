<?php
include_once '../config/database.php'; // Adjust the path as necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'apply') {
        $applicant_id = trim($_POST['applicant_id'] ?? '');
        $course_id = trim($_POST['course_id'] ?? '');

        // Validate inputs
        if (empty($applicant_id) || empty($course_id)) {
            echo json_encode(['error' => 'All fields are required.']);
            exit();
        }

        // Here would be the logic to save the application to the database
        // Example (ensure you have error handling here):
        $stmt = $connection->prepare("INSERT INTO applications (applicant_id, course_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $applicant_id, $course_id); // "ii" because both are integers

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Application submitted successfully!']);
        } else {
            echo json_encode(['error' => 'Database error: ' . $stmt->error]);
        }
        
        $stmt->close();
        exit();
    } else {
        echo json_encode(['error' => 'Invalid action.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
?>
