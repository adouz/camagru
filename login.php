<?php
    // dont login if email is not verifyed
    require 'config/database.php';

    session_start();
    // if already logged in!
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        header("location: index.php");
        exit();
    }
    elseif (isset($_POST['submit'])){
        if (!empty(trim($_POST['uname'])) && !empty(trim($_POST['passwd']))){
            $user = $_POST['uname'];
            $pass = $_POST['passwd'];
            $hashpass = hash('sha256',$pass);
            $sql = "SELECT username, `password`, active FROM users WHERE username = ? AND `password` = ?";
            $qr = $db->prepare($sql);
            $qr->execute([ $user, $hashpass ]);
            while ($a = $qr->fetch()) {
                $active = $a['active']; 
                $username = $a['username'];
            }
            if ($active == 1){
                $_SESSION["loggedin"] = true;
                $_SESSION["username"] = $user;
                $sql = "SELECT email FROM users WHERE username = ?";
                $q = $db->prepare($sql);
                $q->execute([ $user ]);
                while ($a = $q->fetch()) {
                    $email = $a['email'];
                }
                $_SESSION["email"] = $email;
                // goto Welcome page!
                header("location: gallery.php?page=1");
                //echo "<h2>YOU loged in !!</h2>";
            }elseif( isset($active) && $active == 0){
                $error = "active";
                // Please active your account first by clicking link on your email.
            }else{
                $error = "incorrect";
                // Please enter a correct username and password.
            }
        }else {
            $error = "required";
            //"Username or password is empty\n";
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
            <h1>Login</h1>
            <form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
                <?php
                    if (isset($error)){
                        ?>
                        <div class="error">
                        <?php
                        // resend email verification?
                        if ($error == "active"){
                            echo "Please active your account first by clicking link on your email.";
                        }elseif($error == "incorrect"){
                            echo "Please enter a correct username and password."; 
                        }elseif($error == "required"){
                            echo "Username/password feild is empty.";
                        }
                        ?>
                        </div>
                        <?php
                    }
                ?>
                <p><label for="uname">Username</label></p> 
                <p><input type="text" placeholder="Enter Username" name="uname" required></p>
                <p><label for="passwd">Password</label></p> 
                <p><input type="password" placeholder="Enter Password" name="passwd" required></p>
                <p><button type="submit" name="submit">Login</button></p>
            </form>
            <div class="singup">
                <p><a href="index.php?u=forget">Forgotten Password?</a></p>
            </div>
        </div>
    </main>
</body>
<div class="footer">
    <p>Camagru 2019 © adouz</p>
</div>
</html>
