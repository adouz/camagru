<?php
    require 'config/database.php';
    date_default_timezone_set('Africa/Casablanca');
    session_start();
    // if already logged in!
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        header("location: index.php");
        exit();
    }
    // change password
    if (isset($_POST['change'])){
        $pass1 = $_POST['pass1'];
        $pass2 = $_POST['pass2'];
        $email = $_POST['email'];
        $key = $_POST['key'];
        if ($pass1 != $pass2){
            // e0: please entre the same password.
            header("location: reset_password.php?email=$email&key=$key&error=e0");
        }elseif(empty(trim($pass1)) || empty(trim($pass2))){
            // e1: you cant leave a field empty.
            header("location: reset_password.php?email=$email&key=$key&error=e1");
        }elseif(!preg_match('/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.{8,})/', $pass1)){
            // e2: you password shoulde containe at least 8 characters(1 alphabetical character, 1 numeric character).
            header("location: reset_password.php?email=$email&key=$key&error=e2");
        }else{
            // UPDATE password.
            $hashpass = hash('sha256',$pass1);
            $sql = "UPDATE users SET `password` = ? WHERE email = ?";
            $q = $db->prepare($sql);
            $q->execute([ $hashpass, $email ]);
            // Delete row form password_reset
            $sql = "DELETE FROM password_reset WHERE email = ?";
            $q = $db->prepare($sql);
            $q->execute([ $email ]);
            header("location: index.php?u=login");
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login | Camagru</title>
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
            <a href="index.php?u=login" class="active">Login</a>
            <a href="index.php?u=singup">Sing Up</a>
        </div>
    </header>
    <main>
    <center>
<?php
    if (isset($_GET['email']) && isset($_GET['key'])){
        $key = $_GET['key'];
        $email = $_GET['email'];
        $curDate = date("Y-m-d H:i:s");
        
        $sql = "SELECT * FROM password_reset WHERE `key` = ? AND `email` = ?";
        $q = $db->prepare($sql);
        $q->execute([ $key, $email ]);
        while ($a = $q->fetch()) {
            $expDate = $a['expDate'];
        }
        // if not found on db
        if (!isset($expDate)){
            $error = "<h2>Invalid Link</h2><p>The link is invalid/expired. 
            Either you did not copy the correct link from the email, 
            or you have already used the key in which case it is deactivated.</p>";
        }
        elseif($expDate >= $curDate){
            $reset = "OK";
        }else {
            $error = "<h2>Link Expired</h2>
            <p>The link is expired. You are trying to use the expired link which 
            as valid only 24 hours (1 days after request).<br /><br /></p>";
        }
    }
    if (isset($error)){
        echo $error;
    }
    elseif (isset($reset)){
        ?>
        <div class="form">
            <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
                <?php
                    if(isset($_GET['error'])){
                        ?>
                        <div class="error">
                        <?php
                        $err = $_GET['error'];
                        if ($err == "e0"){
                            echo "please entre the same password.";
                        }elseif($err == "e1"){
                            echo "you cant leave a field empty.";
                        }elseif($err == "e2"){
                            echo "you password shoulde containe at least 8 characters(1 alphabetical character, 1 numeric character).";
                        }else{
                            echo "please entre your new password.";
                        }
                        ?>
                        </div>
                        <?php
                    }
                ?>
                <p><label for="pass1">New Password</label></p>
                <p><input type="password" name="pass1"></p>
                <p><label for="pass2">Re-Entre New Password</label></p>
                <p><input type="password" name="pass2"></p>
                <p><input type="hidden" name="email" value="<?php echo $email; ?>"></p>
                <p><input type="hidden" name="key" value="<?php echo $key; ?>"></p>
                <p><button type="submit" name="change">Change Password</button></p>
            </form>
        <div>
        <?php
    }else{
        ?>
        <h1>404</h1>
        <h2>Page not found!</h2>
        </center>
        <?php
    } 
?>
    </main>
</body>
<div class="footer">
    <p>Camagru 2019 © adouz</p>
</div>
</html>