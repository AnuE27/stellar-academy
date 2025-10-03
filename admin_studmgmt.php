<!DOCTYPE html>
<html>
<head>
    <title>Student Management - Admin</title>
    <link rel="stylesheet" href="studmgmtcss.css">
</head>
<body>
<div class="button-container">
    <!-- Form for search and management actions -->
    <form id='a' action='' method='post'>
        <select id='srchby' name='srchby'>
            <option value='Fname'>First Name</option>
            <option value='Lname'>Last Name</option>
            <option value='Email'>Email</option>
        </select>
        <input type="text" id="srch" name='srch' placeholder="Type here">
        <button type="submit" id='search' name='search'>Search</button>
        <button type='submit' id='viewstud' name='viewstud'>Enrolled Students</button>
        <button type="submit" id='application' name='application'>Applications</button>
    </form>
</div>


    <!-- Table for displaying student information -->
    <form action='' method='post'>
    <?php
include 'db.php';

function fetch_stud($conn, $condition) {
    $sql = "SELECT tbl_stud.S_ID, Fname, Lname, Ph_no, Email, Guar_ph, DOB, tbl_subject.Name AS subject_name, adm_status
            FROM tbl_stud
            JOIN tbl_enrollment ON tbl_stud.S_ID = tbl_enrollment.S_ID
            JOIN tbl_subject ON tbl_enrollment.SUB_ID = tbl_subject.SUB_ID
            WHERE $condition";
    $result = $conn->query($sql);

    $students = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id = $row['S_ID'];
            $subject_name = $row['subject_name'];

            list($class, $subject) = explode("_", $subject_name);

            if (!isset($students[$id])) {
                $students[$id] = [
                    'Fname' => $row['Fname'],
                    'Lname' => $row['Lname'],
                    'Ph_no' => $row['Ph_no'],
                    'Email' => $row['Email'],
                    'Guar_ph' => $row['Guar_ph'],
                    'DOB' => $row['DOB'],
                    'Class' => $class,
                    'Subjects' => [],
                    'adm_status' => $row['adm_status'],
                ];
            }

            $students[$id]['Subjects'][] = $subject;
        }

        echo "<table><tr><th>Select</th><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Email</th><th>Guardian Phone</th><th>Date of Birth</th><th>Class</th><th>Subjects</th><th>Action</th></tr>";
        foreach ($students as $student) {
            $subjects = implode(", ", $student['Subjects']);
            echo "<tr><td><input type='checkbox' name='selected[]' value='" . $student['Email'] . "'></td>
                  <td>{$student['Fname']}</td><td>{$student['Lname']}</td><td>{$student['Ph_no']}</td>
                  <td>{$student['Email']}</td><td>{$student['Guar_ph']}</td><td>{$student['DOB']}</td>
                  <td>{$student['Class']}</td><td>{$subjects}</td>";

            if ($student['adm_status'] == 0) {
                echo "<td><button type='submit' name='accept' value='" . $student['Email'] . "'>Accept</button>
                      <button type='submit' name='reject' value='" . $student['Email'] . "'>Reject</button></td></tr>";
            } else {
                echo "<td><button type='submit' name='remove_single' value='" . $student['Email'] . "'>Remove</button></td></tr>";
            }
        }
        echo "</table>";
        echo "<button type='submit' name='remove_multiple'>Remove Selected</button>";
    } else {
        echo "<p>No results found</p>";
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
    if ($srch == '') {
        echo "<p>Please enter a value to search</p>";
        if ($srchby == '') {
            echo "<p>Please select a search type</p>";
        }
    } else {
        fetch_stud($conn, "$srchby LIKE '%$srch%'");
    }
}

if (isset($_POST['viewstud'])) {
    fetch_stud($conn, "adm_status='1'");
}

if (isset($_POST['application'])) {
    fetch_stud($conn, "adm_status='0'");
}

if (isset($_POST['remove_single'])) {
    $to_remove = $_POST['remove_single'];
    $sql = "DELETE FROM tbl_stud WHERE Email = '$to_remove'";
    if ($conn->query($sql)) {
        echo "<p>Student removed successfully.</p>";
    } else {
        echo "<p>Error removing student.</p>";
    }
}

if (isset($_POST['remove_multiple'])) {
    if (!isset($_POST['selected'])) {
        echo "<p>No students selected</p>";
    } else {
        $all_success = true;
        foreach ($_POST['selected'] as $to_remove) {
            $sql = "DELETE FROM tbl_stud WHERE Email = '$to_remove'";
            if (!$conn->query($sql)) {
                $all_success = false;
                echo "<p>Error removing student with email $to_remove.</p>";
            }
        }
        if ($all_success) {
            echo "<p>Selected students removed successfully.</p>";
        }
    }
}

if (isset($_POST['accept'])) {
    $acc = $_POST['accept'];
    $sql1 = "UPDATE tbl_stud SET adm_status='1' WHERE Email = '$acc'";
    if ($conn->query($sql1)) {
        echo "<p>Application accepted successfully.</p>";
    } else {
        echo "<p>Error accepting application.</p>";
    }
    
}

if (isset($_POST['reject'])) {
    $rej = $_POST['reject'];
    $sql = "DELETE FROM tbl_stud WHERE Email = '$rej'";
    if ($conn->query($sql)) {
        echo "<p>Application rejected successfully.</p>";
    } else {
        echo "<p>Error rejecting application.</p>";
    }
}
?>

    </form>
</body>
</html>
