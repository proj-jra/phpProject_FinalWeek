<?php
/**
 * student records management page
 * displays all students with search/filter functionality
 */

// database configuration
$host = 'localhost';
$dbname = 'school_db';
$username = 'root';
$password = '';

// establish database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("connection failed: " . $e->getMessage());
}

// get search parameters from url
$search_course = $_GET['course'] ?? '';
$search_year = $_GET['year'] ?? '';
$search_name = $_GET['name'] ?? '';

// build dynamic query with filters
$sql = "SELECT * FROM students";
$params = [];

// add where conditions if filters are provided
if ($search_course || $search_year || $search_name) {
    $conditions = [];
    
    // course filter (partial match)
    if ($search_course) {
        $conditions[] = "course LIKE ?";
        $params[] = '%' . $search_course . '%';
    }
    
    // year level filter (exact match)
    if ($search_year) {
        $conditions[] = "year_level = ?";
        $params[] = $search_year;
    }
    
    // student name filter (partial match)
    if ($search_name) {
        $conditions[] = "full_name LIKE ?";
        $params[] = '%' . $search_name . '%';
    }
    
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// order by most recent first
$sql .= " ORDER BY created_at DESC";

// execute query and fetch results
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// get enrollment statistics for dashboard
$count_sql = "SELECT course, COUNT(*) as count FROM students GROUP BY course";
$course_counts = $pdo->query($count_sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container wide">
        <!-- page header with title -->
        <div class="form-header">
            <div class="logo-container">
                <img src="logo.webp" alt="Institution Logo" class="form-logo" style="max-width: 150px; height: auto;">
            </div>
            <h1>Student Records Management</h1>
            <p>Comprehensive database of all currently registered students</p>
        </div>
        
        <div style="padding: 40px;">
            <!-- search and filter form -->
            <form method="GET" class="search-form">
                <h3>Search & Filter Options</h3>
                <div class="search-row">
                    <!-- name search input -->
                    <input type="text" name="name" placeholder="Search by Student Name" 
                           value="<?php echo htmlspecialchars($search_name); ?>">
                    
                    <!-- course search input -->
                    <input type="text" name="course" placeholder="Search by Course" 
                           value="<?php echo htmlspecialchars($search_course); ?>">
                    
                    <!-- year level dropdown filter -->
                    <select name="year">
                        <option value="">All Years</option>
                        <option value="1" <?php echo ($search_year == '1') ? 'selected' : ''; ?>>First Year</option>
                        <option value="2" <?php echo ($search_year == '2') ? 'selected' : ''; ?>>Second Year</option>
                        <option value="3" <?php echo ($search_year == '3') ? 'selected' : ''; ?>>Third Year</option>
                        <option value="4" <?php echo ($search_year == '4') ? 'selected' : ''; ?>>Fourth Year (Graduating)</option>
                    </select>
                    
                    <!-- action buttons -->
                    <button type="submit" class="search-btn">Search</button>
                    <a href="display_records.php" class="reset-btn">Reset</a>
                </div>
            </form>

            <!-- enrollment statistics dashboard -->
            <div class="stats-container">
                <h3>Enrollment Statistics</h3>
                <div class="stats-grid">
                    <?php foreach ($course_counts as $count): ?>
                        <div class="stat-item">
                            <strong><?php echo htmlspecialchars($count['course']); ?></strong><br>
                            <?php echo $count['count']; ?> students enrolled
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- student records data table -->
            <div class="table-container">
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>DOB</th>
                                <th>Gender</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($students): ?>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <!-- student photo or placeholder -->
                                        <td style="text-align: center;">
                                            <?php if (!empty($student['photo']) && file_exists($student['photo'])): ?>
                                                <img src="<?php echo htmlspecialchars($student['photo']); ?>" 
                                                     alt="photo" 
                                                     style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                            <?php else: ?>
                                                <div style="width: 50px; height: 50px; border-radius: 50%; background: #ccc; display: flex; align-items: center; justify-content: center; font-size: 0.7rem;">N/A</div>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- student basic info -->
                                        <td><?php echo $student['id']; ?></td>
                                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                        <td><?php echo $student['dob']; ?></td>
                                        <td><?php echo $student['gender']; ?></td>
                                        <td><?php echo htmlspecialchars($student['course']); ?></td>
                                        
                                        <!-- year level with text conversion -->
                                        <td><?php 
                                            $year_level = intval($student['year_level']);
                                            switch($year_level) {
                                                case 1: echo 'First Year'; break;
                                                case 2: echo 'Second Year'; break;
                                                case 3: echo 'Third Year'; break;
                                                case 4: echo 'Fourth Year (Graduating)'; break;
                                                default: echo 'Year ' . htmlspecialchars($student['year_level']);
                                            }
                                        ?></td>
                                        
                                        <!-- contact info and dates -->
                                        <td><?php echo htmlspecialchars($student['contact_number']); ?></td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo $student['created_at']; ?></td>
                                        
                                        <!-- action buttons -->
                                        <td style="text-align: center;">
                                            <div style="display: flex; gap: 8px; justify-content: center;">
                                                <button onclick="window.location.href='edit_student.php?id=<?php echo $student['id']; ?>'" 
                                                        class="edit-btn" title="Edit Student">Edit</button>
                                                <button onclick="confirmDelete(<?php echo $student['id']; ?>, '<?php echo htmlspecialchars($student['full_name'], ENT_QUOTES); ?>')" 
                                                        class="delete-btn" title="Delete Student">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- no results message -->
                                <tr>
                                    <td colspan="11" style="text-align: center; padding: 48px;">
                                        No students found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- add new student button -->
            <div style="text-align: center; margin-top: 32px; display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
                <button onclick="window.location.href='index.html'" class="search-btn" 
                        style="width: auto; padding: 16px 32px; max-width: 300px;">← Back to Home</button>
                <button onclick="window.location.href='form.html'" class="submit-btn" 
                        style="width: auto; padding: 16px 32px; max-width: 300px;">Register New Student</button>
            </div>
        </div>
    </div>

    <!-- delete confirmation modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Confirm Deletion</h3>
            <p id="deleteMessage">Are you sure you want to delete this student record? This action cannot be undone.</p>
            <div class="modal-buttons">
                <button onclick="executeDelete()" class="confirm-btn">Yes, Delete</button>
                <button onclick="closeDeleteModal()" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        // global variable for delete functionality
        let deleteStudentId = null;

        /**
         * show delete confirmation modal
         * @param {number} studentId - id of student to delete
         * @param {string} studentName - name of student for confirmation
         */
        function confirmDelete(studentId, studentName) {
            deleteStudentId = studentId;
            document.getElementById('deleteMessage').innerHTML = 
                `Are you sure you want to delete <strong>${studentName}</strong>? This action cannot be undone.`;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        /**
         * execute the delete operation
         */
        function executeDelete() {
            if (deleteStudentId) {
                window.location.href = `delete_student.php?id=${deleteStudentId}`;
            }
        }

        /**
         * close the delete confirmation modal
         */
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            deleteStudentId = null;
        }
    </script>
</body>
</html>