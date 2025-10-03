<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Management</title>
    <link rel="stylesheet" href="feemgmt.css">
</head>
<body>
    <div class="button-container">
        <form action="" method="post">
            <button type="submit" name="manage_fees" class="button">Fee Details</button>
            <button type="submit" name="fee_payment_status" class="button">Fee Payment Status</button>
        </form>
    </div>

    <?php
    include 'db.php';

    $message = '';

    // Handle the 'manage_fees' button click
    if (isset($_POST['manage_fees']) || isset($_POST['edit_mode']) || isset($_POST['update_fees'])) {
        echo "<h2>Manage Fee Details</h2>";

        // Display existing fee details with due date
        $sql = "SELECT F_ID, Type, Amount, Due_date FROM tbl_fees";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<form action='' method='post'>";
            echo "<table border='1'><tr><th>Type</th><th>Amount</th><th>Due Date</th></tr>";

            // Check if we are in edit mode
            $editable = isset($_POST['edit_mode']) && $_POST['edit_mode'] === 'true';

            while ($row = $result->fetch_assoc()) {
                $fee_id = $row['F_ID'];
                $type = $row['Type'];
                $amount = $row['Amount'];
                $due_date = $row['Due_date'];

                echo "<tr>
                        <td>$type</td>
                        <td><input type='text' name='amounts[$fee_id]' value='$amount'" . ($editable ? "" : " readonly") . " required></td>
                        <td><input type='date' name='due_dates[$fee_id]' value='$due_date'" . ($editable ? "" : " readonly") . " required></td>
                      </tr>";
            }
            echo "</table>";

            if ($editable) {
                echo "<button type='submit' name='update_fees'>Update All</button>";
                echo "<button type='submit' name='edit_mode' value='false'>Cancel</button>";
            } else {
                echo "<button type='submit' name='edit_mode' value='true'>Update Fees</button>";
            }
            
            echo "<button type='submit' name='add_new_type'>Add New Type</button>";
            echo "</form>";
        } else {
            echo "No fee details found.";
        }
    }

    // Handle updating fee details
    if (isset($_POST['update_fees'])) {
        $amounts = $_POST['amounts'];
        $due_dates = $_POST['due_dates'];
        $updated = false;

        foreach ($amounts as $fee_id => $amount) {
            $due_date = $due_dates[$fee_id];

            if (is_numeric($amount) && $amount >= 0 && $due_date) {
                $sql = "UPDATE tbl_fees SET Amount='$amount', Due_date='$due_date' WHERE F_ID='$fee_id'";
                if ($conn->query($sql)) {
                    $updated = true;
                } else {
                    $message = "Error updating fee ID $fee_id: " . $conn->error;
                }
            } else {
                $message = "Please enter a valid amount and due date.";
            }
        }

        if ($updated) {
            $message = "Fee details updated successfully!";
        }

        echo "<p>$message</p>";
    }

    // Handle adding a new fee type
    if (isset($_POST['add_new_type'])) {
        echo "<h3>Add New Fee Type</h3>";
        echo "<div class='add-fee-form'>
    <form action='' method='post'>
        <div class='form-group'>
            <label for='new_type'>Type:</label>
            <input type='text' id='new_type' name='new_type' required>
        </div>
        
        <div class='form-group'>
            <label for='new_amount'>Amount:</label>
            <input type='text' id='new_amount' name='new_amount' required>
        </div>
        
        <div class='form-group'>
            <label for='new_due_date'>Due Date:</label>
            <input type='date' id='new_due_date' name='new_due_date' required>
        </div>
        
        <button type='submit' name='submit_new_type'>Add Type</button>
    </form>
</div>
";
    }

    // Handle submitting a new fee type
    if (isset($_POST['submit_new_type'])) {
        $new_type = $_POST['new_type'];
        $new_amount = $_POST['new_amount'];
        $new_due_date = $_POST['new_due_date'];

        if (is_numeric($new_amount) && $new_amount >= 0 && $new_due_date) {
            $sql = "INSERT INTO tbl_fees (Type, Amount, Due_date) VALUES ('$new_type', '$new_amount', '$new_due_date')";
            if ($conn->query($sql)) {
                $message = "New fee type added successfully!";
            } else {
                $message = "Error adding new fee type: " . $conn->error;
            }
        } else {
            $message = "Please enter a valid amount and due date.";
        }

        echo "<p>$message</p>";
    }
    

