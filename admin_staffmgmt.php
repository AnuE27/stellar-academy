<!DOCTYPE html>
<html>
<head>
    <title>Staff Management - Admin</title>
    <link rel="stylesheet" href="staffmgmt.css">
    <script src="validate.js"></script>
</head>
<body>
    <!-- Form for search and action buttons -->
    <form class="action-form" action='' method='post'>
        <select id='srchby' name='srchby'>
            <option>First Name</option>
            <option>Last Name</option>
            <option>Email</option>
        </select>
        <input type="text" id="srch" name='srch' placeholder="Type here">
        <button type="submit" id='search' name='search'>Search</button>
        <button type='submit' id='viewstaff' name='viewstaff'>Available Teachers</button>
        <button type="submit" id='add' name='add'>Add New Teacher</button>
    </form>

    <!-- Form for displaying teachers and adding new ones -->
    <form class="teacher-form" action='' method='post'>
        <?php
        include 'db.php';
        function fetch_teachers($conn, $condition) {
            $sql = "SELECT tbl_staff.ST_ID, Fname, Lname, Ph_no, Email, tbl_subject.Name AS subject_name
                    FROM tbl_staff
                    JOIN tbl_allocation ON tbl_staff.ST_ID = tbl_allocation.ST_ID
                    JOIN tbl_subject ON tbl_allocation.SUB_ID = tbl_subject.SUB_ID
                    WHERE $condition";
            $result = $conn->query($sql);
        
            $teachers = [];
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = $row['ST_ID'];
                    $subject_name = $row['subject_name'];
        
                    list($class, $subject) = explode("_", $subject_name);
        
                    if (!isset($teachers[$id])) {
                        $teachers[$id] = [
                            'Fname' => $row['Fname'],
                            'Lname' => $row['Lname'],
                            'Ph_no' => $row['Ph_no'],
                            'Email' => $row['Email'],
                            'Classes' => [],
                            'Subject' => $subject,
                        ];
                    }
        
                    $teachers[$id]['Classes'][] = $class;
                }
        
                echo "<table><tr><th>Select</th><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Email</th><th>Classes</th><th>Subject</th><th>Action</th></tr>";
                foreach ($teachers as $teacher) {
                    $classes = implode(", ", $teacher['Classes']);
                    echo "<tr><td><input type='checkbox' name='selected[]' value='" . $teacher['Email'] . "'></td>
                          <td>{$teacher['Fname']}</td><td>{$teacher['Lname']}</td><td>{$teacher['Ph_no']}</td>
                          <td>{$teacher['Email']}</td><td>{$classes}</td><td>{$teacher['Subject']}</td>
                          <td><button type='submit' name='remove_single' value='" . $teacher['Email'] . "'>Remove</button></td></tr>";
                }
                echo "</table>";
                echo "<button type='submit' name='remove_multiple'>Remove Selected</button>";
            }
        }
        if (isset($_POST['search'])) {
            $srchby = $_POST['srchby'];
            $srch = $_POST['srch'];

            if ($srchby == 'First Name') {
                $srchby = 'Fname';
            } elseif ($srchby == 'Last Name') {
                $srchby = 'Lname';
            }
            if($srch==''){
                echo "Please enter a value to search";
                if($srchby==''){
                    echo "Please select a search type";
                }
            } else {
                fetch_teachers($conn, "$srchby LIKE '%$srch%' AND Role='Teacher'");
            }
        }
        if (isset($_POST['viewstaff'])) {
            fetch_teachers($conn, "Role='Teacher'");
        }

        if (isset($_POST['add'])) {
            echo "
            <div class='aform'>
    <form action='' method='post'>
        <div class='form-group'>
            <label for='fname'>First Name:</label>
            <input type='text' id='fname' name='fname'>
        </div>
        
        <div class='form-group'>
            <label for='lname'>Last Name:</label>
            <input type='text' id='lname' name='lname'>
        </div>
        
        <div class='form-group'>
            <label for='email'>Email:</label>
            <input type='text' id='email' name='email'>
        </div>
        
        <div class='form-group'>
            <label for='ph_no'>Phone Number:</label>
            <input type='text' id='ph_no' name='ph_no'>
        </div>
        
        <div class='form-group'>
            <label for='subject'>Subject:</label>
            <select id='subject' name='subject'>
                <option value='Biology'>Biology</option>
                <option value='Maths'>Maths</option>
                <option value='Physics'>Physics</option>
                <option value='Chemistry'>Chemistry</option>
                <option value='English'>English</option>
            </select>
        </div>
        
        <div class='form-group'>
            <label>Class:</label>
            <div class='checkbox-group'>
                <input type='checkbox' id='class8' name='classes[]' value='8'>
                <label for='class8'>8</label>
                <input type='checkbox' id='class9' name='classes[]' value='9'>
                <label for='class9'>9</label>
                <input type='checkbox' id='class10' name='classes[]' value='10'>
                <label for='class10'>10</label>
                <input type='checkbox' id='class11' name='classes[]' value='11'>
                <label for='class11'>11</label>
                <input type='checkbox' id='class12' name='classes[]' value='12'>
                <label for='class12'>12</label>
            </div>
        </div>
        
        <button type='submit' onclick='return validate()' name='submit_teacher'>Add Teacher</button>
    </form>
</div>

            ";
        }

        if (isset($_POST['submit_teacher'])) {
            include 'db.php';
    
            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $email = $_POST['email'];
            $ph_no = $_POST['ph_no'];
            $subject = $_POST['subject'];
            $classes = isset($_POST['classes']) ? $_POST['classes'] : [];
    
            // Insert teacher into tbl_staff
            $sql = "INSERT INTO tbl_staff (Fname, Lname, Email, Ph_no, Role) VALUES ('$fname', '$lname', '$email', '$ph_no', 'Teacher')";
            if ($conn->query($sql)) {
                $teacher_id = $conn->insert_id;
    
                foreach ($classes as $class) {
                    $class_subject = $class . '_' . $subject;
                    $sql_class = "INSERT INTO tbl_allocation (ST_ID, SUB_ID) VALUES ('$teacher_id', (SELECT SUB_ID FROM tbl_subject WHERE Name='$class_subject'))";
                    $conn->query($sql_class);
                    $sql_check_status = "SELECT stat FROM tbl_subject WHERE Name = '$class_subject'";
                    $result = $conn->query($sql_check_status);
                    $row = $result->fetch_assoc();
                    if ($row['stat'] == 0) {
                        $sql_update_status = "UPDATE tbl_subject SET stat = 1 WHERE Name = '$class_subject'";
                        $conn->query($sql_update_status);
                    }
                }
                echo "Teacher added successfully!";
            } else {
                echo "Error adding teacher: " . $conn->error;
            }
        }

        if (isset($_POST['remove_single'])) {
            $to_remove = $_POST['remove_single'];
            $sql = "DELETE FROM tbl_staff WHERE Email = '$to_remove'";
            if ($conn->query($sql)) {
                echo "Teacher removed successfully.";
                $sql_update_status_zero = "UPDATE tbl_subject SET stat = 0 WHERE SUB_ID NOT IN (SELECT SUB_ID FROM tbl_allocation)";
                $conn->query($sql_update_status_zero);
            } else {
                echo "Error removing teacher.";
            }
        }

        if (isset($_POST['remove_multiple'])) {
            if (!isset($_POST['selected'])) {
                echo "No teachers selected";
            } else {
                $all_success = true;
                foreach ($_POST['selected'] as $to_remove) {
                    $sql = "DELETE FROM tbl_staff WHERE Email = '$to_remove'";
                    if (!$conn->query($sql)) {
                        $all_success = false;
                        echo "Error removing teacher with email $to_remove.";
                    }
                }
                if ($all_success) {
                    echo "Selected teachers removed successfully.";
                    $sql_update_status_zero = "UPDATE tbl_subject SET stat = 0 WHERE SUB_ID NOT IN (SELECT SUB_ID FROM tbl_allocation)";
                    $conn->query($sql_update_status_zero);
                }
            }
        }
        ?>
    </form>
</body>
</html>
