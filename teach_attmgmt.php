<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management - Teacher</title>
    <link rel="stylesheet" href="staffmgmt.css">
    <script>
        // Set max date for the date input field to prevent future dates
        window.onload = function() {
            var today = new Date().toISOString().split('T')[0];
            document.getElementById('date').setAttribute('max', today);
        };
    </script>
</head>
<body>
    <h1>Attendance Management</h1>
    
    <!-- Buttons to view attendance history and add new attendance -->
    <form method="POST">
        <button type="submit" name="action" value="viewHistory">View Attendance History</button>
        <button type="submit" name="action" value="addEntry">Add Attendance Entry</button>
    </form>
    
    <?php
    
    include 'db.php';
    $teacherEmail = $_SESSION['login'];

    // Fetch the teacher's ID based on their email
    $teacherQuery = "SELECT ST_ID FROM tbl_staff WHERE Email = '$teacherEmail'";
    $teacherResult = $conn->query($teacherQuery);
    $teacherData = $teacherResult->fetch_assoc();
    $teacherId = $teacherData['ST_ID'];

    // Fetch the subjects allocated to the logged-in teacher
    $subjectsQuery = "SELECT SUB_ID FROM tbl_allocation WHERE ST_ID = '$teacherId'";
    $subjectsResult = $conn->query($subjectsQuery);

    $allocatedSubjects = [];
    while ($subject = $subjectsResult->fetch_assoc()) {
        $allocatedSubjects[] = $subject['SUB_ID'];
    }

    $allocatedSubjectsString = implode(",", $allocatedSubjects);

    // Fetch classes based on allocated subjects
    $classesQuery = "SELECT DISTINCT SUBSTRING_INDEX(Name, '_', 1) AS Class FROM tbl_subject WHERE SUB_ID IN ($allocatedSubjectsString)";
    $classesResult = $conn->query($classesQuery);
    
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action == 'addEntry') {
    ?>
        <h2>Add Attendance Entry</h2>
        <div class="aform">
    <form action="" method="POST">
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
        </div>
<br>
        <div class="form-group">
            <label for="class">Class:</label>
            <select id="class" name="class" required>
                <?php while ($class = $classesResult->fetch_assoc()) { ?>
                    <option value="<?php echo $class['Class']; ?>"><?php echo $class['Class']; ?></option>
                <?php } ?>
            </select>
        </div>

        <button type="submit" name="viewstud">View Students</button>
    </form>
</div>

    <?php
    }

    if (isset($_POST['viewstud'])) {
        $date = $_POST['date'];
        $classId = $_POST['class'];

        // Fetch unique students for the selected class
        $studentsQuery = "SELECT DISTINCT s.S_ID, CONCAT(s.Fname, ' ', s.Lname) AS name 
                          FROM tbl_stud s
                          JOIN tbl_enrollment e ON s.S_ID = e.S_ID
                          JOIN tbl_subject sub ON e.SUB_ID = sub.SUB_ID
                          WHERE SUBSTRING_INDEX(sub.Name, '_', 1) = '$classId'
                          AND sub.SUB_ID IN ($allocatedSubjectsString)";
        $studentsResult = $conn->query($studentsQuery);
        
        if ($studentsResult->num_rows > 0) {
            ?>
            <h3>Mark Attendance for Class <?php echo $classId; ?> on <?php echo $date; ?></h3>
            <div class="aform">
    <form method="POST" action="">
        <input type="hidden" name="date" value="<?php echo $date; ?>">
        <input type="hidden" name="class" value="<?php echo $classId; ?>">

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Present</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($student = $studentsResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $student['name']; ?></td>
                        <td>
                            <input type="checkbox" name="present_students[]" value="<?php echo $student['S_ID']; ?>">
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <br>
        <button type="submit" name="submitAttendance">Submit Attendance</button>
    </form>
</div>

            <?php
        } else {
            echo "<p>No students found for this class.</p>";
        }
    }

    if (isset($_POST['submitAttendance'])) {
        $date = $_POST['date'];
        $classId = $_POST['class'];
        $presentStudents = isset($_POST['present_students']) ? $_POST['present_students'] : [];

        // Insert attendance records
        $studentsQuery = "SELECT s.S_ID,e.E_ID FROM tbl_stud s
                          JOIN tbl_enrollment e ON s.S_ID = e.S_ID
                          JOIN tbl_subject sub ON e.SUB_ID = sub.SUB_ID
                          WHERE SUBSTRING_INDEX(sub.Name, '_', 1) = '$classId'
                          AND e.SUB_ID IN ($allocatedSubjectsString)";
        $studentsResult = $conn->query($studentsQuery);

        while ($student = $studentsResult->fetch_assoc()) {
            $status = in_array($student['S_ID'], $presentStudents) ? 'Present' : 'Absent';
            $insertQuery = "INSERT INTO tbl_attendance (Att_date, E_ID, Status) 
                            VALUES ('$date', '{$student['E_ID']}','$status')
                            ON DUPLICATE KEY UPDATE Status = '$status'";
            $conn->query($insertQuery);
        }
        echo "<p>Attendance records have been added.</p>";
    } elseif ($action == 'viewHistory') {
    ?>
        <!-- Display options to view attendance by various criteria -->
        <h2>View Attendance History</h2>
        <div class="aform">
    <form method="POST">
        <input type="hidden" name="action" value="viewHistory">
        
        <div class="form-group">
            <label for="filter">Filter by:</label>
            <select name="filter" id="filter" required>
                <option value="all">All</option>
                <option value="date">Date</option>
                <option value="student">Student</option>
                <option value="class">Class</option>
            </select>
        </div>
        
        <button type="submit">Choose Filter</button>
    </form>
</div>


    <?php
        if (isset($_POST['filter'])) {
            $filter = $_POST['filter'];
            
            if ($filter == 'all') {
                // Directly display all records filtered by teacher's allocated subjects
                $sql = "SELECT a.*, CONCAT(s.Fname, ' ', s.Lname) AS student_name, 
                       SUBSTRING_INDEX(sub.Name, '_', 1) AS class_name
                FROM tbl_attendance a 
                JOIN tbl_enrollment e ON a.E_ID = e.E_ID
                JOIN tbl_stud s ON e.S_ID = s.S_ID
                JOIN tbl_subject sub ON e.SUB_ID = sub.SUB_ID
                WHERE e.SUB_ID IN ($allocatedSubjectsString)";
                
                $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "<table border='1'>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['Att_date']}</td>
                    <td>{$row['student_name']}</td>
                    <td>{$row['class_name']}</td>
                    <td>{$row['Status']}</td>";
            if ($row['Status'] == 'Absent') {
                echo "<td>
                        <form method='POST' action=''>
                            <button type='submit' name='markPresent' value='{$row['ATT_ID']}'>Mark as Present</button>
                        </form>
                      </td>";
            } else {
                echo "<td>-</td>";
            }
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No records found for the selected filter.</p>";
    }

            } else {
                ?>
                <!-- Display filter options based on selection, except for 'All' -->
                <div class="aform">
    <form method="POST">
        <input type="hidden" name="action" value="viewHistory">
        <input type="hidden" name="filter" value="<?php echo $filter; ?>">

        <?php if ($filter == 'date') { ?>
            <div class="form-group">
                <label for="date">Select Date:</label>
                <input type="date" name="filterValue" required>
            </div>
        <?php } elseif ($filter == 'student') { ?>
            <div class="form-group">
                <label for="student">Enter Student name or Email ID:</label>
                <input type="text" name="filterValue" required>
            </div>
        <?php } elseif ($filter == 'class') { ?>
            <div class="form-group">
                <label for="class">Select Class:</label>
                <select name="filterValue" required>
                    <?php while ($class = $classesResult->fetch_assoc()) { ?>
                        <option value="<?php echo $class['Class']; ?>"><?php echo $class['Class']; ?></option>
                    <?php } ?>
                </select>
            </div>
        <?php } ?>

        <button type="submit">Apply Filter</button>
    </form>
</div>

                <?php
            }
        }

        // Handling filter value submissions
       // Handling filter value submissions
if (isset($_POST['filterValue'])) {
    $filterValue = $_POST['filterValue'];

    if ($filter == 'date') {
        $sql = "SELECT a.*, CONCAT(s.Fname, ' ', s.Lname) AS student_name, 
                       SUBSTRING_INDEX(sub.Name, '_', 1) AS class_name
                FROM tbl_attendance a 
                JOIN tbl_enrollment e ON a.E_ID = e.E_ID
                JOIN tbl_stud s ON e.S_ID = s.S_ID
                JOIN tbl_subject sub ON e.SUB_ID = sub.SUB_ID
                WHERE a.Att_date = '$filterValue' AND e.SUB_ID IN ($allocatedSubjectsString)";
    } elseif ($filter == 'student') {
        $sql = "SELECT a.*, CONCAT(s.Fname, ' ', s.Lname) AS student_name, 
                       SUBSTRING_INDEX(sub.Name, '_', 1) AS class_name
                FROM tbl_attendance a 
                JOIN tbl_enrollment e ON a.E_ID = e.E_ID
                JOIN tbl_stud s ON e.S_ID = s.S_ID
                JOIN tbl_subject sub ON e.SUB_ID = sub.SUB_ID
                WHERE (s.Fname LIKE '%$filterValue%' OR s.Email LIKE '%$filterValue%')
                AND e.SUB_ID IN ($allocatedSubjectsString)";
    } elseif ($filter == 'class') {
        $sql = "SELECT a.*, CONCAT(s.Fname, ' ', s.Lname) AS student_name, 
                       SUBSTRING_INDEX(sub.Name, '_', 1) AS class_name
                FROM tbl_attendance a 
                JOIN tbl_enrollment e ON e.E_ID = e.E_ID
                JOIN tbl_stud s ON e.S_ID = s.S_ID
                JOIN tbl_subject sub ON e.SUB_ID = sub.SUB_ID
                WHERE SUBSTRING_INDEX(sub.Name, '_', 1) = '$filterValue' 
                AND e.SUB_ID IN ($allocatedSubjectsString)";
    }

    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "<table border='1'>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['Att_date']}</td>
                    <td>{$row['student_name']}</td>
                    <td>{$row['class_name']}</td>
                    <td>{$row['Status']}</td>";
            if ($row['Status'] == 'Absent') {
                echo "<td>
                        <form method='POST' action=''>
                            <button type='submit' name='markPresent' value='{$row['ATT_ID']}'>Mark as Present</button>
                        </form>
                      </td>";
            } else {
                echo "<td>-</td>";
            }
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No records found for the selected filter.</p>";
    }
}
    }
    

    // Handling 'Mark as Present' button action
    if (isset($_POST['markPresent'])) {
        $attendanceId = $_POST['markPresent'];
        $updateSql = "UPDATE tbl_attendance SET Status = 'Present' WHERE ATT_ID = '$attendanceId'";
        if ($conn->query($updateSql) === TRUE) {
            echo "<p>Record updated successfully.</p>";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
    ?>
</body>
</html>
