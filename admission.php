<html>
<head>
    <title>Admission Form</title>
    <link rel="stylesheet" href="add1.css">
</head>
<body>
<header>
        <h2 class="logo">Stellar Tuition Academy</h2>
        <nav class="navigation">
            <a href="home2.php">Home</a>
            <a href="aboutus.html">About Us</a>
            <a href="http://localhost/mini%20project/admission.php">Admission</a>
           
            <button class="btnLogin-popup" onclick="window.location.href='login.php';">Login</button>
        </nav>
    </header>
<div class="wrapper"> 
    <div class="form-box">
        <h2>Admission Form</h2>
        <form action='' method='post' onsubmit="return validateform()">
            <div class="input-box">
                <input type="text" id="fname" name="fname" placeholder="First Name" required>
            </div>
            <div class="input-box">
                <input type="text" id="lname" name="lname" placeholder="Last Name of the applicant" required>
            </div>
            <div class="input-box">
                <input type="text" id="contactno" name="contactno" placeholder="Contact Number" required>
            </div>
            <div class="input-box">
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-box">
                <input type="text" id="guardphone" name="guardphone" placeholder="Guardian's Phone Number" required>
            </div>
            <div class="input-box">
                <input type="date" id="dob" name="dob" required>
                <label for="dob" class="date-label">Date of Birth</label>
            </div>
            <div class="input-box">
                <select id="cls" name="cls" onchange="fetchSubjects()" required>
                    <option value="" disabled selected>Select Class</option>
                    <option>8</option>
                    <option>9</option>
                    <option>10</option>
                    <option>11</option>
                    <option>12</option>
                </select>
                
            </div>
            <div class="input-box" id="subjectCheckboxes">
                <!-- Subjects will be loaded here -->
            </div>
            <div class="input-box">
                <input type="password" id="pass" name="pass" placeholder="Password" required>
            </div>
            <div class="input-box">
                <input type="password" id="confpass" name="confpass" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="btn">Apply</button>
        </form>
    </div>
</div>


        <script> 
            function validateform() 
            {
                var fName = document.getElementById("fname").value;
                var lName = document.getElementById("lname").value;
                var contactNo = document.getElementById("contactno").value;
                var email = document.getElementById("email").value;
                var guardPhone = document.getElementById("guardphone").value;
                var dob = document.getElementById("dob").value;
                var cls= document.getElementById("cls").value;
                var subjects = document.getElementsByName("subjects[]");
                var password=document.getElementById("pass").value;
                var confirmpass=document.getElementById("confpass").value;
                
                if (fName === '' || lName === '' || contactNo==='' || email==='' || guardPhone === '' || dob === '' || cls==='' || password==='' || confirmpass==='')
                {
                   alert("Please fill in all required fields.");
                   return false;
                }
                var namePattern = /^[A-Za-z]+$/;
                if (!namePattern.test(fName) || !namePattern.test(lName))
                {
                   alert("First name and last name should only contain letters.");
                   return false;
                }

                var phpattern=/^\d{10}$/
                if (!phpattern.test(contactNo) || !phpattern.test(guardPhone)) 
                {
                   alert("Please enter a valid 10-digit phone number.");
                   return false;
                }
        
                var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) 
                {
                    alert("Please enter a valid email address.");
                    return false;
                }
                var maxDate = new Date("2013-12-31"); // Maximum allowed date
                var dobDate = new Date(dob);
                if (dobDate > maxDate)
                {
                    alert("Date of Birth cannot be after 2013.");
                    return false;
                }

                var subCheck=false;
                for (var i = 0; i < subjects.length; i++) 
                {
                   if (subjects[i].checked) 
                   {
                       subCheck = true;
                       break;
                   }
                }

                if (!subCheck) 
                {
                    alert("Please select at least one subject.");
                    return false;
                }

                var hasUpperCase = /[A-Z]/.test(password);
                var hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
                var hasNumber = /\d/.test(password);
                var hasMinimumLength = password.length >= 8;
                if (hasUpperCase && hasSpecialChar && hasNumber && hasMinimumLength) 
                {
                   if (password !== confirmpass) 
                   {
                      alert("Passwords do not match");
                      return false;
                   }
                   else
                   {
                      return true; 
                   }
                }
                else
                {

                   alert("Invalid Password. Password must contain a capital letter, special character, a number, and be at least 8 characters long.");
                   return false;
                }

                return true;
            }
            function fetchSubjects() 
            {
                var cls = document.getElementById("cls").value;
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "subavail.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() 
                {
                    if (xhr.readyState == 4 && xhr.status == 200) 
                    {
                        document.getElementById("subjectCheckboxes").innerHTML = xhr.responseText;
                    }
                };
                xhr.send("cls=" + cls);
            }

        </script>
    </body>
    <?php

        include 'db.php'; 
       
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
           $fname = $_POST['fname'];
           $lname = $_POST['lname'];
           $contactno = $_POST['contactno'];
           $email = $_POST['email'];
           $guardphone = $_POST['guardphone'];
           $dob = $_POST['dob'];
           $class = $_POST['cls'];
           $subjects = (array) $_POST['subjects'];
           $password = $_POST['pass']; 
           $hashed_password = password_hash($password, PASSWORD_DEFAULT);
          
                $sql="INSERT INTO tbl_stud (Fname,Lname,Ph_no,Email,Password,Guar_ph,DOB,Adm_status) VALUES ('$fname','$lname','$contactno','$email','$hashed_password','$guardphone','$dob','0')";
                if (mysqli_query($conn, $sql))
                {
                    $student_id = mysqli_insert_id($conn);
                    foreach ($subjects as $subject) 
                    {
                        $subname = $class . "_" . $subject;
                        $sql1 = "SELECT SUB_ID FROM tbl_subject WHERE Name = '$subname'";
                        $result = mysqli_query($conn, $sql1);
                
                        if ($result->num_rows > 0) 
                        {
                            $row = $result->fetch_assoc();
                            $subject_id = $row['SUB_ID'];
                
                            $sql2 = "INSERT INTO tbl_enrollment (S_ID, SUB_ID) VALUES ('$student_id', '$subject_id')";
                            mysqli_query($conn, $sql2);
                        }
                    }
    
                    echo "Application submitted successfully";
                    
                }
                else
                {
                    echo "Application not submitted";
                }
              }
         
        $conn->close();
    ?>


</html>