// Section 2: View Fee Payment Status by Fee Type and Payment Status
if (isset($_POST['fee_payment_status'])) {
    // Section 1: Search by Student
echo "<div class='add-fee-form'><h2>Search by Student</h2>
<form action='' method='post'>
    <div class='form-group'>
        <label for='search_by'>Search By:</label>
        <select id='search_by' name='search_by'>
            <option value=''>--Select--</option>
            <option value='Fname'>First Name</option>
            <option value='Lname'>Last Name</option>
            <option value='Email'>Email</option>
        </select>
    </div>
    <div class='form-group'>
        <label for='search_value'>Search Value:</label>
        <input type='text' id='search_value' name='search_value'>
    </div>
    <button type='submit' name='search_student'>Search</button>
</form>";

// Section 2: View Fee Payment Status by Fee Type and Payment Status
echo "<h2>View Fee Payment Status</h2>
<form action='' method='post'>
    <div class='form-group'>
        <label for='fee_type'>Fee Type:</label>
        <select id='fee_type' name='fee_type'>
            <option value=''>--Select Fee Type--</option>";

// Populate fee types from tbl_fees
$sql_fees = "SELECT F_ID, Type FROM tbl_fees";
$result_fees = $conn->query($sql_fees);
if ($result_fees->num_rows > 0) {
while ($row_fee = $result_fees->fetch_assoc()) {
  $type = $row_fee['Type'];
  $id = $row_fee['F_ID'];
  echo "<option value='$id'>$type</option>";
}
}
echo "      </select>
    </div>
    <div class='form-group'>
        <label for='payment_status'>Payment Status:</label>
        <select id='payment_status' name='payment_status'>
            <option value=''>--Select Payment Status--</option>
            <option value='paid'>Paid</option>
            <option value='unpaid'>Unpaid</option>
        </select>
    </div>
    <button type='submit' name='view_status'>View Status</button>
</form></div>";

}
if (isset($_POST['search_student'])) {
    $search_by = $_POST['search_by'];
    $search_value = $_POST['search_value'];

    if ($search_by && $search_value) {
        // Query to retrieve student information along with fee types
       

        $sql = "
        SELECT tbl_fees.F_ID, tbl_fees.Type, tbl_feepayment.S_ID, tbl_feepayment.Payment_date, tbl_feepayment.FP_ID, 
               filtered_students.Fname, filtered_students.Lname, filtered_students.Email
        FROM tbl_fees
        LEFT JOIN tbl_feepayment ON tbl_fees.F_ID = tbl_feepayment.F_ID
        LEFT JOIN (SELECT S_ID, Fname, Lname, Email 
                   FROM tbl_stud 
                   WHERE $search_by LIKE '%$search_value%' 
                   AND adm_status='1') AS filtered_students ON tbl_feepayment.S_ID = filtered_students.S_ID
    ";
    


       
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $first_row = $result->fetch_assoc();
    $student_name = $first_row['Fname'] . ' ' . $first_row['Lname'];
    $email=$first_row['Email'];

    // Display the heading
    echo "<h2>Fee Payment status of $student_name - $email</h2>";

    // Move the pointer back to the first row
    $result->data_seek(0);
            echo "<table border='1'>";
            echo "<tr><th>Fee Type</th><th>Status</th><th>Payment Date</th><th>Action</th></tr>";

            while ($row = $result->fetch_assoc()) {
                $status = is_null($row['FP_ID']) ? 'Unpaid' : 'Paid';
                $payment_date = is_null($row['FP_ID']) ? '-' : $row['Payment_date'];
                $fee_type_id = $row['F_ID']; // Included F_ID here

                echo "<tr>";
                
                echo "<td>" . $row['Type'] . "</td>";
                echo "<td>" . $status . "</td>";
                echo "<td>" . $payment_date . "</td>";

                if ($status == 'Unpaid') {
                    echo "<td><form action='' method='post'>
                            <input type='hidden' name='student_id' value='{$row['S_ID']}'>
                            <input type='hidden' name='fee_type' value='$fee_type_id'>
                            <button type='submit' name='mark_as_paid'>Mark as Paid</button>
                        </form></td>";
                } else {
                    echo "<td>-</td>";
                }
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "No records found.";
        }
    }
}

