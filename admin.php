<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
if($_SESSION["type"]=='user'){
    header("location: user.php");
    exit;
}
?>

<!DOCTYPE html>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/formstyle.css"/>
    <link rel="stylesheet" href="css/style.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        .alert-box{
            top:100px;
            width: 35%;
            position: relative;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="center">
        <div class="page-header">
            <h2>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h2>
            <p>
                <a href="logout.php" class="btn btn-danger">Log Out</a>
            </p>
            <form method="post"> 
                <input type="submit" class="btn btn-warning" name="addseries" value="Add New Webseries" />
                <input type="submit" class="btn btn-warning" name="addseason" value="Add New season" />
                <input type="submit" class="btn btn-warning" name="removeseries" value="Remove Webseries" />
            </form><br>
            <p>
                <a href="user.php" class="btn btn-danger">Go Watch a Webseries</a>
            </p>
        </div>
    </div>
        
<?php

include('connection.php');

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$series_name = $rating = $season_num = $episode_cnt = $time = '';

if(isset($_POST['addnewseries'])){
    $series_name = test_input($_POST['wsname']);
    $rating = test_input($_POST['rating']);

    $sql = "SELECT * FROM $table_webseries where name = '$series_name'";
    $out = mysqli_query($con,$sql);
    if(mysqli_num_rows($out) == 0){
        if(empty($_POST['genre'])){
            echo "Select genre";
        }
        else{
            $maxsize_video = 20971520; // 20MB
     
            $name_video = $_FILES['webvideo']['name'];
            $name_img = $_FILES['webimg']['name'];
            $target_dir_vid = "videos/";
            $target_dir_img = "images/";
            $temp_video = explode(".", $_FILES["webvideo"]["name"]);
            $newvideoname = round(microtime(true)) . '.' . end($temp_video);
            $target_videofile = $target_dir_vid . $newvideoname;
            $temp_img = explode(".",$_FILES["webimg"]["name"]);
            $newimgname = round(microtime(true)) . '.' . end($temp_img);
            $target_imgfile = $target_dir_img . $newimgname;
    
            // Select file type
            $videoFileType = strtolower(pathinfo($target_videofile,PATHINFO_EXTENSION));
            $imgFileType = strtolower(pathinfo($target_imgfile,PATHINFO_EXTENSION));
            // Valid file extensions
            $extensions_arr_video = array("mp4","avi","3gp","mov","mpeg");
            $extensions_arr_img = array("jpg","jpeg","png","gif","psd","raw");
            // Check extension
            if( in_array($videoFileType,$extensions_arr_video) && in_array($imgFileType, $extensions_arr_img)){
        
                // Check file size
                if(($_FILES['webvideo']['size'] >= $maxsize_video) || ($_FILES["webvideo"]["size"] == 0) || 
                ($_FILES['webimg']['size'] == 0)) {
                    echo '<div class="alert-box"><div class="alert alert-danger" role="alert"> File is too large greater than 20MB. </div></div>';
                }
                else{
                    // Upload
                    if(move_uploaded_file($_FILES['webvideo']['tmp_name'],$target_videofile)){
                        if(move_uploaded_file($_FILES['webimg']['tmp_name'], $target_imgfile)){
                            $sql = "INSERT INTO $table_webseries(name, rating, image, video) values ('$series_name', '$rating', 'images/$newimgname', 'videos/$newvideoname')";
                            if(mysqli_query($con, $sql) === TRUE){
                                $sql = "SELECT id FROM $table_webseries where name='$series_name'";
                                $out = mysqli_query($con, $sql);
                                $row = mysqli_fetch_assoc($out);
                                $id = $row['id'];
                                foreach($_POST['genre'] as $check) {
                                    $sql = "INSERT INTO $table_genre(id, genre) VALUES ('$id', '$check')";
                                    // mysqli_query($con, $sql);
                                    if (!mysqli_query($con, $sql)) {
                                        echo '<div class="alert-box"><div class="alert alert-danger" role="alert"> Error adding genre: ' . mysqli_error($con).' </div></div>';
                                    }
                                }
                                echo '<div class="alert-box"><div class="alert alert-success" role="alert"> Upload Successful </div></div>';
                            }
                            else{
                                $path1 = $_SERVER['DOCUMENT_ROOT'].'/dbms/videos/'. $newvideoname;
                                $path2 = $_SERVER['DOCUMENT_ROOT'].'/dbms/images/'. $newimgname;
                                unlink($path1);
                                unlink($path2);
                            }
                        }
                        else{
                            $path = $_SERVER['DOCUMENT_ROOT'].'/dbms/videos/'. $newvideoname;
                            unlink($path);
                        }         
                    }
                }
            }
            else{
                echo '<div class="alert-box"><div class="alert alert-danger" role="alert"> Invalid file extention </div></div>';
            }
        }
    }
    else{
        echo '<div class="alert-box"><div class="alert alert-danger" role="alert"> This web series already exists!! Add new seasons. </div></div>';
    }  
}

