<?php
// api/Applicant.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Register new applicant
        $data = json_decode(file_get_contents('php://input'), true);
        $name = $data['username'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        // Validate incoming data
        if (empty($name) || empty($email) || empty($password)) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'All fields are required.']);
            exit;
        }

        // Hash the password before storing
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Prepare SQL statement to insert new applicant
            $stmt = $pdo->prepare("INSERT INTO applicants (username, email, password) VALUES (?, ?, ?)");

            // Debugging: Check the parameters being passed
            var_dump([$name, $email, $hashedPassword]); // Add this line for debugging

            $stmt->execute([$name, $email, $hashedPassword]);

            echo json_encode(['message' => 'Registration successful.']);
        } catch (PDOException $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
        break;

    case 'GET':
        // Get all applicants or a specific applicant
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM applicants WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $applicant = $stmt->fetch();
            if ($applicant) {
                echo json_encode($applicant);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Applicant not found.']);
            }
        } else {
            $stmt = $pdo->query("SELECT * FROM applicants");
            $applicants = $stmt->fetchAll();
            echo json_encode($applicants);
        }
        break;
    case 'PUT':
        // Update applicant logic
        // Get the raw input data from the request body
        $data = json_decode(file_get_contents('php://input'), true);

        // Extract necessary data
        $applicant_id = $data['applicant_id'] ?? null;
        $new_name = $data['username'] ?? null;
        $new_email = $data['email'] ?? null;
        $new_password = $data['password'] ?? null;

        // Validate the input: Applicant ID is required, and at least one field to update
        if (empty($applicant_id) || (!$new_name && !$new_email && !$new_password)) {
            http_response_code(400);
            echo json_encode(['error' => 'Applicant ID and at least one field to update are required.']);
            exit;
        }

        try {
            // Check if the applicant exists
            $stmt = $pdo->prepare("SELECT * FROM applicants WHERE id = ?");
            $stmt->execute([$applicant_id]);
            $applicant = $stmt->fetch();

            if (!$applicant) {
                http_response_code(404);
                echo json_encode(['error' => 'Applicant not found.']);
                exit;
            }

            // Prepare the update query dynamically based on the provided fields
            $updateQuery = "UPDATE applicants SET ";
            $params = [];

            if ($new_name) {
                $updateQuery .= "username = ?, ";
                $params[] = $new_name;
            }
            if ($new_email) {
                $updateQuery .= "email = ?, ";
                $params[] = $new_email;
            }
            if ($new_password) {
                $updateQuery .= "password = ?, ";
                $params[] = password_hash($new_password, PASSWORD_BCRYPT); // Hash the password
            }

            // Remove the trailing comma and space from the query
            $updateQuery = rtrim($updateQuery, ', ');

            // Add the condition to update the specific applicant
            $updateQuery .= " WHERE id = ?";
            $params[] = $applicant_id;

            // Execute the update query
            $stmt = $pdo->prepare($updateQuery);
            $stmt->execute($params);

            // Return success response
            http_response_code(200);
            echo json_encode(['message' => 'Applicant updated successfully.']);

        } catch (PDOException $e) {
            // Return a 500 Internal Server Error if something goes wrong with the database
            http_response_code(500);
            echo json_encode(['error' => 'Update failed: ' . $e->getMessage()]);
        }
        break;

    case 'DELETE':
        // Delete applicant logic
        // Get the raw input data (usually sent via the query string or request body)
        $data = json_decode(file_get_contents('php://input'), true);

        // Ensure the applicant ID is provided
        $applicant_id = $data['applicant_id'] ?? null;

        if (empty($applicant_id)) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Applicant ID is required.']);
            exit;
        }

        try {
            // Check if the applicant exists
            $stmt = $pdo->prepare("SELECT * FROM applicants WHERE id = ?");
            $stmt->execute([$applicant_id]);
            $applicant = $stmt->fetch();

            if (!$applicant) {
                http_response_code(404); // Not Found
                echo json_encode(['error' => 'Applicant not found.']);
                exit;
            }

            // Delete the applicant from the database
            $stmt = $pdo->prepare("DELETE FROM applicants WHERE id = ?");
            $stmt->execute([$applicant_id]);

            // Return success response
            http_response_code(200);
            echo json_encode(['message' => 'Applicant deleted successfully.']);

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
};

class Applicant {
    private $db;
    public function __construct($database) {
        $this->db = $database;
    }
    public function login($email, $password) {
    	$query = "SELECT * FROM applicants WHERE email = :email LIMIT 1";
    	$stmt = $this->db->prepare($query);
    	$stmt->bindParam(':email', $email);
    	$stmt->execute();

    	if ($stmt->rowCount() > 0) {
        	$applicant = $stmt->fetch(PDO::FETCH_ASSOC);
        	if (password_verify($password, $applicant['password'])) {
            		return $applicant; // Return applicant data
        	}
    	}
    	return false; // Invalid credentials
    }
}
?>
