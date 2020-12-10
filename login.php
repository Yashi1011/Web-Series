<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/formstyle.css">
</head>

<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if($_SESSION["type"] == 'user')
        header("location: user.php");
    else
        header("location: admin.php");
    exit;
}
 
// Database connection
include('connection.php');

// define variables and set to empty values
$username = $password = $type = $websiteErr = $message = "";

// If there is a post request then the following commands run.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Storing the form inputs to variables.
    $username = test_input($_POST["username"]);
    $password = test_input($_POST["password"]);
    
    // Getting data from database.
    $sql = "SELECT username, password, type FROM $table_users WHERE username='$username'";
    $out = mysqli_query($con, $sql);
    // If there are no records then user is not reistered.
    if(mysqli_num_rows($out) == 0){
        $websiteErr = "User Didn't Register";
        echo "<div class='alert alert-danger center' role='alert'>$username Didn't Register <a href='register.php' class='alert-link'>Click here to Register</a></div>";
    }
    // Else the data is inserted in database.
    else{
        $row = mysqli_fetch_assoc($out);
        $pass = decrypt($row["password"]);
        if($pass == $password){
            // Storing the session details
            session_start();
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $username;
            $_SESSION["type"] = $row["type"];
            if($row["type"] == "user"){
                // Redirect to user page
                header("location: user.php");
            }else if($row["type"] == "admin") {
                // Redirect to admin page
                header("location: admin.php");
            }
            else{
                echo "<script>alert('There is some problem please contact admin')</script>";
            }
        }
        else{
            $websiteErr = "Password is Incorrect";
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
        
        <form name="login-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
            <p class="help-block error"><?php echo $websiteErr; ?></p>
            <div class="form-group">
                <input type="text" class="form-control item" name="username" id="username" placeholder="Username" onchange="usernameVerification(this.value)" value="<?php echo $username; ?>" required>
            </div>
            <span class="help-block" id="txtHint"></span>
            <div class="form-group">
                <input type="password" class="form-control item" name="password" id="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <input type="submit" name="login" value="Login" class="btn btn-block create-account">
            </div>
        </form>
        <div class="others">
            <h5><p>Don't have an account? <a href="register.php">Register here</a>.</p></h5>
            <h5><a href="forgotPassword.php">Forgot Password?</a></h5>
        </div>
    </div>
    
</body>

</html>