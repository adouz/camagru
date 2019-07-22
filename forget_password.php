<?php
    function sendmail($email, $key){
        $to = $email;
        $sbjct = 'Camagru | Reset Password';
        $msg = '
        <br>
        Please click on the following link to reset your password.<br>
        <br>
        http://localhost:3000/reset_password.php?email='.$email.'&key='.$key.'<br>
        <br>
        Please be sure to copy the entire link into your browser.<br>
        * The link will expire after 1 day for security reason.<br>
        <br>
        ';
        $head = 'From:noreply@camagru.ma' . "\r\n";
        mail($to, $sbjct, $msg, $head);
    }

    require 'config/database.php';
    date_default_timezone_set('Africa/Casablanca');
    session_start();
    // if already logged in!
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        header("location: index.php");
        exit();
    }
    if (isset($_POST['send'])){
        $email = $_POST['email'];
        // checking input if is a valid input!
        if (empty(trim($email))){
            $error = "empty";
        }elseif(!preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', $email)){
            $error = "invalid";
        }else {
            $sql = "SELECT * FROM users WHERE email = ?";
            $q = $db->prepare($sql);
            $q->execute([ $email ]);
            $count = $q->fetchColumn();
            if ($count > 0){
                //date for link to expire afther 1 day of sending email
                $expFormat = mktime(
                    date("H"), date("i"), date("s"), date("m") ,date("d")+1, date("Y")
                );
                $expDate = date("Y-m-d H:i:s",$expFormat);
                // generate strong key
                $key = md5(2418*2+$email);
                $addKey = substr(md5(uniqid(rand(),1)),3,10);
                $key = $key . $addKey;
                // Insert into table
                $sql = "INSERT INTO `password_reset` (`email`, `key`, `expDate`) VALUES (?, ?, ?)";
                $q = $db->prepare($sql);
                $q->execute([ $email, $key, $expDate ]);
                //send email to user
                sendmail($email, $key);
                header('Location: verify.php?reset');
            }else {
                // we dont this email on db!
                $error = "unavailable";                
            }
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
        <div class="form">
            <h2>Forgot your password?</h2>   
            <form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
                <?php
                    if (isset($error)){
                        ?>
                        <div class="error">
                        <?php
                        // resend email verification?
                        if ($error == "empty"){
                            echo "Please Entre your Email.";
                        }elseif($error == "invalid"){
                            echo "Please Enter a valid Email."; 
                        }elseif($error == "unavailable"){
                            echo "Your Email is not used by any user.";
                        }
                        ?>
                        </div>
                        <?php
                    }
                ?>
                <p><label for="email">Enter Email Address To Send Password Link.</label></p> 
                <p><input type="text" placeholder="Enter Email" name="email" required></p>
                <p><button type="submit" name="send">Send</button></p>
            </form>
            <div class="singup">
                <p><a href="index.php?u=singup">Create account?</a></p>
            </div>
        </div>
    </main>
</body>
<div class="footer">
    <p>Camagru 2019 © adouz</p>
</div>
</html>