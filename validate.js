
        function validate()
        {
            var fName = document.getElementById("fname").value;
                var lName = document.getElementById("lname").value;
                var ph_no = document.getElementById("ph_no").value;
                var email = document.getElementById("email").value;
                var classes = document.getElementsByName("classes[]");
                if (fName === '' || lName === '' || ph_no==='' || email==='')
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
                if (!phpattern.test(ph_no)) 
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
                var clsCheck=false;
                for (var i = 0; i < classes.length; i++) 
                {
                   if (classes[i].checked) 
                   {
                       clsCheck = true;
                       break;
                   }
                }

                if (!clsCheck) 
                {
                    alert("Please select at least one class.");
                    return false;
                }
                return true;
        }

        
