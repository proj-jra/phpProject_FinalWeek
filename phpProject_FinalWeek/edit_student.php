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

// Get student ID
$studentId = $_GET['id'] ?? null;
if (!$studentId || !is_numeric($studentId)) {
    header('Location: display.php');
    exit;
}

// Fetch student data
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header('Location: display.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <!-- header section -->
        <div class="form-header">
            <div class="logo-container">
                <img src="logo.webp" alt="Institution Logo" class="form-logo" style="max-width: 150px; height: auto;">
            </div>
            <h1>Edit Student Information</h1>
            <p>Update student details and save changes</p>
        </div>
        
        <!-- main form -->
        <form action="update_student.php" method="POST" id="editForm" enctype="multipart/form-data">
            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
            <input type="hidden" name="existing_photo" value="<?php echo htmlspecialchars($student['photo']); ?>">
            
            <!-- full name input -->
            <div class="form-group">
                <label for="full_name" class="required">Full Name:</label>
                <input type="text" id="full_name" name="full_name" required minlength="2" 
                       value="<?php echo htmlspecialchars($student['full_name']); ?>">
                <small class="form-hint">Enter complete legal name</small>
            </div>

            <!-- date of birth -->
            <div class="form-group">
                <label for="dob" class="required">Date of Birth:</label>
                <input type="date" id="dob" name="dob" required 
                       value="<?php echo $student['dob']; ?>">
                <small class="form-hint">Must be at least 17 years old to apply.</small>
            </div>

            <!-- gender selection -->
            <div class="form-group">
                <label class="required">Gender:</label>
                <div class="radio-group">
                    <div class="radio-item">
                        <input type="radio" id="male" name="gender" value="Male" required 
                               <?php echo ($student['gender'] === 'Male') ? 'checked' : ''; ?>>
                        <label for="male">Male</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="female" name="gender" value="Female"
                               <?php echo ($student['gender'] === 'Female') ? 'checked' : ''; ?>>
                        <label for="female">Female</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="other" name="gender" value="Other"
                               <?php echo ($student['gender'] === 'Other') ? 'checked' : ''; ?>>
                        <label for="other">Other</label>
                    </div>
                </div>
            </div>

            <!-- course dropdown -->
            <div class="form-group">
                <label for="course" class="required">Course/Program:</label>
                <select id="course" name="course" required>
                    <option value="">Select preferred course</option>
                    <option value="Computer Science" <?php echo ($student['course'] === 'Computer Science') ? 'selected' : ''; ?>>Computer Science</option>
                    <option value="Business Administration" <?php echo ($student['course'] === 'Business Administration') ? 'selected' : ''; ?>>Business Administration</option>
                    <option value="Multimedia Arts" <?php echo ($student['course'] === 'Multimedia Arts') ? 'selected' : ''; ?>>Multimedia Arts</option>
                    <option value="Creative Writing" <?php echo ($student['course'] === 'Creative Writing') ? 'selected' : ''; ?>>Creative Writing</option>
                    <option value="International Relations" <?php echo ($student['course'] === 'International Relations') ? 'selected' : ''; ?>>International Relations</option>
                </select>
            </div>

            <!-- year level -->
            <div class="form-group">
                <label for="year_level" class="required">Year Level:</label>
                <select id="year_level" name="year_level" required>
                    <option value="">Select academic year</option>
                    <option value="1" <?php echo ($student['year_level'] == 1) ? 'selected' : ''; ?>>First Year</option>
                    <option value="2" <?php echo ($student['year_level'] == 2) ? 'selected' : ''; ?>>Second Year</option>
                    <option value="3" <?php echo ($student['year_level'] == 3) ? 'selected' : ''; ?>>Third Year</option>
                    <option value="4" <?php echo ($student['year_level'] == 4) ? 'selected' : ''; ?>>Fourth Year (Graduating)</option>
                </select>
            </div>

            <!-- contact number -->
            <div class="form-group">
                <label for="contact_number" class="required">Contact Number:</label>
                <input type="tel" id="contact_number" name="contact_number" required 
                       value="<?php echo htmlspecialchars($student['contact_number']); ?>">
                <small class="form-hint">Please include country code for international numbers</small>
            </div>

            <!-- email input -->
            <div class="form-group">
                <label for="email" class="required">Email Address:</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($student['email']); ?>">
                <small class="form-hint">This will be used for all official communications</small>
            </div>

            <!-- current photo display and new photo upload -->
            <div class="form-group">
                <label for="student_photo">Student Photo:</label>
                
                <!-- Current photo display -->
                <?php if (!empty($student['photo']) && file_exists($student['photo'])): ?>
                    <div style="margin-bottom: 15px;">
                        <p><strong>Current Photo:</strong></p>
                        <img src="<?php echo htmlspecialchars($student['photo']); ?>" 
                             alt="Current Photo" 
                             style="max-width: 150px; max-height: 150px; border-radius: 8px; border: 2px solid var(--border-light);">
                    </div>
                <?php endif; ?>
                
                <!-- New photo upload -->
                <div class="file-upload-container">
                    <input type="file" id="student_photo" name="student_photo" accept="image/*">
                    <div class="file-upload-preview">
                        <img id="photo-preview" src="" alt="Photo Preview" style="display: none;">
                        <div id="file-info" class="file-info"></div>
                    </div>
                </div>
                <small class="form-hint">Upload a new photo (JPEG/PNG, max 2MB) or leave empty to keep current photo</small>
            </div>

            <!-- submit buttons -->
            <div class="form-group submit-group">
                <div class="button-row">
                    <button type="submit" class="submit-btn">Update Student Information</button>
                    <button type="button" class="reset-btn-form" onclick="window.location.href='display_records.php'">Cancel</button>
                </div>
            </div>
        </form>
        
        <!-- footer navigation -->
        <div class="form-footer">
            <p>
                <a href="index.html">← Back to Home</a> | 
                <a href="display_records.php">View All Students</a>
            </p>
        </div>
    </div>

    <script>
        // Photo preview functionality
        const photoInput = document.getElementById('student_photo');
        const photoPreview = document.getElementById('photo-preview');
        const fileInfo = document.getElementById('file-info');

        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    this.value = '';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                    photoPreview.style.display = 'block';
                    fileInfo.innerHTML = `<strong>${file.name}</strong><br>Size: ${(file.size / 1024).toFixed(1)} KB`;
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submission confirmation
        document.getElementById('editForm').addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to update this student information?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
