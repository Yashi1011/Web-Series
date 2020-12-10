<?php

$username =$_GET["username"];

include('connection.php');

$sql="SELECT * FROM $table_users WHERE username = '".$username."'";
$out = mysqli_query($con,$sql);
if(mysqli_num_rows($out) != 0){
    echo "<small>&ensp;Username taken</small>";
}else{
    echo "";
}

mysqli_close($con);
?>