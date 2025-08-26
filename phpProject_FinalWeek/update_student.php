<?php
// Database connection
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
$studentData = [];

// Check if form is submitted
if ($_POST) {
    // Get form data
    $student_id = intval($_POST['student_id']);
    $full_name = trim($_POST['full_name']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $course = trim($_POST['course']);
    $year_level = intval($_POST['year_level']); // Back to intval
    $contact_number = trim($_POST['contact_number']);
    $email = trim($_POST['email']);
    $existing_photo = $_POST['existing_photo'];

    // Handle photo upload (optional for updates)
    $photoPath = $existing_photo; // Keep existing photo by default
    
    if (isset($_FILES['student_photo']) && $_FILES['student_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/photos/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileInfo = pathinfo($_FILES['student_photo']['name']);
        $fileName = 'student_' . time() . '.' . $fileInfo['extension'];
        $targetPath = $uploadDir . $fileName;
        
        $allowedTypes = ['jpg', 'jpeg', 'png'];
        if (in_array(strtolower($fileInfo['extension']), $allowedTypes) && 
            $_FILES['student_photo']['size'] <= 2 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['student_photo']['tmp_name'], $targetPath)) {
                // Delete old photo if it exists and is different
                if (!empty($existing_photo) && file_exists($existing_photo) && $existing_photo !== $targetPath) {
                    unlink($existing_photo);
                }
                $photoPath = $targetPath;
            }
        }
    }

    // Store data for display
    $studentData = compact('student_id', 'full_name', 'dob', 'gender', 'course', 'year_level', 'contact_number', 'email', 'photoPath');

    // Validation
    if (empty($full_name) || empty($dob) || empty($gender) || empty($course) || 
        empty($year_level) || empty($contact_number) || empty($email)) {
        $message = "All required fields must be filled.";
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = 'error';
    } else {
        try {
            // Update database
            $sql = "UPDATE students SET full_name = ?, dob = ?, gender = ?, course = ?, 
                    year_level = ?, contact_number = ?, email = ?, photo = ? WHERE id = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$full_name, $dob, $gender, $course, $year_level, $contact_number, $email, $photoPath, $student_id]);
            
            $message = "Student information updated successfully.";
            $messageType = 'success';
            
        } catch(PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $messageType = 'error';
        }
    }
} else {
    $message = "Invalid access. Please submit the form properly.";
    $messageType = 'error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Result</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <div class="form-header">
            <h1><?php echo ($messageType === 'success') ? 'Update Successful' : 'Update Error'; ?></h1>
            <p>Student Information Update Result</p>
        </div>
        
        <div style="padding: 40px;">
            <div class="<?php echo $messageType; ?>-message">
                <?php echo $message; ?>
            </div>
            
            <?php if ($messageType === 'success' && !empty($studentData)): ?>
                <div class="stats-container">
                    <h3>Updated Information</h3>
                    <?php if (!empty($studentData['photoPath'])): ?>
                        <div style="text-align: center; margin-bottom: 20px;">
                            <img src="<?php echo htmlspecialchars($studentData['photoPath']); ?>" 
                                 alt="Student Photo" 
                                 style="max-width: 150px; max-height: 150px; border-radius: 8px; box-shadow: var(--shadow);">
                        </div>
                    <?php endif; ?>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <strong>Full Name:</strong><br><?php echo htmlspecialchars($studentData['full_name']); ?>
                        </div>
                        <div class="stat-item">
                            <strong>Course:</strong><br><?php echo htmlspecialchars($studentData['course']); ?>
                        </div>
                        <div class="stat-item">
                            <strong>Year Level:</strong><br><?php 
                                switch($studentData['year_level']) {
                                    case 1: echo 'First Year'; break;
                                    case 2: echo 'Second Year'; break;
                                    case 3: echo 'Third Year'; break;
                                    case 4: echo 'Fourth Year (Graduating)'; break;
                                    default: echo 'Year ' . $studentData['year_level'];
                                }
                            ?></div>
                        <div class="stat-item">
                            <strong>Email:</strong><br><?php echo htmlspecialchars($studentData['email']); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 32px;">
                <button onclick="window.location.href='index.html'" class="submit-btn">Back to Home</button>
                <button onclick="window.location.href='display_records.php'" class="submit-btn">Back to Student Records</button>
                <?php if ($messageType === 'success' && !empty($studentData['student_id'])): ?>
                    <button onclick="window.location.href='edit_student.php?id=<?php echo $studentData['student_id']; ?>'" class="search-btn">Edit Again</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
