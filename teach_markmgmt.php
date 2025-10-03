<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Performance Management - Teacher</title>
    <link rel="stylesheet" href="staffmgmt.css">
</head>
<body>
    <h1>Student Performance Management</h1>
    
    <!-- Buttons to view performance history and add new performance entry -->
    <form method="POST">
        <button type="submit" name="action" value="viewHistory">View Student Performance History</button>
        <button type="submit" name="action" value="addEntry">Add Student Performance Entry</button>
    </form>
    
    <?php
include 'db.php';
// Ensure session is started to access session variables
$teacherEmail = $_SESSION['login'];

// Fetch the teacher's ID based on their email
$teacherQuery = "SELECT ST_ID FROM tbl_staff WHERE Email = '$teacherEmail'";
$teacherResult = $conn->query($teacherQuery);
if (!$teacherResult) {
    echo "<p>Error fetching teacher ID: " . $conn->error . "</p>";
    exit;
}
$teacherData = $teacherResult->fetch_assoc();
$teacherId = $teacherData['ST_ID'];

// Fetch the subjects allocated to the logged-in teacher
$subjectsQuery = "SELECT SUB_ID FROM tbl_allocation WHERE ST_ID = '$teacherId'";
$subjectsResult = $conn->query($subjectsQuery);
if (!$subjectsResult) {
    echo "<p>Error fetching allocated subjects: " . $conn->error . "</p>";
    exit;
}

$allocatedSubjects = [];
while ($subject = $subjectsResult->fetch_assoc()) {
    $allocatedSubjects[] = $subject['SUB_ID'];
}
$allocatedSubjectsString = implode(",", $allocatedSubjects);

// Fetch classes based on allocated subjects
$classesQuery = "SELECT DISTINCT SUBSTRING_INDEX(Name, '_', 1) AS Class FROM tbl_subject WHERE SUB_ID IN ($allocatedSubjectsString)";
$classesResult = $conn->query($classesQuery);
if (!$classesResult) {
    echo "<p>Error fetching classes: " . $conn->error . "</p>";
    exit;
}

