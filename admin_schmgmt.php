<html>
<head>
    <title>Schedule Management</title>
    <link rel="stylesheet"  href="schmgmt.css">
</head>
<body>
    <div class="form-container">
        <!-- Buttons for managing fee details and fee payment status -->
        <form action="" method="post">
            <button type="submit" name="viewsch">View Schedule</button>
            <button type="submit" name="updatesch">Update Schedule</button>
        </form>
    </div>

    <?php
    // Connect to database
    include 'db.php';

   
// ... (rest of the code remains the same)

$show_second_form = false;

if (isset($_POST['viewsch'])){
    echo '<form action="" method="post">';
echo '<label for="view-by">View by:</label>';
echo '<select id="view_by" name="view_by">';
echo '<option value="">Select an option</option>';
echo '<option value="day">Day</option>';
echo '<option value="time">Time</option>';
echo '<option value="class">Class</option>';
echo '<option value="subject">Subject</option>';
echo '</select>';
echo '<br><br>';
echo '<div id="second-section" style="display: none;">';

echo '<div id="day-section" style="display: none;">';
echo '<label for="day">Day:</label><select id="day" name="day">';
echo '<option value="">Select a day</option>';
echo '<option value="Monday">Monday</option>';
echo '<option value="Tuesday">Tuesday</option>';
echo '<option value="Wednesday">Wednesday</option>';
echo '<option value="Thursday">Thursday</option>';
echo '<option value="Friday">Friday</option>';
echo '<option value="Saturday">Saturday</option>';
echo '</select><br><br>';
echo '</div>';

echo '<div id="time-section" style="display: none;">';
echo '<label for="time">Time:</label><select id="time" name="time">';
echo '<option value="">Select a time</option>';
echo '<option value="5:30-6:30">5:30-6:30</option>';
echo '<option value="6:30-7:30">6:30-7:30</option>';
echo '<option value="7:30-8:30">7:30-8:30</option>';
echo '<option value="8:30-9:30">8:30-9:30</option>';
echo '</select><br><br>';
echo '</div>';

echo '<div id="class-section" style="display: none;">';
echo '<label for="class">Class:</label><select id="class" name="class">';
echo '<option value="">Select a class</option>';
for ($i = 8; $i <= 12; $i++) {
    echo '<option value="' . $i . '">Class ' . $i . '</option>';
}
echo '</select><br><br>';
echo '</div>';

echo '<div id="subject-section" style="display: none;">';
echo '<label for="subject">Subject:</label><select id="subject" name="subject">';
echo '<option value="">Select a subject</option>';
echo '<option value="Maths">Maths</option>';
echo '<option value="Chemistry">Chemistry</option>';
echo '<option value="Physics">Physics</option>';
echo '<option value="English">English</option>';
echo '<option value="Biology">Biology</option>';
echo '</select><br><br>';
echo '</div>';

echo '<input type="hidden" name="view_by" value="">'; 
echo '<button type="submit" name="view">View</button>';
echo '</div>';
echo '</form>';

echo '<script>';
echo 'document.getElementById("view_by").addEventListener("change", function() {';
echo 'var selectedValue = this.value;';
echo 'document.getElementById("second-section").style.display = "block";';
echo 'document.querySelector("input[name=view_by]").value = selectedValue;';
echo 'document.getElementById("day-section").style.display = "none";';
echo 'document.getElementById("time-section").style.display = "none";';
echo 'document.getElementById("class-section").style.display = "none";';
echo 'document.getElementById("subject-section").style.display = "none";';
echo 'switch (selectedValue) {';
echo 'case "day":';
echo 'document.getElementById("day-section").style.display = "block";';
echo 'break;';
echo 'case "time":';
echo 'document.getElementById("time-section").style.display = "block";';
echo 'break;';
echo 'case "class":';
echo 'document.getElementById("class-section").style.display = "block";';
echo 'break;';
echo 'case "subject":';
echo 'document.getElementById("subject-section").style.display = "block";';
echo 'break;';
echo '}';
echo '});';
echo '</script>';
}
    
        if (isset($_POST['view']) && isset($_POST['view_by'])) {
            $view_by = $_POST['view_by'];
            $selected_value = $_POST[$view_by];
            
        if($view_by=='day' || $view_by=='time'){
            $query = "SELECT 
                SUBSTRING_INDEX(s.Name, '_', 1) AS class,
                SUBSTRING_INDEX(s.Name, '_', -1) AS subject,
                sch.Day, 
                sch.Time,
                CONCAT(t.Fname, ' ', t.Lname) AS teacher
              FROM 
                tbl_schedule sch
              JOIN 
                tbl_allocation a ON sch.SUBT_ID = a.SUBT_ID
              JOIN 
                tbl_subject s ON a.SUB_ID = s.SUB_ID
                JOIN tbl_staff t ON a.ST_ID=t.ST_ID
               WHERE $view_by = '$selected_value'";
        }
        elseif($view_by=='class'){
            $query = "SELECT 
                SUBSTRING_INDEX(s.Name, '_', 1) AS class,
                SUBSTRING_INDEX(s.Name, '_', -1) AS subject,
                sch.Day, 
                sch.Time,
                CONCAT(t.Fname, ' ', t.Lname) AS teacher
              FROM 
                tbl_schedule sch
              JOIN 
                tbl_allocation a ON sch.SUBT_ID = a.SUBT_ID
              JOIN 
                tbl_subject s ON a.SUB_ID = s.SUB_ID
                JOIN tbl_staff t ON a.ST_ID=t.ST_ID
               WHERE s.Name LIKE '$selected_value%'";
        }
        elseif($view_by=='subject'){
            $query = "SELECT 
                SUBSTRING_INDEX(s.Name, '_', 1) AS class,
                SUBSTRING_INDEX(s.Name, '_', -1) AS subject,
                sch.Day, 
                sch.Time,
                CONCAT(t.Fname, ' ', t.Lname) AS teacher
              FROM 
                tbl_schedule sch
              JOIN 
                tbl_allocation a ON sch.SUBT_ID = a.SUBT_ID
              JOIN 
                tbl_subject s ON a.SUB_ID = s.SUB_ID
                JOIN tbl_staff t ON a.ST_ID=t.ST_ID
               WHERE s.Name LIKE '%$selected_value'";
        }
            $result = mysqli_query($conn, $query);
        
            if (mysqli_num_rows($result) > 0) {
                echo "<table border='1'>";
                echo "<tr><th>Day</th><th>Time</th><th>Class</th><th>Subject</th><th>Teacher</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['Day'] . "</td>";
                    echo "<td>" . $row['Time'] . "</td>";
                    echo "<td>" . $row['class'] . "</td>";
                    echo "<td>" . $row['subject'] . "</td>";
                    echo "<td>" . $row['teacher'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No schedule found for $view_by = $selected_value";
            }
        }
        
       
        $message = '';

        // Handle the 'updatesch' button click
        if (isset($_POST['updatesch']) || isset($_POST['edit_mode']) || isset($_POST['update_schedule'])) {
            echo "<h2>Manage Schedule</h2>";
        
            // Display existing schedule details
            $query = "SELECT 
                        SUBSTRING_INDEX(s.Name, '_', 1) AS class,
                        SUBSTRING_INDEX(s.Name, '_', -1) AS subject,
                        sch.Day, 
                        sch.Time,
                        sch.SCH_ID
                      FROM 
                        tbl_schedule sch
                      JOIN 
                        tbl_allocation a ON sch.SUBT_ID = a.SUBT_ID
                      JOIN 
                        tbl_subject s ON a.SUB_ID = s.SUB_ID
                      ORDER BY 
                        sch.Day, sch.Time";
            $result = mysqli_query($conn, $query);
        
            if (mysqli_num_rows($result) > 0) {
                echo "<form action='' method='post'>";
                echo "<table border='1'>";
                echo "<tr><th>Class</th><th>Subject</th><th>Day</th><th>Time</th><th>Action</th></tr>";
        
                // Check if we are in edit mode
                $editable = isset($_POST['edit_mode']) && $_POST['edit_mode'] === 'true';
        
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['class'] . "</td>";
                    echo "<td>" . $row['subject'] . "</td>";
                    echo "<td><input type='text' name='day[" . $row['SCH_ID'] . "]' value='" . $row['Day'] . "' " . ($editable ? "" : "readonly") . " required></td>";
                    echo "<td><input type='text' name='time[" . $row['SCH_ID'] . "]' value='" . $row['Time'] . "' " . ($editable ? "" : "readonly") . " required></td>";
                    echo "<td><button type='submit' name='remove' value='" . $row['SCH_ID'] . "'>Remove</button></td>";
                    echo "</tr>";
                }
                echo "</table>";
        
                if ($editable) {
                    echo "<button type='submit' name='update_schedule'>Update All</button>";
                    echo "<button type='submit' name='edit_mode' value='false'>Cancel</button>";
                } else {
                    echo "<button type='submit' name='edit_mode' value='true'>Edit Schedule</button>";
                }
        
                echo "<button type='submit' name='add_new'>Add New</button>";
                echo "</form>";
            } else {
                echo "No records found.";
            }
        }
        if (isset($_POST['remove'])) {
            $sch_id = mysqli_real_escape_string($conn, $_POST['remove']);
            $query = "DELETE FROM tbl_schedule WHERE SCH_ID = '$sch_id'";
            if (mysqli_query($conn, $query)) {
                echo "Record removed successfully.";
            } else {
                echo "Error removing record: " . mysqli_error($conn);
            }
            // redirect to the same page to refresh the table
            
            exit();
        }
        
        // Handle updating schedule details
        if (isset($_POST['update_schedule'])) {
            $days = $_POST['day'];
            $times = $_POST['time'];
            $updated = false;
        
            foreach ($days as $sch_id => $day) {
                $time = $times[$sch_id];
                // Add validation for day and time as needed
                if (!preg_match('/^(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday)$/', $day)) {
                    $message = "Invalid day for schedule ID $sch_id.";
                } elseif (!preg_match('/^(5:30-6:30|6:30-7:30|7:30-8:30|8:30-9:30)$/', $time)) {
                    $message = "Invalid time for schedule ID $sch_id.";
                } else {
                    $query = "UPDATE tbl_schedule SET Day='$day', Time='$time' WHERE SCH_ID='$sch_id'";
                    if (mysqli_query($conn, $query)) {
                        $updated = true;
                    } else {
                        $message = "Error updating schedule ID $sch_id: " . mysqli_error($conn);
                    }
                }
            }
        
            if ($updated) {
                $message = "Schedule updated successfully!";
            }
        
            echo "<p>$message</p>";
        }
        

