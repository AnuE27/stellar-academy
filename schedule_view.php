<html lang="en">
<head>
    
    <title>Schedule Overview</title>
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
    $sql = "SELECT SUBSTRING_INDEX(sub.Name, '_', -1) AS subject_name, Day, Time
            FROM tbl_schedule sch
            JOIN tbl_allocation alc ON  sch.SUBT_ID = alc.SUBT_ID
            JOIN tbl_subject sub ON alc.SUB_ID = sub.SUB_ID
            JOIN tbl_enrollment e ON sub.SUB_ID = e.SUB_ID
            JOIN tbl_stud s ON e.S_ID = s.S_ID
            WHERE s.Email = '$stud_email'";
    
    $result=$conn->query($sql);
        
        
        // Check if any attendance records are found
        if ($result->num_rows > 0) {
            echo "<table border='1'>
                    <tr>
                        <th>Subject</th>
                        <th>Day</th>
                        <th>Time</th>
                        
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        
                        <td>{$row['subject_name']}</td>
                        <td>{$row['Day']}</td>
                        <td>{$row['Time']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "No schedule records found.";
        }
    }
     else {
        echo "Error in fetching schedule.";
    }


// Fetch and display the student's attendance

$conn->close();
?>
</body>
</html>