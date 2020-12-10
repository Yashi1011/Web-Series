<?php
    //Declaring variables.
    $host = "localhost";  
    $user = "root";  
    $password = ''; 
    $db = "dbms";
    $table_users = "users";
    $table_webseries = "web_series";
    $table_seasons = "seasons";
    $table_genre = "genre";

      
    // Connecting to database.
    $con = mysqli_connect($host, $user, $password);


    // Check connection
    if(!$con) {
        die("<br>Connection failed: " . mysqli_connect_error());
    }

    // Create database
    echo '<script>console.log("Connected successfully")</script>';
    $sql = "CREATE DATABASE IF NOT EXISTS $db";


    if (!mysqli_query($con, $sql)) {
        echo "<br>Error creating database: " . mysqli_error($con);
    }

    // Connecting to database
    $sql = "USE $db";
    if (!mysqli_query($con, $sql)) {
        echo "<br>Error creating database: " . mysqli_error($con);
    }

    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS $table_users ( 
        username VARCHAR(100) NOT NULL , 
        password VARCHAR(100) NOT NULL , 
        phone VARCHAR(20) NOT NULL ,
        birth_date DATE NOT NULL,
        type VARCHAR(10) NOT NULL,
        reg_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (username))";

    if (!mysqli_query($con, $sql)) {
        echo "<br>Error creating table: " . mysqli_error($con);
    }
    
    // create table to store data about series
    $sql = "CREATE TABLE IF NOT EXISTS $table_webseries(
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(225) NOT NULL UNIQUE,
        seasons INT,
        rating INT NOT NULL, 
        image VARCHAR(225) NOT NULL,
        video VARCHAR(225) NOT NULL,
        PRIMARY KEY(id))";
        
    if (!mysqli_query($con, $sql)) {
        echo "<br>Error creating table: " . mysqli_error($con);
    }

    // create table to store data about each season
    $sql = "CREATE TABLE IF NOT EXISTS $table_seasons(
        id INT NOT NULL,
        season_num INT NOT NULL,
        episode_cnt INT NOT NULL,
        time_ep VARCHAR(10) NOT NULL,
        uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY(id,season_num),
        CONSTRAINT fkey_seasons FOREIGN KEY (id) REFERENCES $table_webseries(id)
        ON UPDATE CASCADE ON DELETE CASCADE)";
        
    if (!mysqli_query($con, $sql)) {
        echo "<br>Error creating table: " . mysqli_error($con);
    }

    // create table to store data about each season
    $sql = "CREATE TABLE IF NOT EXISTS $table_genre(
        id INT NOT NULL,
        genre VARCHAR(225),
        PRIMARY KEY(id, genre),
        CONSTRAINT fkey_genre FOREIGN KEY (id) REFERENCES $table_webseries(id)
        ON UPDATE CASCADE ON DELETE CASCADE)";
        
    if (!mysqli_query($con, $sql)) {
        echo "<br>Error creating table: " . mysqli_error($con);
    }
    
    // Funtion to encrypt the given text.
    function encrypt($str){
        $ciphering = "BF-CBC";
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
        $encryption_iv = '808fc44d';
        $encryption_key = openssl_digest(php_uname(), 'MD5', TRUE);
        $encryption = openssl_encrypt($str, $ciphering, $encryption_key, $options, $encryption_iv);
        return $encryption;
    }

    // Function to decrypt the given string.
    function decrypt($str1){
        $ciphering = "BF-CBC";
        $iv_length = openssl_cipher_iv_length($ciphering);
        $decryption_key = openssl_digest(php_uname(), 'MD5', TRUE);
        $options = 0;
        $encryption_iv = '808fc44d';
        $decryption = openssl_decrypt ($str1, $ciphering, $decryption_key, $options, $encryption_iv);
        return $decryption;
    }

?>  
