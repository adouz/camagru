<?php
// confirme password
// add uppercase to password
    require 'config/database.php';

    session_start();
    // if already logged in!
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        header("location: index.php");
        exit();
    }

    function sendemail($email, $hash, $user, $passwd){
        $to = $email;
        $sbjct = 'Camagru | Account Verification';
        $from = 'noreply@camagru.com';
        $head = 'From:'. $from;
        $msg = '
        <br>    
        Thanks for signing up!<br>
        Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.<br>
        <br>
        ------------------------<br>
        <br>
        Please click this link to activate your account:<br>
        http://localhost:3000/verify.php?email='.$email.'&hash='.$hash.'<br>
        <br>
        ';
        mail($to, $sbjct, $msg, $head);
    }

    if (isset($_POST['submit'])){
        $user = $_POST['uname'];
        $passwd = $_POST['passwd'];
        $email = $_POST['email'];
        
        if ($passwd != $_POST['passwd0']){
            $passerror = "notmatch";
        }
        // Check Username
        if ($user == ""){
            $usrerror = "empty";
            // Empty
        }elseif (!preg_match('/^[a-zA-Z0-9]{5,}$/', $user)){
            $usrerror = "format";
            // only alphanumeric characters and more than 5 characters
        }

        // Check Password
        if ($passwd == ""){
            $passerror = "empty";
            // Empty
        }elseif(!preg_match('/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.{8,})/', $passwd)){
            $passerror = "passwd";
            // at least 8 characters, 1 alphabetical character, 1 numeric character
        }
        //Check Email
        if ($email == ""){
            $mailerror = "empty";
             // Empty
        }elseif(!preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', $email)){
            $mailerror = "format";
            // valid email address
        }
        // USERNAME if exists in db
        $sql = "SELECT * FROM users WHERE username = ?";
        $qr = $db->prepare($sql);
        $qr->execute([ $user ]);
        $count = $qr->fetchColumn();
        if ($count) {
            $usrerror = "exists";
        }
        // EMAIL if exists in db        
        $sql = "SELECT * FROM users WHERE email = ?";
        $qr = $db->prepare($sql);
        $qr->execute([ $email ]);
        $count = $qr->fetchColumn();
        if ($count) {
            $mailerror = "exists";
        }
        if (empty($mailerror.$usrerror.$passerror)){
            // SEND HASH FOR VERIFICATION AND PASSWOR
            $hash = md5(rand(0,1000));
            $hashpass = hash('sha256',$passwd);
            // INSERT INTO DATABASE
            $sql = "INSERT INTO users (username, `password`, email, `hash`) VALUES (?, ?, ?, ?)";
            $qr = $db->prepare($sql);
            if($qr->execute([ $user, $hashpass, $email, $hash ])){
                // SEND EMAIL TO USER FOR VERIFICATION
                sendemail($email, $hash, $user, $passwd);
                header('Location: verify.php?verifcation');
            }else{
                die(mysql_error());
            }
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Singup | Camagru</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="./src/pics/favicon.png">
    <link rel="stylesheet" href="./src/css/style.css">
    <link rel="stylesheet" href="./src/css/form.css">
</head>
<body>
    <header>
        <a href="index.php" class="logo"><img src="src/pics/logo.png"></a>
        <div class="header-right">
            <a href="gallery.php?page=1">Gallery</a>
            <a href="index.php?u=login">Login</a>
            <a href="index.php?u=singup" class="active">Sing Up</a>
        </div>
    </header>
    <main>
        <div class="form">
            <h1>Sing Up</h1>
            <form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST" name="register">
                <div class="error">
                <?php
                    if ($usrerror) {
                        if ($usrerror == "empty"){
                            echo "username feild is empty.";
                        }
                        if ($usrerror == "format"){
                            echo "please change your username (only alphanumeric characters and more than 5 characters)";
                        }
                        if ($usrerror == "exists"){
                            echo "username is already taken.";
                        }
                    }elseif ($passerror) {
                        if ($passerror == "notmatch"){
                            echo "Passwords Don't match.";
                        }
                        if ($passerror == "empty"){
                            echo "password feild is empty.";
                        }
                        if ($passerror == "passwd"){
                            echo "please change your password (at least 8 characters, 1 alphabetical character, 1 numeric character)";
                        }
                    }elseif ($mailerror) {
                        if ($mailerror == "empty"){
                            echo "Email feild is empty.";
                        }
                        if ($mailerror == "format"){
                            echo "please entre a valid email.";
                        }
                        if ($mailerror == "exists"){
                            echo "Email is Already in Use.";
                        }
                    }
                ?>
                </div>
                <p><label for="uname">Username</label></p>
                <p><input type="text" placeholder="Enter Username" minlength="5" maxlength="30" name="uname" required></p>
                <p><label for="passwd">Password</label></p>
                <p><input type="password" minlength="8" maxlength="30" placeholder="Enter Password" name="passwd" required></p>
                <p><label for="passwd0">Confirm Password</label></p>
                <p><input type="password" minlength="8" maxlength="30" placeholder="Enter Password again" name="passwd0" required></p>
                <p><label for="email">E-mail</label></p>
                <p><input type="email" placeholder="Enter e-mail" maxlength="50" name="email" required></p>
                <p><button type="submit" name="submit">Register</button></p>
            </form>
            <div class="error" id="error"></div>
        </div>
    </main>
</body>
<div class="footer">
    <p>Camagru 2019 © adouz</p>
</div>
</html>
<script src="src/js/singup.js"></script>