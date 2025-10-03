<html lang="en">
<head>
    
    <title>Attendance Overview</title>
    <link rel="stylesheet" href="attend_viewcss.css"> <!-- Link to your CSS file -->
</head>
<body>
<?php

include 'db.php'; // Include the database connection

// Check if the student is logged in
if (isset($_SESSION['login'])) {
$stud_email = $_SESSION['login']; // Get the student ID from the session

// Function to fetch attendance

    // Query to fetch attendance data for the student
    $sql = "SELECT a.Att_date, SUBSTRING_INDEX(sub.Name, '_', -1) AS subject_name, a.Status
            FROM tbl_attendance a
            JOIN tbl_enrollment e ON a.E_ID = e.E_ID
            JOIN tbl_subject sub ON e.SUB_ID = sub.SUB_ID
            JOIN tbl_stud s ON e.S_ID = s.S_ID
            WHERE s.Email = '$stud_email'";
    
    $result=$conn->query($sql);
        
        
        // Check if any attendance records are found
        if ($result->num_rows > 0) {
            echo "<table border='1'>
                    <tr>
                        <th>Date</th>
                        <th>Subject</th>
                        <th>Status</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['Att_date']}</td>
                        <td>{$row['subject_name']}</td>
                        <td>{$row['Status']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "No attendance records found.";
        }
    }
     else {
        echo "Error in fetching attendance.";
    }


// Fetch and display the student's attendance

$conn->close();
?>
</body>
</html>