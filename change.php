<?php
require 'config/database.php';

session_start();
$user = $_SESSION['username'];

if (isset($_POST['submit'])){
    $newuser = $_POST['username'];
    $newmail = $_POST['email'];
    $newpass = $_POST['password1'];
    $oldpass = $_POST['oldpassword'];
    $hashpasswd = hash('sha256',$oldpass);
    // chekc if password correct
    $sql = "SELECT * FROM `users` WHERE `username` = ? AND `password` = ?";
    $qr = $db->prepare($sql);
    $qr->execute([ $user, $hashpasswd ]);
    $count = $qr->fetchColumn();
    if (!$count){
        header('Location: account.php?oldpass');
        exit();
    }
    // check 2 passwords are the same
    if ($newpass != $_POST['password2']){
        header('Location: account.php?confirmpass');
        exit();
    }
    // notification checkbox
    if (isset($_POST['notification'])){
        $noti = 1;
    }else{
        $noti = 0;
    }
    // check if input is valid
    // Check Username
    if ($newuser == ""){
        header('Location: account.php?usr=empty');
        // Empty
        exit();
    }elseif (!preg_match('/^[a-zA-Z0-9]{5,}$/', $newuser)){
        header('Location: account.php?usr=format');
        // only alphanumeric characters and more than 5 characters
        exit();
    }

    // Check Password
    if (!empty($newpass)){
        if (trim($newpass) == ""){
            header('Location: account.php?passwd=empty');
            // Empty
            exit();
        }elseif(!preg_match('/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.{8,})/', $newpass)){
            header('Location: account.php?passwd=format');
            // at least 8 characters, 1 alphabetical character, 1 numeric character
            exit();
        }
    }
    //Check Email
    if ($newmail == ""){
        header('Location: account.php?mail=empty');
         // Empty
        exit();
    }elseif(!preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', $newmail)){
        header('Location: account.php?mail=format');
        // valid email address
        exit();
    }
    $user = $_SESSION['username'];
    $email = $_SESSION["email"];
    // USERNAME if exists in db
    if ($newuser != $user){
        $sql = "SELECT * FROM users WHERE username = ?";
        $qr = $db->prepare($sql);
        $qr->execute([ $newuser ]);
        $count = $qr->fetchColumn();
        if ($count){
            header('Location: account.php?usr=exists');   
            exit();
        }
    }else {
        $newuser = $user;
    }
    if ($newmail != $email){
        // EMAIL if exists in db        
        $sql = "SELECT * FROM users WHERE email = ?";
        $qr = $db->prepare($sql);
        $qr->execute([ $newmail ]);
        $count = $qr->fetchColumn();
            if ($count) {
          header('Location: error.php?mail=exists');
         exit();
        }
    }else {
        $newmail = $email;
    }

    
    //update if password not empty else if empty
    if (!empty($newpass)){
        $hashpass = hash('sha256',$newpass);
        $sql = "UPDATE users SET username = ?, email = ?, `password` = ?, `notification` = ? WHERE username = ?";
        $qr = $db->prepare($sql);
        if ($qr->execute([ $newuser, $newmail, $hashpass, $noti, $user ])){
        // update all tables with new info
            $_SESSION["username"] = $newuser;
            $_SESSION["email"] = $newmail;
            $sql = "UPDATE imgs_cmt SET username = ? WHERE username = ?";
            $q = $db->prepare($sql);
            $q->execute([ $newuser, $user ]);
            $sql = "UPDATE users_imgs SET username = ? WHERE username = ?";
            $q = $db->prepare($sql);
            $q->execute([ $newuser, $user ]);
        }
    }else{
        $sql = "UPDATE users SET username = ?, email = ?, `notification` = ? WHERE username = ?";
        $qr = $db->prepare($sql);
        if($qr->execute([ $newuser, $newmail, $noti, $user ])){
        // update all tables with new info
            $_SESSION["username"] = $newuser;
            $_SESSION["email"] = $newmail;
            $sql = "UPDATE imgs_cmt SET username = ? WHERE username = ?";
            $q = $db->prepare($sql);
            $q->execute([ $newuser, $user ]);
            $sql = "UPDATE users_imgs SET username = ? WHERE username = ?";
            $q = $db->prepare($sql);
            $q->execute([ $newuser, $user ]);
        }
    }
}
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: index.php?u=login");
        exit();
    }else {
        header("location: account.php");
        exit();
    }


?>