<?php
require 'config/database.php';

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php?u=login");
    exit();
}

if (isset($_POST['cmtbutton'])) {
    $user = $_POST['usr'];
    $photo = $_POST['img'];
    // protect agianset xss with FILTER_SANITIZE_STRING
    $cmt = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');
    $usr = $_POST['usrp'];
    // check comment that its not empty
    if (strlen(trim($cmt)) == 0){
        header("location: gallery.php");
        exit();
    }
    if (strlen($cmt) > 150){
        header("location: gallery.php");
        exit();
    }
    $sql = "INSERT INTO imgs_cmt (username, comment, photo) VALUE (?, ?, ?)";
    $q = $db->prepare($sql);
    $q->execute([ $user, $cmt, $photo ]);
    // see if user have notification on
    $sql = "SELECT `notification` FROM users WHERE username = ?";
    $qr = $db->prepare($sql);
    $qr->execute([ $usr ]);
    while ($x = $qr->fetch()){
        $notif = $x['notification'];
    }
    if($notif == 1){
        // get photo id
        $sql = "SELECT id FROM users_imgs WHERE photo = ?";
        $q = $db->prepare($sql);
        $q->execute([ $photo ]);
        while ($a = $q->fetch()) {
            $photoid = $a["id"];
        }
        //send mail
        $sql = "SELECT email FROM users WHERE username = ?";
        $q = $db->prepare($sql);
        $q->execute([ $usr ]);
        while ($a = $q->fetch()) {
            $email = $a["email"];
        }
        sendmail($email, $user, $cmt, $photoid);        
    }
}
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php?u=login");
    exit();
}else {
    header("location: gallery.php");
    exit();
}

function sendmail($email, $user, $cmt, $photoid){
    $to = $email;
    $sbjct = 'Camagru | '.$user.' commented on one of your photos';
    $msg = '
    <br>
    <b>'.$user.'</b> commented:<br><i>"'.$cmt.'"</i> on your post:
    <br>
    http://localhost:3000/photo.php?id='.$photoid.'<br>
    <br>
    ';
    $head = 'From:noreply@camagru.ma';
    mail($to, $sbjct, $msg, $head);
}

?>