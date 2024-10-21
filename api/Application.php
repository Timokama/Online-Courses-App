<?php
// api/Application.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
    // Apply for courses
    $data = json_decode(file_get_contents('php://input'), true);

    // Extracting the data
    $applicant_id = $data['applicant_id'] ?? null;
    $course_ids = $data['course_ids'] ?? [];

    // Validate input
    if (empty($applicant_id) || empty($course_ids) || count($course_ids) > 3) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid application data. You must provide an applicant ID and up to 3 course IDs.']);
        exit;
    }

    // Check if all course_ids exist in the courses table
    $valid_course_ids = [];
    try {
        $placeholders = implode(',', array_fill(0, count($course_ids), '?'));
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id IN ($placeholders)");
        $stmt->execute($course_ids);
        $valid_course_ids = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Failed to validate courses: ' . $e->getMessage()]);
        exit;
    }

    // Check if any course_id is invalid
    if (count($valid_course_ids) !== count($course_ids)) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'One or more course IDs are invalid.']);
        exit;
    }

    try {
        // Begin a transaction
        $pdo->beginTransaction();

        // Insert applications for the selected courses
        foreach ($course_ids as $course_id) {
            // Prepare the SQL statement
            $stmt = $pdo->prepare("INSERT INTO applications (applicant_id, course_id) VALUES (?, ?)");
            $stmt->execute([$applicant_id, $course_id]);
        }

        // Commit the transaction
        $pdo->commit();
        echo json_encode(['message' => 'Application submitted successfully.']);
    } catch (PDOException $e) {
        // Rollback in case of error
        $pdo->rollBack();
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Application submission failed: ' . $e->getMessage()]);
    }
    break;

    case 'GET':
        // Check if applicant_id is provided
        if (isset($_GET['applicant_id'])) {
            // Get applications for a specific applicant
            $applicant_id = $_GET['applicant_id'];

            // Validate applicant_id
            if (empty($applicant_id)) {
                http_response_code(400); // Bad Request
                echo json_encode(['error' => 'Applicant ID is required.']);
                exit;
            }

            try {
                $stmt = $pdo->prepare("SELECT * FROM applications WHERE applicant_id = ?");
                $stmt->execute([$applicant_id]);
                $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($applications) {
                    echo json_encode($applications);
                } else {
                    echo json_encode(['message' => 'No applications found for this applicant.']);
                }
            } catch (PDOException $e) {
                http_response_code(500); // Internal Server Error
                echo json_encode(['error' => 'Failed to retrieve applications: ' . $e->getMessage()]);
            }
        } else {
            // Get all applications if no applicant_id is provided
            try {
                $stmt = $pdo->query("SELECT * FROM applications");
                $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($applications);
            } catch (PDOException $e) {
                http_response_code(500); // Internal Server Error
                echo json_encode(['error' => 'Failed to retrieve applications: ' . $e->getMessage()]);
            }
        }
        break;

     case 'PUT':
        // Update application
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Extracting the data
        $application_id = $data['application_id'] ?? null;
        $applicant_id = $data['applicant_id'] ?? null;
        $course_ids = $data['course_ids'] ?? null;

        // Validate input
        if (empty($application_id) || (empty($applicant_id) && empty($course_ids))) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid update data. Application ID is required, and at least one of applicant ID or course IDs must be provided.']);
            exit;
        }

        try {
            // Begin a transaction
            $pdo->beginTransaction();
            
            // Prepare the base SQL statement
            $updateFields = [];
            $params = [];

            // Add fields to update
            if (!empty($applicant_id)) {
                $updateFields[] = "applicant_id = ?";
                $params[] = $applicant_id;
            }
            if (!empty($course_ids)) {
                // You might want to handle multiple course_ids logic here if applicable
                // For simplicity, we are just updating to the first course_id
                $updateFields[] = "course_id = ?"; // assuming you want to update to a specific course
                $params[] = $course_ids[0]; // Update to the first course ID (change this logic as needed)
            }

            // Check if there are fields to update
            if (count($updateFields) > 0) {
                $sql = "UPDATE applications SET " . implode(", ", $updateFields) . " WHERE id = ?";
                $params[] = $application_id; // Add the application ID to the parameters

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            }

            // Commit the transaction
            $pdo->commit();
            echo json_encode(['message' => 'Application updated successfully.']);
        } catch (PDOException $e) {
            // Rollback in case of error
            $pdo->rollBack();
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Application update failed: ' . $e->getMessage()]);
        }
        break;

    case 'DELETE':
        // Validate application ID
        if (empty($application_id)) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Application ID is required.']);
            exit;
        }

        try {
            // Prepare and execute the DELETE statement
            $stmt = $pdo->prepare("DELETE FROM applications WHERE id = ?");
            $stmt->execute([$application_id]);

            // Check if the application was deleted
            if ($stmt->rowCount() > 0) {
                echo json_encode(['message' => 'Application deleted successfully.']);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(['error' => 'Application not found.']);
            }
        } catch (PDOException $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Application deletion failed: ' . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['error' => 'Invalid request method.']);
        break;
}
?>