// Handling the view status

if (isset($_POST['view_status']) && !empty($_POST['fee_type']) && $_POST['payment_status'] == '') {
    $fee_type_id = $_POST['fee_type'];

    // Query to retrieve student information and payment status
    $sql = "SELECT tbl_stud.S_ID, tbl_stud.Fname, tbl_stud.Lname, tbl_stud.Email, 
                   tbl_feepayment.Payment_date, tbl_fees.Type,
                   (SELECT FP_ID FROM tbl_feepayment WHERE S_ID = tbl_stud.S_ID AND F_ID = '$fee_type_id') AS FP_ID 
            FROM tbl_stud
            LEFT JOIN tbl_feepayment 
            ON tbl_stud.S_ID = tbl_feepayment.S_ID AND tbl_feepayment.F_ID = '$fee_type_id'
            JOIN tbl_fees ON tbl_fees.F_ID = '$fee_type_id'
            WHERE tbl_stud.adm_status='1'";


    // Execute the query
    $result = $conn->query($sql);
    

    // Check if the query was successful
   
    // Display the results
    echo "<table border='1'>";
    echo "<tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Fee Type</th><th>Status</th><th>Payment Date</th><th>Action</th></tr>";

    while($row = $result->fetch_assoc()) {
        if (is_null($row['FP_ID'])) {
            $status = 'Unpaid';
            $payment_date = '-';
        } else {
            $status = 'Paid';
            $payment_date = $row['Payment_date'];
        }
        echo "<tr>";
        echo "<td>" . $row['Fname'] . "</td>";
        echo "<td>" . $row['Lname'] . "</td>";
        echo "<td>" . $row['Email'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $status. "</td>";
        echo "<td>" . $payment_date . "</td>";
        if ($status=='Unpaid')
        {
            echo "<td><form action='' method='post'>
                                <input type='hidden' name='student_id' value='{$row['S_ID']}'>
                                <input type='hidden' name='fee_type' value='$fee_type_id'>
                                <button type='submit' name='mark_as_paid'>Mark as Paid</button>
                            </form></td>";
        }
        else{
            echo"<td> - </td>";
        }
        echo "</tr>";
    }

    echo "</table>";
}

if(isset($_POST['view_status']) && empty($_POST['fee_type']) && !empty($_POST['payment_status'])) {
    $paymentStatus = $_POST['payment_status'];

    if ($paymentStatus == 'paid') {
        $sql = "SELECT DISTINCT tbl_stud.S_ID, tbl_stud.Fname, tbl_stud.Lname, tbl_stud.Email, tbl_fees.Type AS FeeType, tbl_feepayment.Payment_date
                FROM tbl_stud
                JOIN tbl_feepayment ON tbl_stud.S_ID = tbl_feepayment.S_ID
                JOIN tbl_fees ON tbl_feepayment.F_ID = tbl_fees.F_ID AND adm_status='1'";
    } elseif($paymentStatus == 'unpaid') {
        $sql = "SELECT tbl_stud.S_ID, tbl_stud.Fname, tbl_stud.Lname, tbl_stud.Email, tbl_fees.Type AS FeeType
                FROM tbl_stud
                JOIN tbl_fees
                LEFT JOIN tbl_feepayment ON tbl_stud.S_ID = tbl_feepayment.S_ID AND tbl_feepayment.F_ID = tbl_fees.F_ID
                WHERE tbl_feepayment.S_ID IS NULL AND adm_status='1'";
    }


    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $status_text = ($paymentStatus == 'paid') ? "Paid" : "Unpaid";
        echo "<h2>Fee Payment Status for {$status_text} Students</h2>";
        echo "<table border='1'>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Fee Type</th>";

        if ($paymentStatus == 'unpaid') {
            echo "<th>Action</th>";
        }
        else{
            echo "<th>Payment Date</th>";
        }

        echo "</tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['Fname']}</td>
                    <td>{$row['Lname']}</td>
                    <td>{$row['Email']}</td>
                    <td>{$row['FeeType']}</td>";

            if ($paymentStatus == 'unpaid') {
                echo "<td>
                        <form action='' method='post'>
                            <input type='hidden' name='student_id' value='{$row['S_ID']}'>
                            <input type='hidden' name='fee_type' value='{$row['FeeType']}'>
                            <button type='submit' name='mark_as_paid'>Mark as Paid</button>
                        </form>
                      </td>";
            } else{
                echo " <td>{$row['Payment_date']}</td>";
            }


            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No records found for {$status_text} students.";
    }
}

