<?php
    require 'config/database.php';

    session_start();
    // if already logged in!
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        header("location: index.php");
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>please confirm your Account</title>
    <link rel="icon" href="./src/pics/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./src/css/style.css">
    <link rel="stylesheet" href="./src/css/form.css">
</head>
<body>
    <header>
        <a href="index.php" class="logo"><img src="src/pics/logo.png"></a>
        <div class="header-right">
            <a href="gallery.php?page=1">Gallery</a>
            <a href="index.php?u=login">Login</a>
            <a href="index.php?u=singup">Sing Up</a>
        </div>
    </header>        
<main>
<center>
<?php
    if (isset($_GET['email']) && isset($_GET['hash'])){
        $email = $_GET['email'];
        $hash = $_GET['hash'];
        $sql = "SELECT email, `hash`, active FROM users WHERE email=? AND hash=? AND active='0'";
        $qr = $db->prepare($sql);
        $qr->execute([ $email, $hash ]);
        $count = $qr->fetchColumn();
        if ($count){
            $sql = "UPDATE users SET active='1' WHERE email=? AND hash=? AND active='0'";
            $qr = $db->prepare($sql);
            $qr->execute([ $email, $hash ]);
            ?>
            <h2>Your account has been activated, you can now <a href="index.php?u=login">login</a></h2>
            <?php
        }else {
            ?>
            <h2>The url is either invalid or you already have activated your account.</h2>
            <?php
        }
    }elseif (isset($_GET['verifcation'])) {
        ?>
            <br><h3>please confirm your Account by clicking the activation link that has been send to your email.</h3>
        <?php
    }elseif(isset($_GET['reset'])){
        ?>
        <br><h3>please click link on your email to reset your Password.</h3>
        <?php
    }else {
        ?>
        <h1>404</h1>
        <h2>Page not found!</h2>
        <?php
    }
?>
    </center>
    </main>
</body>
<div class="footer">
        <p>Camagru 2019 © adouz</p>
</div>    
</html>