$semesterMapping = [
    'Semester 1' => 'S1',
    'Semester 2' => 'S2',
    'Semester 3' => 'S3'
];

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'viewHistory') {
    // View Student Performance History
?>
    <h2>View Student Performance History</h2>
    
    <form method="POST" action="" class="aform">
    <div class="form-group">
        <label for="class">Select Class:</label>
        <select id="class" name="class" required>
            <option value="">--Select Class--</option>
            <?php while ($class = $classesResult->fetch_assoc()): ?>
                <option value="<?php echo $class['Class']; ?>">
                    <?php echo $class['Class']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <button type="submit" name="action" value="viewHistory">Submit</button>
</form>


    <?php
    if (isset($_POST['class'])) {
        $selectedClass = $_POST['class'];

        // Query to get students and their performance based on the selected class
        $sql = "SELECT s.S_ID, CONCAT(s.Fname, ' ', s.Lname) AS student_name,
        p.S1, p.S2, p.S3
        FROM tbl_stud s
        JOIN tbl_enrollment e ON s.S_ID = e.S_ID
        JOIN tbl_performance p ON e.E_ID = p.E_ID
        JOIN tbl_subject sub ON e.SUB_ID = sub.SUB_ID
        WHERE SUBSTRING_INDEX(sub.Name, '_', 1) = '$selectedClass' AND sub.SUB_ID IN ($allocatedSubjectsString)
        GROUP BY s.S_ID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<h2>Performance Details for Class $selectedClass</h2>";
            echo "<table border='1'>
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Semester 1</th>
                            <th>Semester 2</th>
                            <th>Semester 3</th>
                        </tr>
                    </thead>
                    <tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['student_name'] . "</td>
                        <td>" . ($row['S1'] !== null ? $row['S1'] : 'N/A') . "</td>
                        <td>" . ($row['S2'] !== null ? $row['S2'] : 'N/A') . "</td>
                        <td>" . ($row['S3'] !== null ? $row['S3'] : 'N/A') . "</td>
                      </tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p>No performance data found for Class $selectedClass.</p>";
        }
    }

} elseif ($action == 'addEntry' || $action == 'showStudents') {
    ?>
    <h2>Add Student Performance Entry</h2>
    <form method="POST" action="" class="aform">
    <div class="form-group">
        <label for="class">Select Class:</label>
        <select id="class" name="class" required>
            <option value="">--Select Class--</option>
            <?php
            // Re-fetch classes since the original result set is exhausted
            $classesResult = $conn->query($classesQuery);
            while ($class = $classesResult->fetch_assoc()): ?>
                <option value="<?php echo $class['Class']; ?>" 
                    <?php echo (isset($_POST['class']) && $_POST['class'] == $class['Class']) ? 'selected' : ''; ?>>
                    <?php echo $class['Class']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="semester">Select Semester:</label>
        <select id="semester" name="semester" required>
            <option value="">--Select Semester--</option>
            <?php foreach ($semesterMapping as $displayName => $dbValue): ?>
                <option value="<?php echo $dbValue; ?>" 
                    <?php echo (isset($_POST['semester']) && $_POST['semester'] == $dbValue) ? 'selected' : ''; ?>>
                    <?php echo $displayName; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <button type="submit" name="action" value="showStudents">Show Students</button>
</form>

    <?php
    if ($action == 'showStudents' && isset($_POST['class']) && isset($_POST['semester'])) {
        $selectedClass = $_POST['class'];
        $selectedSemester = $_POST['semester'];
    
        // Query to check if entries already exist and fetch them if they do
        $existingEntriesQuery = "SELECT s.S_ID, CONCAT(s.Fname, ' ', s.Lname) AS student_name, e.E_ID, p.$selectedSemester AS score
                                 FROM tbl_stud s
                                 JOIN tbl_enrollment e ON s.S_ID = e.S_ID
                                 JOIN tbl_subject sub ON e.SUB_ID = sub.SUB_ID
                                 LEFT JOIN tbl_performance p ON e.E_ID = p.E_ID
                                 WHERE SUBSTRING_INDEX(sub.Name, '_', 1) = '$selectedClass' 
                                 AND sub.SUB_ID IN ($allocatedSubjectsString)";
        $result = $conn->query($existingEntriesQuery);
    
        if ($result && $result->num_rows > 0) {
            echo "<h2>Performance Entry for Class $selectedClass - " . array_search($selectedSemester, $semesterMapping) . "</h2>";
            echo "<form method='POST' action=''>
                    <input type='hidden' name='class' value='$selectedClass'>
                    <input type='hidden' name='semester' value='$selectedSemester'>
                    <table border='1'>
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody>";
    
            while ($row = $result->fetch_assoc()) {
                $enrollmentId = $row['E_ID'];
                $currentScore = $row['score'] ?? ''; // Display existing score if available, or leave blank
                echo "<tr>
                        <td>" . $row['student_name'] . "</td>
                        <input type='hidden' name='enrollment_ids[]' value='$enrollmentId'>
                        <td><input type='text' name='score[$enrollmentId]' value='$currentScore' required></td>
                      </tr>";
            }
    
            echo "</tbody></table>
                  <button type='submit' name='action' value='submitScores'>Submit Scores</button>
                  </form>";
        } else {
            echo "<p>No students found for Class $selectedClass.</p>";
        }
    }
    

} elseif ($action == 'submitScores' && isset($_POST['score'])) {
   
    $selectedClass = $_POST['class'];
        $selectedSemester = $_POST['semester'];
        $enrollmentIds = $_POST['enrollment_ids'];
        $marks = $_POST['score'];
        

        // Update marks for each enrollment ID
        foreach ($enrollmentIds as $enrollmentId) {
            $mark = isset($marks[$enrollmentId]) ? $marks[$enrollmentId] : '0';

            $performanceQuery = "INSERT INTO tbl_performance (E_ID, $selectedSemester)
                     VALUES ('$enrollmentId', '$mark')
                     ON DUPLICATE KEY UPDATE $selectedSemester = '$mark'";

            $conn->query($performanceQuery);

            
        }
        echo "<p>Scores successfully submitted for Class $selectedClass - " . array_search($selectedSemester, $semesterMapping) . ".</p>";
    }
?>

</body>
</html>