if (isset($_POST['view_status']) && !empty($_POST['fee_type']) && !empty($_POST['payment_status'])) {
    $fee_type_id = $_POST['fee_type'];
    $payment_status = $_POST['payment_status'];

    if ($payment_status == 'paid') {
        $sql = "SELECT tbl_stud.S_ID, tbl_stud.Fname, tbl_stud.Lname, tbl_stud.Email, tbl_fees.Type AS FeeType, tbl_feepayment.Payment_date
                FROM tbl_stud
                JOIN tbl_feepayment ON tbl_stud.S_ID = tbl_feepayment.S_ID
                JOIN tbl_fees ON tbl_feepayment.F_ID = tbl_fees.F_ID
                WHERE tbl_fees.F_ID = '$fee_type_id' AND adm_status='1'";
    } elseif ($payment_status == 'unpaid') {
        $sql = "SELECT tbl_stud.S_ID, tbl_stud.Fname, tbl_stud.Lname, tbl_stud.Email, tbl_fees.Type AS FeeType
                FROM tbl_stud
                CROSS JOIN tbl_fees
                LEFT JOIN tbl_feepayment ON tbl_stud.S_ID = tbl_feepayment.S_ID AND tbl_feepayment.F_ID = tbl_fees.F_ID
                WHERE tbl_fees.F_ID = '$fee_type_id' AND tbl_feepayment.S_ID IS NULL AND adm_status='1'";
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $status_text = ($payment_status == 'paid') ? "Paid" : "Unpaid";
        echo "<h2>Fee Payment Status for {$status_text} Students (Filtered by Fee Type)</h2>";
        echo "<table border='1'>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Fee Type</th>";

        if ($payment_status == 'unpaid') {
            echo "<th>Action</th>";
        }
        else{
            echo "<th>Payment Date</th>";
        }

        echo "</tr>";

        while ($row = $result->fetch_assoc()) {
            
            echo "<tr>
                    <td>{$row['Fname']}</td>
                    <td>{$row['Lname']}</td>
                    <td>{$row['Email']}</td>
                    <td>{$row['FeeType']}</td>";
                   

            if ($payment_status == 'unpaid') {
                echo "<td>
                        <form action='' method='post'>
                            <input type='hidden' name='student_id' value='{$row['S_ID']}'>
                            <input type='hidden' name='fee_type' value='$fee_type_id'>
                            <button type='submit' name='mark_as_paid'>Mark as Paid</button>
                        </form>
                      </td>";
            }
            else{
                echo " <td>{$row['Payment_date']}</td>";
            }

            echo "</tr>";
        }
        echo "</table>";
    } else {
        $status_text = ($payment_status == 'paid') ? "paid" : "unpaid";
        echo "No records found for {$status_text} students with the selected fee type.";
    }
}


if(isset($_POST['mark_as_paid']))
{
    $student_id = $_POST['student_id'];
    $fee_type = $_POST['fee_type'];

    // Retrieve the F_ID from the tbl_fees table based on the fee type
    $sql = "SELECT F_ID FROM tbl_fees WHERE F_ID = '$fee_type'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fee_id = $row['F_ID'];

        // Insert a new entry into the fee payment table
        $today = date('Y-m-d'); // Get today's date
        $sql = "INSERT INTO tbl_feepayment (S_ID, F_ID, Payment_date) VALUES ('$student_id', '$fee_id', '$today')";
        if ($conn->query($sql)) {
            $message = "Fee marked as paid successfully!";
        } else {
            $message = "Error marking fee as paid: " . $conn->error;
        }
    } else {
        $message = "Fee type not found.";
    }

    // Display the message
    echo "<p>$message</p>";
}

    ?>

</body>
</html>
