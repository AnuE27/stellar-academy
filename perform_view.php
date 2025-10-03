<html lang="en">
<head>
    
    <title>Performance Overview</title>
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
    $sql = "SELECT SUBSTRING_INDEX(sub.Name, '_', -1) AS subject_name, p.S1, p.S2, p.S3
            FROM tbl_performance p
            JOIN tbl_enrollment e ON p.E_ID = e.E_ID
            JOIN tbl_subject sub ON e.SUB_ID = sub.SUB_ID
            JOIN tbl_stud s ON e.S_ID = s.S_ID
            WHERE s.Email = '$stud_email'";
    
    $result=$conn->query($sql);
        
        
        // Check if any attendance records are found
        if ($result->num_rows > 0) {
            echo "<table border='1'>
                    <tr>
                        <th>Subject</th>
                        <th>Semester 1</th>
                        <th>Semester 2</th>
                        <th>Semester 3</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        
                        <td>{$row['subject_name']}</td>
                        <td>" . ($row['S1'] !== null ? $row['S1'] : 'N/A') . "</td>
                        <td>" . ($row['S2'] !== null ? $row['S2'] : 'N/A') . "</td>
                        <td>" . ($row['S3'] !== null ? $row['S3'] : 'N/A') . "</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "No performance records found.";
        }
    }
     else {
        echo "Error in fetching performance.";
    }


// Fetch and display the student's attendance

$conn->close();
?>
</body>
</html>