<?php
    //include_once 'config/setup.php';

    session_start();
    // if already logged in!
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        header("location: gallery.php?page=1");
        exit();
    }
    if ($_GET["u"] === "login"){
        header("location: login.php");
        exit();
    }
    elseif ($_GET["u"] === "singup"){
        header("location: singup.php");
        exit();
    }    
    elseif ($_GET["u"] === "forget"){
        header("location: forget_password.php");
        exit();
    }else{
        header("location: gallery.php?page=1");
        exit();
    }
?>