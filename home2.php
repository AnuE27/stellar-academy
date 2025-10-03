<?php
session_start();
include 'db.php';

// select the correct table and column names
$newStudentsResult = $conn->query("SELECT COUNT(*) as count FROM tbl_stud WHERE adm_status = '1'");
$teachersResult = $conn->query("SELECT COUNT(*) as count FROM tbl_staff WHERE Role = 'Teacher'");
// Initialize variables 
$succeeded_students = 0;
$new_students = 0;
$current_teachers = 0;
$classes_offered = 0;
// Check for successful queries and assign the results
if ($newStudentsResult) {
    $new_students = $newStudentsResult->fetch_assoc()['count'];
} else {
    echo "Query failed: " . $conn->error;
}

if ($teachersResult) {
    $current_teachers = $teachersResult->fetch_assoc()['count'];
} else {
    echo "Query failed: " . $conn->error;
}
$conn->close();
?>


<html>
<head>
    <title>Home Page</title>
    <link rel="stylesheet" href="homecss2.css">
</head>
<body>
    <header>
        <h2 class="logo">Stellar Tuition Academy</h2>
        <nav class="navigation">
            <a href="home2.php">Home</a>
            <a href="aboutus.html">About Us</a>
            <a href="http://localhost/mini/admission.php">Admission</a>
            <button class="btnLogin-popup" onclick="window.location.href='login.php';">Login</button>
        </nav>
    </header>

    <div class="container1">
        <div class="contains-1">
            <h1>Your Journey<br>Your Pace<br>Your Success</h1>
            <p>At Stellar Tuition Academy, we believe that learning should be an enjoyable and fulfilling journey.
             <br>Our dedicated team of educators is committed to creating an engaging and supportive environment where students can thrive at their own pace.  
             <br>With personalized attention and a focus on each student's growth, we're here to help you achieve your academic goals and excel beyond expectations. 
             <br>Join us at Stellar Tuition Academy and discover a place where education meets inspiration!</p>
        </div>
    </div>

    <div class="card-container">
        <div class="card">
            <div class="card-img">
                <img src="multicourse.jpg" alt="books">
            </div>
            <div class="card-content">
                <p class="text">We offer classes to students from class 8 to 12 in multiple subjects. These subjects include English,
                    Biology, Chemistry, Physics, and Maths. The full syllabus is taught to the students thoroughly,
                    giving special attention to any area students might find difficult. Students are taught to critically analyze and answer,
                    and to fully grasp the concept so that they can apply the knowledge in their daily life.
                    We also offer special exam preparation workshops to help students navigate the task of expressing their knowledge in the most
                    accurate way.</p>
                <span class="see-more-btn">See More</span>
            </div>
        </div>
        <div class="card">
            <div class="card-img">
                <img src="teacher.jpeg" alt="books">
            </div>
            <div class="card-content">
                <p class="text">Our team of highly qualified and experienced educators at Stellar Tuition Academy is dedicated to delivering exceptional teaching.
                     Each faculty member brings a wealth of knowledge and expertise to the classroom, ensuring that students receive top-notch instruction. 
                     Our teachers are not only experts in their subjects but are also skilled in employing innovative teaching methods that make learning engaging and effective. 
                     By fostering a positive learning environment and providing personalized guidance, our faculty helps students achieve their academic goals and develop a genuine passion for learning.</p>
                <span class="see-more-btn">See More</span>
            </div>
        </div>
        <div class="card">
            <div class="card-img">
                <img src="time.jpeg" alt="books">
            </div>
            <div class="card-content">
                <p class="text">At Stellar Tuition Academy, we understand that every student has unique scheduling needs. 
                    That’s why we offer flexible learning options to accommodate different timetables and preferences. 
                    Whether you prefer weekend classes, evening sessions, or intensive courses, we provide a variety of scheduling choices to fit your lifestyle. 
                    Our goal is to make high-quality education accessible and convenient, ensuring that all students can balance their academic pursuits with other commitments.</p>
                <span class="see-more-btn">See More</span>
            </div>
        </div>
    </div>
    <script src="homejs.js"></script>
    <section class="facts-section">
        <h2 class="section-title">A Few Facts About Our Academy</h2>
        <div class="facts-container">
            <div class="fact-box">
                <h3 class="fact-number"><?php echo $new_students; ?></h3>
                <p class="fact-description"> Students</p>
            </div>
            <div class="fact-box">
                <h3 class="fact-number"><?php echo $current_teachers; ?></h3>
                <p class="fact-description">Current Teachers</p>
            </div>
            <div class="fact-box">
                <h3 class="fact-number">25</h3>
                <p class="fact-description">Classes Offered</p>
            </div>
        </div>
    </section>
    <section class="contact-us" id="contact">
            <div class="col-lg-3">
            <center>
              <div class="right-info">
                <ul>
                  <li>
                    <h6>Phone Number</h6>
                    <span>010-020-0340</span>
                  </li>
                  <li>
                    <h6>Email Address</h6>
                    <span>info@stellartuition.acd</span>
                  </li>
                  <li>
                    <h6>Street Address</h6>
                    <span>Kuttikanam, PIN: 686512, Idukki, Kerala</span>
                  </li>
                  <li>
                    <h6>Website URL</h6>
                    <span>www.stellar_tuition.com</span>
                  </li>
                </ul>
              </div>
            </div>
        </center>
          </div>
        </div>
        <div class="footer">
          <p>Copyright © 2022 Stellar Tuition Academy., Ltd. All Rights Reserved. 
              <br>Design: <a href="https://templatemo.com" target="_parent" title="free css templates">TemplateMo</a></p>
        </div>
      </section>
    
</body>
</html>

