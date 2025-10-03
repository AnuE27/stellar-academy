<html lang="en">
<head>
    
    <title>Schedule Overview</title>
    <link rel="stylesheet" href="attend_viewcss.css"> <!-- Link to your CSS file -->
</head>
<body>
<?php

include 'db.php'; // Include the database connection

// Check if the student is logged in
$teacherEmail = $_SESSION['login'];


    // Query to fetch attendance data for the student
    $sql = "SELECT SUBSTRING_INDEX(sub.Name, '_', 1) AS class, Day, Time
            FROM tbl_schedule sch
            JOIN tbl_allocation alc ON  sch.SUBT_ID = alc.SUBT_ID
            JOIN tbl_subject sub ON alc.SUB_ID = sub.SUB_ID
            
            JOIN tbl_staff s ON alc.ST_ID = s.ST_ID
            WHERE s.Email = '$teacherEmail'";
    
    $result=$conn->query($sql);
        
        
        // Check if any attendance records are found
        if ($result->num_rows > 0) {
            echo "<table border='1'>
                    <tr>
                        <th>Class</th>
                        <th>Day</th>
                        <th>Time</th>
                        
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        
                        <td>{$row['class']}</td>
                        <td>{$row['Day']}</td>
                        <td>{$row['Time']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "No schedule records found.";
        }
    


// Fetch and display the student's attendance

$conn->close();
?>
</body>
</html>