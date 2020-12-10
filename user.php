<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <link rel = "stylesheet" href = "css/style.css"/>
    <link rel = "stylesheet" href = "css/formstyle.css"/>
    <style>
        td{
            padding:16px;
        }

        .profile-card-2 {
            width: 250px;
            height: 370px;
            background-color: #FFF;
            box-shadow: 0px 0px 25px rgba(0, 0, 0, 0.1);
            background-position: center;
            overflow: hidden;
            position: relative;
            margin: 30px auto;
            cursor: pointer;
            border-radius: 10px;
        }

        .profile-card-2 img {
            transition: all linear 0.25s;
            width: 100%;
            height: 100%;
        }

        .profile-card-2 .profile-name {
            position: absolute;
            left: 30px;
            bottom: 70px;
            font-size: 30px;
            color: #FFF;
            text-shadow: 0px 0px 20px rgba(0, 0, 0, 0.5);
            font-weight: bold;
            transition: all linear 0.25s;
        }

        .profile-card-2 .profile-icons {
            position: absolute;
            bottom: 30px;
            right: 30px;
            color: #FFF;
            transition: all linear 0.25s;
        }

        .profile-card-2 .profile-username {
            position: absolute;
            bottom: 50px;
            left: 30px;
            color: #FFF;
            font-size: 13px;
            transition: all linear 0.25s;
        }

        .profile-card-2 .profile-icons .fa {
            margin: 5px;
        }

        .profile-card-2:hover img {
            filter: grayscale(100%);
        }

        .profile-card-2:hover .profile-name {
            bottom: 80px;
        }

        .profile-card-2:hover .profile-username {
            bottom: 60px;
        }

        .profile-card-2:hover .profile-icons {
            right: 40px;
        }

        hr{
            border: 2px solid blanchedalmond;
        }
    </style>
    
</head>

<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<body>
    <div class="center">
        <div class="page-header">
            <h2>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h2>
            <p>
                <br>
                <a href="logout.php" class="btn btn-danger">Log Out</a>
                <?php
                    if($_SESSION["type"]=='admin'){
                        echo '<br><br><a href="admin.php" class="btn btn-danger">Go!! Add a new Series/Season</a>';
                    }
                ?>
            </p>
            <div class="container center" style="width:300px">
                <form>
                    <label for="genre">Select the Genre</label>
                    <div class="form-group">
                        <select class="form-control" id="genre" name="genre" style="width=200px" onchange="showSeries(this.value)">
                            <option selected value="">Genre</option>
                            <option value="Action">Action</option>
                            <option value="Adventure">Adventure</option>
                            <option value="Comedy">Comedy</option>
                            <option value="Crime">Crime</option>
                            <option value="Fantasy">Fantasy</option>
                            <option value="Horror">Horror</option>
                            <option value="Mystery">Mystery</option>
                            <option value="Triller">Triller</option>
                        </select>
                    </div>
                </form>
            </div>
            <br>
            <div id="txtHint"></div>
        </div>
    </div>
        

    <?php
        // Include files
        include("connection.php");

        $sql = "SELECT * FROM $table_webseries";
        $out = mysqli_query($con, $sql);
        $j = 1;
        if(mysqli_num_rows($out) > 0){
            // Displaying data
            echo "<div class='container center'><h5>Browse</h5><hr><br><table><tr>";
            while($row = $out->fetch_assoc()){
                if($row['seasons']!=null){
                    echo '<td>
                            <div class="profile-card-2" onclick="play('.$row['id'].')">
                                <img src="'.$row['image'].'" class="img img-responsive">
                            </div>
                            <div class="font-weight-bold info">
                                Seasons: '.$row['seasons'].'
                                <br>
                                Rating: ';
                                for($i=0; $i<$row['rating']; $i++) {
                                    if($i==0){
                                        echo '🔴 ';
                                    }
                                    elseif ($i==1) {
                                        echo '🟠 ';
                                    }
                                    elseif ($i==2) {
                                        echo '🟡 ';
                                    }
                                    elseif ($i==3) {
                                        echo '🟢 ';
                                    }
                                    else{
                                        echo '🔵 ';
                                    }
                                }
                                echo '<br>';
                        echo '</div>
                        </td>';
                    if(($j)%4==0){
                        echo "</tr><tr>";
                    }
                    $j++;
                }
            }
            echo "</table></div>";
        }        

    ?>
    <script>
        function play(id){
            window.location.href = "play.php?id=" + id;
        }
        function showSeries(str) {
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
                        document.getElementById("txtHint").innerHTML =
                            this.responseText;
                        console.log(this.responseText);
                    }
                };
                xmlhttp.open("GET", "getseries.php?genre=" + str, true);
                xmlhttp.send();
            }
        }
    </script>
</body>
</html>