<?php
// Database connection details
$host = 'localhost';
$dbname = 'school_db';
$username = 'root';
$password = '';

try {
    // Create PDO connection
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
    // Get and sanitize form data
    $full_name = trim($_POST['full_name']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $course = trim($_POST['course']);
    $year_level = intval($_POST['year_level']); // Back to intval for numeric
    $contact_number = trim($_POST['contact_number']);
    $email = trim($_POST['email']);

    // Handle photo upload
    $photoPath = '';
    if (isset($_FILES['student_photo']) && $_FILES['student_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/photos/';
        
        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileInfo = pathinfo($_FILES['student_photo']['name']);
        $fileName = 'student_' . time() . '.' . $fileInfo['extension'];
        $targetPath = $uploadDir . $fileName;
        
        // Basic validation
        $allowedTypes = ['jpg', 'jpeg', 'png'];
        if (in_array(strtolower($fileInfo['extension']), $allowedTypes) && 
            $_FILES['student_photo']['size'] <= 2 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['student_photo']['tmp_name'], $targetPath)) {
                $photoPath = $targetPath;
            }
        }
    }

    // Store data for display (including photo path)
    $studentData = compact('full_name', 'dob', 'gender', 'course', 'year_level', 'contact_number', 'email', 'photoPath');

    // Simple validation
    if (empty($full_name) || empty($dob) || empty($gender) || empty($course) || 
        empty($year_level) || empty($contact_number) || empty($email)) {
        $message = "All required fields must be filled.";
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = 'error';
    } else {
        try {
            // Insert into database
            $sql = "INSERT INTO students (full_name, dob, gender, course, year_level, contact_number, email, photo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$full_name, $dob, $gender, $course, $year_level, $contact_number, $email, $photoPath]);
            
            $message = "Student registration completed successfully.";
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
    <title>Registration Result</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <!-- result header -->
        <div class="form-header">
            <h1><?php echo ($messageType === 'success') ? 'Registration Successful' : 'Registration Error'; ?></h1>
            <p>Application Processing Result</p>
        </div>
        
        <div style="padding: 40px;">
            <!-- message display -->
            <div class="<?php echo $messageType; ?>-message">
                <?php echo $message; ?>
            </div>
            
            <!-- show details if successful -->
            <?php if ($messageType === 'success' && !empty($studentData)): ?>
                <div class="stats-container">
                    <h3>Registration Details</h3>
                    <!-- photo display -->
                    <?php if (!empty($studentData['photoPath'])): ?>
                        <div style="text-align: center; margin-bottom: 20px;">
                            <img src="<?php echo htmlspecialchars($studentData['photoPath']); ?>" 
                                 alt="Student Photo" 
                                 style="max-width: 150px; max-height: 150px; border-radius: 8px; box-shadow: var(--shadow);">
                        </div>
                    <?php endif; ?>
                    <!-- student info grid -->
                    <div class="stats-grid">
                        <div class="stat-item">
                            <strong>Full Name:</strong><br><?php echo htmlspecialchars($studentData['full_name']); ?>
                        </div>
                        <div class="stat-item">
                            <strong>Date of Birth:</strong><br><?php echo htmlspecialchars($studentData['dob']); ?>
                        </div>
                        <div class="stat-item">
                            <strong>Gender:</strong><br><?php echo htmlspecialchars($studentData['gender']); ?>
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
                            <strong>Contact Number:</strong><br><?php echo htmlspecialchars($studentData['contact_number']); ?>
                        </div>
                        <div class="stat-item">
                            <strong>Email Address:</strong><br><?php echo htmlspecialchars($studentData['email']); ?>
                        </div>
                        <div class="stat-item">
                            <strong>Registration Date:</strong><br><?php echo date('Y-m-d H:i:s'); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- navigation buttons -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 32px;">
                <button onclick="window.location.href='index.html'" class="submit-btn">Back to Home</button>
                <button onclick="window.location.href='form.html'" class="submit-btn">Add Another Student</button>
                <button onclick="window.location.href='display_records.php'" class="search-btn">View All Students</button>
            </div>
        </div>
    </div>
</body>
</html>
    </div>
</body>
</html>
