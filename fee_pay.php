<?php
// Ensure session is started
include 'db.php'; // Include your database connection file

$email = $_SESSION['login']; 
$studentQuery = "SELECT S_ID FROM tbl_stud WHERE Email = '$email'";
$studentResult = $conn->query($studentQuery);
$student = $studentResult->fetch_assoc();
$studentId = $student['S_ID'];

// Check for unpaid fees due within the next 20 days
$today = new DateTime();
$alertMessages = [];
$feeQuery = "
    SELECT f.Type, f.Due_date 
    FROM tbl_fees f 
    LEFT JOIN tbl_feepayment fp ON f.F_ID = fp.F_ID AND fp.S_ID = '$studentId' 
    WHERE fp.F_ID IS NULL";

$feeResult = $conn->query($feeQuery);

while ($row = $feeResult->fetch_assoc()) {
    $dueDate = new DateTime($row['Due_date']);
    $diff = $today->diff($dueDate)->days;
    
    if ($diff > 0 && $diff <= 20) {
        $alertMessages[] = "{$row['Type']} fees is due in {$diff} days.";
    }
}
if (!isset($_SESSION['page_loaded'])) {
    $_SESSION['page_loaded'] = true;
if (!empty($alertMessages)) {
    $alertText = implode("\\n", $alertMessages); // Join messages with newlines
    echo "<script>alert('You have unpaid fees due:\\n$alertText');</script>";
}
}
?>
<html lang="en">
<head>
    <title>Fee Payment</title>
    <link rel="stylesheet" href="fee_paycss.css"> <!-- Link to your CSS file -->
    