if (isset($_POST['add_new'])) {
    // Display the form for adding a new schedule
    echo '<form action="" method="post">';
    echo '<label for="class">Class:</label>';
    echo '<select id="class" name="class">';
    for ($i = 8; $i <= 12; $i++) {
        echo '<option value="' . $i . '">Class ' . $i . '</option>';
    }
    echo '</select>';
    echo '<br><br>';

    echo '<label for="subject">Subject:</label>';
    echo '<select id="subject" name="subject">';
    echo '<option value="Maths">Maths</option>';
    echo '<option value="Chemistry">Chemistry</option>';
    echo '<option value="Physics">Physics</option>';
    echo '<option value="English">English</option>';
    echo '<option value="Biology">Biology</option>';
    echo '</select>';
    echo '<br><br>';

    echo '<label for="day">Day:</label>';
    echo '<select id="day" name="day">';
    echo '<option value="Monday">Monday</option>';
    echo '<option value="Tuesday">Tuesday</option>';
    echo '<option value="Wednesday">Wednesday</option>';
    echo '<option value="Thursday">Thursday</option>';
    echo '<option value="Friday">Friday</option>';
    echo '<option value="Saturday">Saturday</option>';
    echo '</select>';
    echo '<br><br>';

    echo '<label for="time">Time:</label>';
    echo '<select id="time" name="time">';
    echo '<option value="5:30-6:30">5:30-6:30</option>';
    echo '<option value="6:30-7:30">6:30-7:30</option>';
    echo '<option value="7:30-8:30">7:30-8:30</option>';
    echo '<option value="8:30-9:30">8:30-9:30</option>';
    echo '</select>';
    echo '<br><br>';

    echo '<button type="submit" name="add_schedule">Add Schedule</button>';
    echo '</form>';
}

