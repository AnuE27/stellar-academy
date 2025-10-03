<?php
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cls'])) 
{
    $class = $_POST['cls'];
    
    $sql = "SELECT SUB_ID, Name FROM tbl_subject WHERE Name LIKE '$class%' AND stat = '1'";
    $result = mysqli_query($conn, $sql);

    if ($result->num_rows > 0) 
    {
        while ($row = $result->fetch_assoc())
        {
        $subjectName = substr($row['Name'], strpos($row['Name'], '_') + 1);
        echo "<input type='checkbox' id='subjects[]' name='subjects[]' value='$subjectName'>$subjectName<br>";
        }
    } 
    else 
    {
        echo "No subjects available for selected class.";
    }
}
else
{
    echo "Please select a class.";
}
?>
