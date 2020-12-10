<!DOCTYPE html>
<html>

<head>
</head>

<body>
<?php
$genre =$_GET['genre'];

include('connection.php');
echo '<h5>'.$genre.'</h5><hr>';

$sql="SELECT * FROM $table_webseries WHERE id IN (SELECT id FROM $table_genre WHERE genre='".$genre."')";
$out = mysqli_query($con,$sql);
if(mysqli_num_rows($out) == 0){
    echo "<h1>No series with genre ".$genre."</h1>";
}else{
    // Displaying data
    $j = 1;
    echo "<div class='container'><table><tr>";
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

mysqli_close($con);
?>
</body>
</html>