if (isset($_POST['add_schedule'])) {
    // Sanitize inputs
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $day = mysqli_real_escape_string($conn, $_POST['day']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);

    // Validate inputs
    $class_subject = $class . '_' . $subject;
    
        // Get the subject ID
        $query_subject = "SELECT SUB_ID FROM tbl_subject WHERE Name='$class_subject'";
        $result_subject = mysqli_query($conn, $query_subject);
        $subject_row = mysqli_fetch_assoc($result_subject);
        $subject_id = $subject_row['SUB_ID'];

        // Get the subject-teacher ID
        $query_allocation = "SELECT SUBT_ID FROM tbl_allocation WHERE SUB_ID='$subject_id'";
        $result_allocation = mysqli_query($conn, $query_allocation);
        if (mysqli_num_rows($result_allocation) > 0) {
            $allocation_row = mysqli_fetch_assoc($result_allocation);
            $subt_id = $allocation_row['SUBT_ID'];
            $query_check = "
            SELECT 1
            FROM tbl_schedule 
            WHERE Day = '$day' AND Time = '$time'
        ";
        $result_check = mysqli_query($conn, $query_check);
        if (mysqli_num_rows($result_check) > 0) {
            echo "The combination of day and time already exists.";
        } else {
            // Insert into tbl_schedule
            $query_insert = "INSERT INTO tbl_schedule (SUBT_ID, Day, Time) VALUES ('$subt_id', '$day', '$time')";
            if (mysqli_query($conn, $query_insert)) {
                echo "Record added successfully.";
            } else {
                echo "Error adding record: " . mysqli_error($conn);
            }
        }
        } else {
            echo "No teacher allocated for the selected subject and class.";
        }
    }



// Close connection
mysqli_close($conn);




        ?>
        
       
        
</body>
</html>