if(isset($_POST['addnewseason'])){
    $series_name = test_input($_POST['wsname']);
    $season_num = test_input($_POST['season_num']);
    $episode_cnt = test_input($_POST['episode_cnt']);
    $time = test_input($_POST['time']);

    $sql = "SELECT * FROM $table_webseries where name = '$series_name'";
    $out = mysqli_query($con,$sql);
    if(mysqli_num_rows($out) == 0){
        echo '<div class="alert-box"><div class="alert alert-danger" role="alert"> The series with the name '.$series_name.' doesn\'t exist. </div></div>';
    }
    else{
        $row = mysqli_fetch_assoc($out);            
        $id = $row['id'];
        if($row['seasons']==null && $season_num==1){
            $sql = "INSERT INTO $table_seasons(id, season_num, episode_cnt, time_ep) VALUES ('$id', '$season_num', '$episode_cnt', '$time')";
            if(mysqli_query($con, $sql)){
                $sql = "UPDATE $table_webseries SET seasons=1 WHERE id=$id";
                if(!mysqli_query($con, $sql)){
                    echo mysqli_error($con);
                }
                echo '<div class="alert-box"><div class="alert alert-success" role="alert"> Season added successfully! </div></div>';
            }
            else{
                echo '<div class="alert-box"><div class="alert alert-danger" role="alert"> Error adding season: '.mysqli_error($con).' </div></div>';
            }
        }
        elseif ($row['seasons'] + 1 == $season_num) {
            $sql = "INSERT INTO $table_seasons(id, season_num, episode_cnt, time_ep) VALUES ('$id', '$season_num', '$episode_cnt', '$time')";
            if(mysqli_query($con, $sql)){
                $sql = "UPDATE $table_webseries SET seasons=". (int)($row['seasons']+1)." WHERE id=$id";
                if(!mysqli_query($con, $sql)){
                    echo mysqli_error($con);
                }
                echo '<div class="alert-box"><div class="alert alert-success" role="alert"> Season added successfully! </div></div>';
            }
            else{
                echo '<div class="alert-box"><div class="alert alert-danger" role="alert"> Error adding season: '.mysqli_error($con).' </div></div>';
            }
        }
        else{
            echo '<div class="alert-box"><div class="alert alert-danger" role="alert"> Season number should be '.(int)($row['seasons']+1).' </div></div>';
        }
    }

}

if(isset($_POST['deleteseries'])){
    $series_name = test_input($_POST['wsname']);
    $sql = "DELETE FROM $table_webseries WHERE name='".$series_name."'";
    if(mysqli_query($con, $sql)){
        echo '<div class="alert-box"><div class="alert alert-success" role="alert"> Series '.$series_name.' Deleted successfully! </div></div>';
    }
    else{
        echo '<div class="alert-box"><div class="alert alert-danger" role="alert"> Error deleting series: '.mysqli_error($con).' </div></div>';
    }
}

