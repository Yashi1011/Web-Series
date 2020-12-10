<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/formstyle.css">
</head>

<?php
// Database connection
include('connection.php'); 

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if($_SESSION["type"] == 'user')
        header("location: user.php");
    else
        header("location: admin.php");
    exit;
}

// define variables and set to empty values
$username = $password = $cnfpassword = $phone = $websiteErr = $message = "";

// If there is a post request then the following commands run.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Storing the form inputs to variables.
    $username = test_input($_POST["username"]);
    $pass = test_input($_POST["password"]);
    $password = encrypt(test_input($_POST["password"]));
    $cnfpassword = test_input($_POST["cnfpassword"]);
    $phone = test_input($_POST["phone-number"]);
    
    // Checking if there are records with that username.
    $sql = "SELECT username, phone FROM $table_users WHERE username='$username'";
    $out = mysqli_query($con, $sql);
    // If there is atleast one record then username is taken.
    if(mysqli_num_rows($out) == 0){
        $websiteErr = "User Doesn't Exist";
    }
    // Else the data is inserted in database.
    else{
        if($pass == $cnfpassword){
            $row = mysqli_fetch_assoc($out);
            if($row['phone'] == $phone){
                $sql = "UPDATE $table_users SET password='$password' where username='$username'";
                if(mysqli_query($con, $sql) === TRUE){
                    echo "<div class='alert alert-success center' role='alert'>$username password changes successfully. <a href='login.php' class='alert-link'>Click here to login</a></div>";
                }else {
                    echo "Error creating database: " . $con->error;
                }
            }
            else{
                $websiteErr = "Phone number is not same as registered";
            }            
        }
        else{
            $websiteErr = "Password and Confirm password are not same";
        }
        
    }
}

// Funtion to trim and process the input fields.
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<body>
    <div class="form-form">
        
        <form name="forgetPassword-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
                
            <p class="help-block error"><?php echo $websiteErr; ?></p>
            <div class="form-group">
                <input type="text" class="form-control item" name="username" id="username" placeholder="Username" onchange="usernameVerification(this.value)" value="<?php echo $username; ?>" required>
            </div>
            <span class="help-block" id="txtHint"></span>
            <div class="form-group">
                <input type="password" class="form-control item" name="password" id="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control item" name="cnfpassword" id="cnfpassword" placeholder="Confirm Password" onchange="cnfPassVerification(this.value)" required>
            </div>
            <span class="help-block" id="passHint"></span>
            <div class="form-group">
                <input type="text" class="form-control item" name="phone-number" id="phone-number" placeholder="Phone Number" value="<?php echo $phone; ?>" required>
            </div>
            <div class="form-group">
                <input type="submit" name="reset" value="Reset Password" class="btn btn-block create-account">
            </div>
        </form>
        <div class="others">
            <h5><p>Already have an account? <a href="login.php">Login here</a>.</p></h5>
            <h5><p>Don't have an account? <a href="register.php">Register here</a>.</p></h5>
        </div>
    </div>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
    <!-- <script src="assets/js/script.js"></script> -->
    <script>
        $(document).ready(function () {
            // writing the format phone number
            $('#phone-number').mask('0000-000-000');
        })

        function cnfPassVerification(str) {
            if (str == ""){
                document.getElementById("passHint").innerHTML = "";
                return;
            } else{
                var pass = document.getElementById("password").value;
                document.getElementById("passHint").style.color = "red";
                if(pass != str){
                    document.getElementById("passHint").innerHTML = "<small>It is not matching the Password</small>";
                }
                else{
                    document.getElementById("passHint").innerHTML = "";
                }
                return;
            }
        }

        function usernameVerification(str) {
            if (str == "") {
                document.getElementById("txtHint").innerHTML = "";
                return;
            } else {
                if (window.XMLHttpRequest) {
                    // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else {
                    // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("txtHint").style.color = "red";
                        document.getElementById("txtHint").innerHTML =
                            this.responseText;
                    }
                };
                xmlhttp.open("GET", "usernotexist.php?username=" + str, true);
                xmlhttp.send();
            }
        }
    </script>
</body>

</html>