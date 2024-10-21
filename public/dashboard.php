<?php
session_start();
include_once '../config/database.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch all courses
$query = "SELECT id, course_name, course_description FROM courses";
$result = $connection->query($query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional: Link to your CSS file -->
</head>
<body>
<div class="">
    <header>
        <h1>Welcome to the Dashboard</h1>
        <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p> <!-- Display logged-in user's name -->
    </header>
	 <a href="logout.php">Logout</a>

    <main>
        <h2>Available Courses</h2>
        <?php if ($result->num_rows > 0): ?>
            <ul>
                <?php while ($course = $result->fetch_assoc()): ?>
                    <li>
                        <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                        <p><?php echo htmlspecialchars($course['course_description']); ?></p>
                        <a href="courseId.php?id=<?php echo $course['id']; ?>">View Details</a> <!-- Link to course details -->
			<a href="application.php" style="float:right"> Apply Course</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No courses available at the moment.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Online Courses Platform</p>
    </footer>
</div>
</body>
</html>
