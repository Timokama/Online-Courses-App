<?php
// api/Course.php

require_once '../config/database.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Create a new course
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Extract course details
        $course_name = $data['course_name'] ?? null;
        $course_description = $data['course_description'] ?? null;
        $intake_id = $data['intake_id'] ?? null;

        // Validate input
        if (empty($course_name) || empty($course_description) || empty($intake_id)) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Course name, description, and intake ID are required.']);
            exit;
        }

        try {
            // Insert the new course into the database
            $stmt = $pdo->prepare("INSERT INTO courses (course_name, course_description, intake_id) VALUES (?, ?, ?)");
            $stmt->execute([$course_name, $course_description, $intake_id]);

            // Return success response with the ID of the newly created course
            $course_id = $pdo->lastInsertId(); // Get the ID of the newly created course
            http_response_code(201); // Created
            echo json_encode(['message' => 'Course created successfully.', 'course_id' => $course_id]);

        } catch (PDOException $e) {
            // Return a 500 Internal Server Error if something goes wrong with the database
            http_response_code(500);
            echo json_encode(['error' => 'Creation failed: ' . $e->getMessage()]);
        }
        break;

    case 'GET':
        // Get all courses or search
        if (isset($_GET['query'])) {
            $stmt = $pdo->prepare("SELECT * FROM courses WHERE course_name LIKE ?");
            $stmt->execute(['%' . $_GET['query'] . '%']);
            $courses = $stmt->fetchAll();
            echo json_encode($courses);
        } else {
            $stmt = $pdo->query("SELECT * FROM courses");
            $courses = $stmt->fetchAll();
            echo json_encode($courses);
        }
        break;
    case 'PUT':
        // Update course logic
        // Get the raw input data from the request body
        $data = json_decode(file_get_contents('php://input'), true);

        // Extract necessary data
        $course_id = $data['course_id'] ?? null;
        $course_name = $data['course_name'] ?? null;
        $course_description = $data['course_description'] ?? null;
        $intake_id = $data['intake_id'] ?? null;

        // Validate input: Course ID is required, and at least one field to update
        if (empty($course_id) || (!$course_name && !$course_description && !$intake_id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Course ID and at least one field to update are required.']);
            exit;
        }

        try {
            // Check if the course exists
            $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
            $stmt->execute([$course_id]);
            $course = $stmt->fetch();

            if (!$course) {
                http_response_code(404);
                echo json_encode(['error' => 'Course not found.']);
                exit;
            }

            // Prepare the update query dynamically based on the provided fields
            $updateQuery = "UPDATE courses SET ";
            $params = [];

            if ($new_title) {
                $updateQuery .= "course_name = ?, ";
                $params[] = $course_name;
            }
            if ($new_description) {
                $updateQuery .= "course_description = ?, ";
                $params[] = $course_description;
            }
            if ($new_capacity) {
                $updateQuery .= "intake_id = ?, ";
                $params[] = $new_capacity;
            }

            // Remove the trailing comma and space from the query
            $updateQuery = rtrim($updateQuery, ', ');

            // Add the condition to update the specific course
            $updateQuery .= " WHERE id = ?";
            $params[] = $course_id;

            // Execute the update query
            $stmt = $pdo->prepare($updateQuery);
            $stmt->execute($params);

            // Return success response
            http_response_code(200);
            echo json_encode(['message' => 'Course updated successfully.']);

        } catch (PDOException $e) {
            // Return a 500 Internal Server Error if something goes wrong with the database
            http_response_code(500);
            echo json_encode(['error' => 'Update failed: ' . $e->getMessage()]);
        }
        break;

    case 'DELETE':
        // Delete course logic
        // Get the raw input data from the request body
        $data = json_decode(file_get_contents('php://input'), true);

        // Extract the course ID
        $course_id = $data['course_id'] ?? null;

        // Validate input
        if (empty($course_id)) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Course ID is required.']);
            exit;
        }

        try {
            // Check if the course exists
            $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
            $stmt->execute([$course_id]);
            $course = $stmt->fetch();

            if (!$course) {
                http_response_code(404); // Not Found
                echo json_encode(['error' => 'Course not found.']);
                exit;
            }

            // Delete the course from the database
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$course_id]);

            // Return success response
            http_response_code(200); // OK
            echo json_encode(['message' => 'Course deleted successfully.']);

        } catch (PDOException $e) {
            // Return a 500 Internal Server Error if something goes wrong with the database
            http_response_code(500);
            echo json_encode(['error' => 'Delete failed: ' . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['error' => 'Invalid request method.']);
        break;
}
?>
