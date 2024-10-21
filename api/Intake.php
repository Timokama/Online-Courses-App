<?php
// api/intake.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';


header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Create a new intake
        $data = json_decode(file_get_contents('php://input'), true);
        $name = $data['name'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];

        if (empty($name) || empty($start_date) || empty($end_date)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid intake data.']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO intake (name, start_date, end_date) VALUES (?, ?, ?)");
        $stmt->execute([$name, $start_date, $end_date]);

        echo json_encode(['message' => 'Intake created successfully.']);
        break;

    case 'GET':
        // Get all intakes or a specific intake
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM intakes WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $intake = $stmt->fetch();

            if ($intake) {
                echo json_encode($intake);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Intake not found.']);
            }
        } else {
            $stmt = $pdo->query("SELECT * FROM intake");
            $intakes = $stmt->fetchAll();
            echo json_encode($intakes);
        }
        break;

    // Additional cases for PUT and DELETE can be added here
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed.']);
        break;
}
