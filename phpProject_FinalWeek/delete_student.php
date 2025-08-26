<?php
// Database connection details
$host = 'localhost';
$dbname = 'school_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = '';
$messageType = '';

// Check if student ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $studentId = intval($_GET['id']);
    
    try {
        // First, get student info to delete photo file
        $stmt = $pdo->prepare("SELECT full_name, photo FROM students WHERE id = ?");
        $stmt->execute([$studentId]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($student) {
            // Delete the student record
            $deleteStmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $deleteStmt->execute([$studentId]);
            
            // Delete photo file if it exists
            if (!empty($student['photo']) && file_exists($student['photo'])) {
                unlink($student['photo']);
            }
            
            $message = "Student record for '" . htmlspecialchars($student['full_name']) . "' has been successfully deleted.";
            $messageType = 'success';
        } else {
            $message = "Student record not found.";
            $messageType = 'error';
        }
        
    } catch(PDOException $e) {
        $message = "Database error: " . $e->getMessage();
        $messageType = 'error';
    }
} else {
    $message = "Invalid student ID provided.";
    $messageType = 'error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Result</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <div class="form-header">
            <h1><?php echo ($messageType === 'success') ? 'Record Deleted' : 'Deletion Error'; ?></h1>
            <p>Student Record Management</p>
        </div>
        
        <div style="padding: 40px;">
            <div class="<?php echo $messageType; ?>-message">
                <?php echo $message; ?>
            </div>
            
            <div style="text-align: center; margin-top: 32px; display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
                <button onclick="window.location.href='index.html'" class="search-btn">Back to Home</button>
                <button onclick="window.location.href='display_records.php'" class="submit-btn">Back to Student Records</button>
            </div>
        </div>
    </div>
</body>
</html>
