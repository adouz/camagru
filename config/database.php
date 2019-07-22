<?php
    $DB_SERVER = '127.0.0.1';
    $DB_DATABASE = 'camagru';
    
    $DB_DSN = "mysql:dbname=$DB_DATABASE;host=$DB_SERVER";
    $DB_USER = 'root';
    $DB_PASSWORD = 'tiger';

    //$db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
    try {
        $db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (\PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
    /*if ($db === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }*/

    //redirct

 ?>