</head>
<body>
    <h1>Fee Payment</h1>
    
    <!-- Buttons to view payment history -->
    <form method="POST">
        <button type="submit" name="action" value="viewHistory">View Payment History</button>
        <button type="submit" name="action" value="payfees">Pay Fees</button>
    </form>

    <?php
    
    
    // Get the number of subjects enrolled by the student
    $subjectCountQuery = "SELECT COUNT(*) as subject_count FROM tbl_enrollment WHERE S_ID = '$studentId'";
    $subjectCountResult = $conn->query($subjectCountQuery);
    $subjectCount = $subjectCountResult->fetch_assoc()['subject_count'];

    $feeTypeMap = [
        'S1' => 'Semester 1',
        'S2' => 'Semester 2',
        'S3' => 'Semester 3'
    ];
    // Check if the action is to view history
    if (isset($_POST['action']) && $_POST['action'] == 'viewHistory') {
        // Fetch the student's ID based on email
       

        // Fetch payment history from the database
        $paymentQuery = "
            SELECT f.Type, f.Amount, fp.Payment_date
            FROM tbl_feepayment fp 
            JOIN tbl_fees f ON fp.F_ID = f.F_ID 
            WHERE fp.S_ID = '$studentId' 
            ORDER BY fp.Payment_date DESC";
        
        $paymentResult = $conn->query($paymentQuery);

        if ($paymentResult && $paymentResult->num_rows > 0) {
            echo "<h2>Payment History</h2>";
            echo "<table border='1'>
                    <thead>
                        <tr>
                            <th>Fee Type</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>";

            // Define fee type mapping
            

            while ($row = $paymentResult->fetch_assoc()) {
                $amount = $row['Amount'];

                // Calculate the total amount for S1, S2, S3 based on the subject count
                if (in_array($row['Type'], ['S1', 'S2', 'S3'])) {
                    $amount *= $subjectCount; // Multiply by the number of subjects
                }

                // Use the mapping for the fee type
                $feeTypeDisplay = $feeTypeMap[$row['Type']] ?? $row['Type'];

                echo "<tr>
                        <td>" . $feeTypeDisplay . "</td>
                        <td>" . $amount . "</td>
                        <td>" . $row['Payment_date'] . "</td>
                      </tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p>No payment history found.</p>";
        }
    }
    
        
    if (isset($_POST['action']) && $_POST['action'] == 'payfees') {
        include 'db.php';
        $subjectCountQuery = "SELECT COUNT(*) as subject_count FROM tbl_enrollment WHERE S_ID = '$studentId'";
        $subjectCountResult = $conn->query($subjectCountQuery);
        $subjectCount = $subjectCountResult->fetch_assoc()['subject_count'];
    
        $feeTypeMap = [
            'S1' => 'Semester 1',
            'S2' => 'Semester 2',
            'S3' => 'Semester 3'
        ];
    
        // Fetch unpaid fee types
        $feeQuery = "
            SELECT f.Type, f.Amount, f.Due_date 
            FROM tbl_fees f 
            LEFT JOIN tbl_feepayment fp ON f.F_ID = fp.F_ID AND fp.S_ID = '$studentId' 
            WHERE fp.F_ID IS NULL 
            ORDER BY Due_date";
        
        $feeResult = $conn->query($feeQuery);
    
        $hasOverdueFees = false;
    
        if ($feeResult && $feeResult->num_rows > 0) {
            // Check if any unpaid fees are past due
            while ($row = $feeResult->fetch_assoc()) {
                if (strtotime($row['Due_date']) < time()) {
                    $hasOverdueFees = true;
                    break;
                }
            }
    
            // Reset result pointer to fetch data again
            $feeResult->data_seek(0);
    
            // Display overdue message if there are past due fees
            if ($hasOverdueFees) {
                echo "<p style='color: red;'>You have unpaid fees that are past their due date. Please pay them as soon as possible.</p>";
            }
    
            echo "<h2>Unpaid Fees</h2>";
            echo "<table border='1'>
                    <thead>
                        <tr>
                            <th>Fee Type</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>";
    
            $totalAmount = 0;
    
            while ($row = $feeResult->fetch_assoc()) {
                $amount = $row['Amount'];
                if (in_array($row['Type'], ['S1', 'S2', 'S3'])) {
                    $amount *= $subjectCount;
                }
    
                $totalAmount += $amount;
                $feeTypeDisplay = $feeTypeMap[$row['Type']] ?? $row['Type'];
    
                echo "<tr>
                        <td>" . $feeTypeDisplay . "</td>
                        <td>" . $amount . "</td>
                        <td>" . $row['Due_date'] . "</td>
                        <td>
                            <form method='POST'>
                                <input type='hidden' name='feeType' value='" . $row['Type'] . "'>
                                <input type='hidden' name='amount' value='" . $amount . "'>
                                <button type='submit' name='pay'>Pay</button>
                            </form>
                        </td>
                      </tr>";
            }
    
            echo "</tbody></table>";
    
            // Pay All button
            echo "<h3>Total Amount: " . $totalAmount . "</h3>";
            echo "<form method='POST'>
                    <input type='hidden' name='totalAmount' value='" . $totalAmount . "'>
                    <button type='submit' name='payAll'>Pay All</button>
                  </form>";
        } else {
            echo "<p>No unpaid fees found.</p>";
        }
    }
    
    
    if (isset($_POST['pay']) || isset($_POST['payAll'])) {
        // Display the payment form for individual or total fee payment
        if (isset($_POST['pay'])) {
            $feeType = $_POST['feeType'];
            $amount = $_POST['amount'];
        } else {
            $totalAmount = $_POST['totalAmount'];
        }
        
        // Render the payment form
        echo"<script>
function validateFees() {
    const cardNumber = document.getElementById('cardNumber').value;
    const expiryDate = document.getElementById('expiryDate').value;
    const cvv = document.getElementById('cvv').value;
    const cardName = document.getElementById('cardName').value;

    // Simple validation checks
    if (cardNumber.length !== 16 || isNaN(cardNumber)) {
        alert('Card number must be 16 digits.');
        return false;
    }
    
    const expiryPattern = /^(0[1-9]|1[0-2])\/\d{2}$/;
    if (!expiryPattern.test(expiryDate)) {
        alert('Expiry date must be in the format MM/YY.');
        return false;
    }
    
    if (cvv.length !== 3 || isNaN(cvv)) {
        alert('CVV must be 3 digits.');
        return false;
    }

    if (!/^[a-zA-Z\s]+$/.test(cardName)) {
                alert('Name on card must contain only letters.');
                return false;
            }
    // If all validations pass
    return true;
}
</script>";

echo"<form id='card' method='POST' >
    <label>Card Number:</label>
    <input type='text' id='cardNumber' name='cardNumber' required>

    <label>Expiry Date (MM/YY):</label>
    <input type='text' id='expiryDate' name='expiryDate' required>

    <label>CVV:</label>
    <input type='text' id='cvv' name='cvv' required>

    <label>Name on Card:</label>
    <input type='text' id='cardName' name='cardName' required>

    <input type='hidden' name='feeType' value='" . ($feeType ?? '') . "'>
                <input type='hidden' name='amount' value='" . ($amount ?? $totalAmount) . "'>

    <button type='submit' name='verifyPayment' onclick='return validateFees();'>Verify</button>
    
</form>
";


    }
    
    // Handle payment verification after form submission
    if (isset($_POST['verifyPayment'])) {
        // Simulate a delay for payment verification (optional)
        sleep(2); // Delay for 2 seconds
    
        // Proceed to payment confirmation
        echo "<h2>Payment Confirmation</h2>";
    
        if (isset($_POST['feeType'])) {
            $amount = $_POST['amount'];
            $feeType = $_POST['feeType'];
            echo "<p>Amount to be paid: $amount</p>";
            echo "<form method='POST'>
                    <input type='hidden' name='feeType' value='$feeType'>
                    <input type='hidden' name='amount' value='$amount'>
                    <button type='submit' name='confirmPayment'>Pay</button>
                    <button type='submit' name='cancel'>Cancel</button>
                  </form>";
        } else {
            $totalAmount = $_POST['amount'];  // Handling total amount for multiple fees
            echo "<p>Total Amount to be paid: $totalAmount</p>";
            echo "<form method='POST'>
                    <input type='hidden' name='totalAmount' value='$totalAmount'>
                    <button type='submit' name='confirmPaymentAll'>Pay</button>
                    <button type='submit' name='cancel'>Cancel</button>
                  </form>";
        }
    }
    
    // Handle individual payment confirmation
    if (isset($_POST['confirmPayment'])) {
        $feeType = $_POST['feeType'];
        $amountPaid = $_POST['amount'];
        $feeQuery = "SELECT F_ID FROM tbl_fees WHERE Type='$feeType'";
        $feeResult = $conn->query($feeQuery);
        
        while ($row = $feeResult->fetch_assoc()) {
            $feeid = $row['F_ID'];
        }
    
        $insertPayment = "INSERT INTO tbl_feepayment (S_ID, F_ID, Payment_date) 
                          VALUES ('$studentId', '$feeid', NOW())";
        $conn->query($insertPayment);
    
        echo "<p>Payment successful for amount: $amountPaid</p>";
    }
    
    // Handle total fee payment confirmation
    if (isset($_POST['confirmPaymentAll'])) {
        $totalAmount = $_POST['totalAmount'];
    
        // Fetch all unpaid fees
        $feeQuery = "
            SELECT f.F_ID 
            FROM tbl_fees f 
            LEFT JOIN tbl_feepayment fp ON f.F_ID = fp.F_ID AND fp.S_ID = '$studentId' 
            WHERE fp.F_ID IS NULL";
        
        $feeResult = $conn->query($feeQuery);
        
        while ($row = $feeResult->fetch_assoc()) {
            $feeid = $row['F_ID'];
            $insertPayment = "INSERT INTO tbl_feepayment (S_ID, F_ID, Payment_date) 
                              VALUES ('$studentId', '$feeid', NOW())";
            $conn->query($insertPayment);
        }
    
        echo "<p>Payment successful for total amount: $totalAmount</p>";
    }
    if (isset($_POST['cancel'])) {
        displayUnpaidFees($studentId);
    }
    ?>
</body>
</html>
