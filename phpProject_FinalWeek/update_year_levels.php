<?php
/**
 * utility script to fix year level data format
 * converts text year levels to numeric values for consistency
 */

// database configuration
$host = 'localhost';
$dbname = 'school_db';
$username = 'root';
$password = '';

try {
    // establish database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>fixing year level data:</h2>";
    
    // conversion mapping from text to numeric
    $updates = [
        ["UPDATE students SET year_level = 1 WHERE year_level = 'First Year' OR year_level = '1'", "converting to 1"],
        ["UPDATE students SET year_level = 2 WHERE year_level = 'Second Year' OR year_level = '2'", "converting to 2"],
        ["UPDATE students SET year_level = 3 WHERE year_level = 'Third Year' OR year_level = '3'", "converting to 3"],
        ["UPDATE students SET year_level = 4 WHERE year_level = 'Fourth Year (Graduating)' OR year_level = '4'", "converting to 4"]
    ];
    
    // execute each update query
    foreach ($updates as $update) {
        $result = $pdo->exec($update[0]);
        echo "updated {$result} records: {$update[1]}<br>";
    }
    
    // display updated data for verification
    echo "<br><h2>current data after fix:</h2>";
    $stmt = $pdo->query("SELECT id, full_name, year_level FROM students ORDER BY id");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>id</th><th>name</th><th>year level (numeric)</th><th>display as</th></tr>";
    
    foreach ($students as $student) {
        $year_level = intval($student['year_level']);
        
        // convert numeric to display text
        $display = '';
        switch($year_level) {
            case 1: $display = 'first year'; break;
            case 2: $display = 'second year'; break;
            case 3: $display = 'third year'; break;
            case 4: $display = 'fourth year (graduating)'; break;
            default: $display = 'year ' . $year_level;
        }
        
        echo "<tr><td>{$student['id']}</td><td>{$student['full_name']}</td><td>{$student['year_level']}</td><td>{$display}</td></tr>";
    }
    echo "</table><br>";
    
    // success message and navigation
    echo "<br><strong style='color: green; font-size: 18px;'>✓ year levels fixed successfully!</strong><br><br>";
    echo "<a href='index.html' style='background: #6b7280; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>back to home</a>";
    echo "<a href='display_records.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>view student records</a>";
    
} catch(PDOException $e) {
    echo "<strong style='color: red;'>error: " . $e->getMessage() . "</strong>";
}
?>
