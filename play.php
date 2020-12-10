<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <link rel = "stylesheet" href = "css/style.css"/>

</head>
<body>
<?php

include("connection.php");
$id = $_GET['id'];
$sql = "SELECT * FROM $table_webseries WHERE id=$id";
$out = mysqli_query($con, $sql);
if(mysqli_num_rows($out) > 0){
    $row = mysqli_fetch_assoc($out);
    echo '<video width="100%" height="500px" controls>
    <source src="'.$row['video'].'" type="video/mp4">
    Your browser does not support the video tag.
    </video>';
    echo '<div class="container center">';
    echo '<div class="font-weight-bold info"><h1>'.$row['name'].'</h1></div><br>';
    $inner_sql = "SELECT * FROM $table_seasons WHERE ".$row['id']." = id";
    $inner_out = mysqli_query($con, $inner_sql);
    echo '<div class="font-weight-bold info"><table class="table">
        <thead>
        <tr>
            <th scope="col">Season number</th>
            <th scope="col">Episode count</th>
            <th scope="col">Time of each episode</th>
        </tr>
        </thead>
        <tbody>';
    while($inner_row = $inner_out->fetch_assoc()) {
        echo '<tr>
            <td>'.$inner_row['season_num'].'</td>
            <td>'.$inner_row['episode_cnt'].'</td>
            <td>'.$inner_row['time_ep'].' mins</td>
            </tr>';
        }
        echo '</tbody></table><br>';
        $inner_sql = "SELECT genre FROM $table_genre WHERE ".$row['id']." = id";
        $inner_out = mysqli_query($con, $inner_sql);
        echo 'Genre: ';
        while($inner_row = $inner_out->fetch_assoc()) {
            echo $inner_row['genre'].', ';
        }
        echo '</div></div><br><br>';
}

?>
    
</body>
</html>