if(isset($_POST['addseries'])){
    echo '<div class="form-1">
    <h1 class="center">Add New Webseries</h1>
    <form name="series-form" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <input type="text" class="form-control" name="wsname" id="wsname" placeholder="Name" required>
        </div>
        <div class="form-group">
            <select class="form-control" id="rating" name="rating" required>
                <option selected value="" disabled>Rating</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <label>Genre</label></br>
        <div class="form-check form-group" required>
            <input class="form-check-input" type="checkbox" name="genre[]" value="Action" id="defaultCheck1">
            <label class="form-check-label" for="defaultCheck1">Action</label></br>
            <input class="form-check-input" type="checkbox" name="genre[]" value="Adventure" id="defaultCheck1">
            <label class="form-check-label" for="defaultCheck1">Adventure</label></br>
            <input class="form-check-input" type="checkbox" name="genre[]" value="Comedy" id="defaultCheck1">
            <label class="form-check-label" for="defaultCheck1">Comedy</label></br>
            <input class="form-check-input" type="checkbox" name="genre[]" value="Crime" id="defaultCheck1">
            <label class="form-check-label" for="defaultCheck1">Crime</label></br>
            <input class="form-check-input" type="checkbox" name="genre[]" value="Fantasy" id="defaultCheck1">
            <label class="form-check-label" for="defaultCheck1">Fantasy</label></br>
            <input class="form-check-input" type="checkbox" name="genre[]" value="Horror" id="defaultCheck1">
            <label class="form-check-label" for="defaultCheck1">Horror</label></br>
            <input class="form-check-input" type="checkbox" name="genre[]" value="Mystery" id="defaultCheck1">
            <label class="form-check-label" for="defaultCheck1">Mystery</label></br>
            <input class="form-check-input" type="checkbox" name="genre[]" value="Triller" id="defaultCheck1">
            <label class="form-check-label" for="defaultCheck1">Triller</label></br>
        </div> 
        <div class="form-group">
            <div class="custom-file">
                <input type="file" class="custom-file-input file1" id="webimg" name="webimg" required>
                <label class="custom-file-label" for="webimg">Choose image for Webseries</label>
            </div>
        </div>
        <div class="form-group">
            <div class="custom-file">
                <input type="file" class="custom-file-input file2" id="webvideo" name="webvideo" required>
                <label class="custom-file-label" for="webvideo">Choose a trailer video for Webseries</label>
            </div>
        </div>
        <div class="form-group">
            <input type="submit" name="addnewseries" value="Add Webseries" class="btn btn-block create-account">
        </div>
    </form>
    </div>';
}

if(isset($_POST['addseason'])){
    echo '<div class="form-1">
    <h1 class="center">Add New Season</h1>
    <form name="season-form" method="post">
        <div class="form-group">
            <input type="text" class="form-control" name="wsname" id="wsname" placeholder="Name" required>
        </div>
        <div class="form-group">
            <input type="number" class="form-control" name="season_num" id="season_num" min="1" placeholder="Season number" required>
        </div>
        <div class="form-group">
            <input type="number" class="form-control" name="episode_cnt" id="episode_cnt" min="1" placeholder="Number of Episodes" required>
        </div>
        <div class="form-group">
            <input type="time" class="form-control" name="time" id="time" placeholder="Approx time of each episode" required>
        </div>
        <div class="form-group">
            <input type="submit" name="addnewseason" value="Add New Season" class="btn btn-block create-account">
        </div>
    </form>
    </div>';
}

if(isset($_POST['removeseries'])){
    echo '<div class="form-1">
    <h1 class="center">Delete Webseries</h1>
    <form name="series-remove-form" id="deleteform" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <select class="form-control" id="wsname" name="wsname" required>
                <option selected value="" disabled>None</option>';
    $sql = "SELECT name FROM $table_webseries";
    $out = mysqli_query($con, $sql);
    if(mysqli_num_rows($out) > 0){
        while($row = $out->fetch_assoc()){
            echo '<option value="'.$row['name'].'">'.$row['name'].'</option>';
        }
    }
    echo '</select>
        </div>
        <div class="form-group">
            <input type="submit" name="deleteseries" value="Delete Webseries" class="btn btn-block create-account">
        </div>
    </form>
    </div>';
}
?> 

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
    <script>        
        document.querySelector('.file1').addEventListener('change',function(e){
            var fileName = document.getElementById("webimg").files[0].name;
            var nextSibling = e.target.nextElementSibling
            nextSibling.innerText = fileName
        })

        document.querySelector('.file2').addEventListener('change',function(e){
            var fileName = document.getElementById("webvideo").files[0].name;
            var nextSibling = e.target.nextElementSibling
            nextSibling.innerText = fileName
        })

        var el = document.getElementById('deleteform');

        el.addEventListener('submit', function(){
            return confirm('Are you sure you want to Delete the webseries?');
        }, false);


        function logOut() {
            // logout.php file removes the stored cookie.
            window.location.href = "logout.php";
        }


    </script>
</body>
</html>
