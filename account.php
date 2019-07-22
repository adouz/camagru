<?php
    require 'config/database.php';

    session_start();
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: index.php");
        exit();
    }
    // get user info
    $user = $_SESSION["username"];    
    $sql = "SELECT email, active, `notification` FROM users WHERE username = ?";
    $q = $db->prepare($sql);
    $q->execute([ $user ]);
    while ($a = $q->fetch()) {
        $email = $a['email'];
        $noti = $a['notification'];
        $active = $a['active'];
    }    
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account | Camagru</title>
    <link rel="icon" href="./src/pics/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./src/css/style.css">
    <link rel="stylesheet" href="./src/css/form.css">
</head>
<body>
    <header>
        <a href="index.php" class="logo"><img src="src/pics/logo.png"></a>
        <div class="header-right">
            <a href="home.php">Home</a>
            <a href="gallery.php?page=1">Gallery</a>
            <a href="account.php" class="active">Account</a>
            <a href="logout.php">Logout</a>
        </div>
    </header>
    <main>
        <div class="form">
            <h1>Account</h1>
            <form action="change.php" method="post">
                <div class="error">
                    <?php
                    if (isset($_GET['oldpass'])){
                        echo "your current password is incorrect.";
                    }
                    elseif (isset($_GET['confirmpass'])){
                        echo "Passwords Don't match.";
                    }
                    elseif (isset($_GET['usr'])){
                        if ($_GET['usr'] == "empty"){
                            echo "username feild is empty.";
                        }
                        if ($_GET['usr'] == "format"){
                            echo "please change your username (only alphanumeric characters and more than 5 characters)";
                        }
                        if ($_GET['usr'] == "exists"){
                            echo "username is already taken.";
                        }  
                    }
                    elseif (isset($_GET['passwd'])) {
                        if ($_GET['passwd'] == "empty"){
                            echo "password feild is empty.";
                        }
                        if ($_GET['passwd'] == "format"){
                            echo "please change your password (at least 8 characters, 1 alphabetical character, 1 numeric character)";
                        }
                    }
                    elseif (isset($_GET['mail'])) {
                        if ($_GET['mail'] == "empty"){
                            echo "email feild is empty.";
                        }
                        if ($_GET['mail'] == "format"){
                            echo "please entre a valid email.";
                        }
                        if ($_GET['mail'] == "exists"){
                            echo "Email is Already in Use.";
                        }
                    }

                    ?>
                </div>
                <p><label for="username">Username</label></p>
                <p><input type="text" value="<?php echo $user; ?>" name="username"></p>
                <p><label for="email">Email</label></p>
                <p><input type="text" value="<?php echo $email; ?>" name="email"></p>
                <p><label for="password1">New Passsword</label></p>
                <p><input type="password" name="password1"></p>
                <p><label for="password2">Confirm New Passsword</label></p>
                <p><input type="password" name="password2"></p>
                <p><input type="checkbox" name="notification" value="notification" <?php if ($noti == 1){echo "checked";}?>>
                <label for="notification">Get notification on your email.</label></p>
                <p><label for="oldpassword">Current Passsword</label></p>
                <p><input type="password" name="oldpassword"></p>
                <p><button type="submit" value="submit" name="submit">Submit</button></p>
            </form>
        </div>
    </main>
</body>
<div class="footer">
        <p>Camagru 2019 © adouz</p>
</div>
</html>