<?php
// infinite pagination of the gallery part of the site
    require 'config/database.php';

    session_start();
    // get page number for pagination
    if (is_numeric($_GET["page"])){
        $page = $_GET["page"];
        $start = 10 * ($page - 1);
        $row = 10;
    }else {
        header("location: gallery.php?page=1");
    }
    // get user id
    if (isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === true){
        $user = $_SESSION['username'];
        $sql = "SELECT id FROM users WHERE username = ?";
        $qr = $db->prepare($sql);
        $qr->execute([ $user ]);
        while ($i = $qr->fetch()) {$userid = $i["id"];}
    }
    if (isset($_SESSION["loggedin"]) && ($_POST['like'] || $_POST['unlike'])){
        // add like
        if($_POST['like']) {
            $pid = $_POST['photoid'];
            $sql = "UPDATE users_imgs SET `likes` = `likes`+1 WHERE `id` = ?";
            $r = $db->prepare($sql);
            $r->execute([ $pid ]);
            $sql = "INSERT INTO likes (userid, photoid) VALUES (?, ?)";
            $r = $db->prepare($sql);
            $r->execute([ $userid, $pid ]);
        }
        if ($_POST['unlike']){
            $pid = $_POST['photoid'];
            $sql = "UPDATE users_imgs set `likes` = `likes`-1 where `id` = ?";
            $r = $db->prepare($sql);
            $r->execute([ $pid ]);
            $sql = "DELETE FROM likes WHERE userid = ? AND photoid = ?";
            $rE = $db->prepare($sql);
            $rE->execute([ $userid, $pid ]);
        }
    }elseif($_POST['like'] || $_POST['unlike']){
        header("location: index.php?u=login");
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Gallery | Camagru</title>
    <link rel="icon" href="./src/pics/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./src/css/style.css">
    <link rel="stylesheet" href="./src/css/gallery.css">
</head>
<body>
    <?php
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        ?>
        <header>
            <a href="index.php" class="logo"><img src="src/pics/logo.png"></a>
            <div class="header-right">
                <a href="gallery.php?page=1" class="active">Gallery</a>
                <a href="index.php?u=login">Login</a>
                <a href="index.php?u=singup">Sing Up</a>
            </div>
        </header>
        <?php
    }else{
        ?>
        <header>
            <a href="index.php" class="logo"><img src="src/pics/logo.png"></a>
            <div class="header-right">
                <a href="home.php">Home</a>
                <a href="gallery.php?page=1" class="active">Gallery</a>
                <a href="account.php">Account</a>
                <a href="logout.php">Logout</a>
            </div>
        </header>
        <?php
    }
    ?>
    <main>
    <h1>Gallery</h1>
    <?php
        // pagination
        try{
            $sql="SELECT COUNT(*) FROM users_imgs";
            $re = $db->prepare($sql);
            $re->execute();
            $rows = $re->fetchColumn();
        }catch(PDOException $e){
            exit();
        }
        $pages = ceil($rows / $row);
        for ($i=1; $i <= $pages; $i++) { 
            echo '<a href="gallery.php?page=' . $i . '">' . $i . '</a>    ';
        }
    ?>
    <hr>
    <div class="posts">
        <?php
            // get all images from database
            try{
                $sql = "SELECT id, username, photo FROM users_imgs ORDER BY id DESC LIMIT $start,$row";
                $qr = $db->prepare($sql);
                $qr->execute();
            }catch(PDOException $e){
                exit();
            }
            while ($a = $qr->fetch()) {
                ?>
            <div class="post">
                <?php
                ?>
                <div class="photo">
                <?php
                $photo = $a["photo"];
                $user = $a["username"];
                $photoid = $a["id"];
                $phtolink = "http://localhost:3000/photo.php?id=".$photoid;
                ?>
                <p><?php echo $user?></p><a href="<?php echo $phtolink;?>"><img src='<?php echo $photo;?>'></a>
            </div>
            <div class="share">
                <p>Share: </p>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $phtolink;?>" class="facebook"><img src="src/pics/facebook.png"></a>
                <a href="https://twitter.com/share?url=<?php echo $phtolink;?>&text=<?php echo $user." post a photo on camagru.";?>&via=0xd002" class="twitter"><img src="src/pics/twitter.png"></a>
                <a href="https://reddit.com/submit?url=<?php echo $phtolink;?>&title=<?php echo $user." post a photo on camagru.";?>" class="reddit"><img src="src/pics/reddit.png"></a>
            </div>
            <div class="like">
            <?php
                if (isset($_SESSION["loggedin"])){
            ?>
                <form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
                    <input type="hidden" name="photoid" value="<?php echo $photoid ?>">
                    <?php
                        $sql = "SELECT id FROM likes WHERE userid = ? AND photoid = ?";
                        $c = $db->prepare($sql);
                        $c->execute([ $userid, $photoid ]);
                        $count = $c->fetchColumn();
                        if ($count){
                            echo "<input type ='submit' value ='👎' name='unlike'/>";
                        }else{
                            echo "<input type ='submit' value ='👍' name='like'/>";
                        }
                    ?>
                </form>
                <?php
                }
                // get number of likes
                    $sql = "SELECT likes FROM users_imgs WHERE id = ?";
                    $a = $db->prepare($sql);
                    $a->execute([ $photoid ]);
                    while ($lik = $a->fetch()) {$likes = $lik["likes"];}
                    echo "<div class=\"likenum\">$likes likes</div>";
                ?>
            </div>

                <div class="comments">
                <?php
                // fetch for comments on this pictures
                $sql = "SELECT username, comment FROM imgs_cmt WHERE photo = ?";
                $q = $db->prepare($sql);
                $q->execute([ $photo ]);
                while ($x = $q->fetch()) {
                    // the comment
                    $cmt = "      ".$x["comment"];
                    $userc = $x["username"];
                    ?>
                    <div class="com">
                        <b><?php echo $userc.":";?></b>
                        <?php echo $cmt; ?>
                    </div>
                    <?php
                }
                ?>
                </div>
                <?php 
                if (isset($_SESSION["loggedin"])){
                ?>
                <form action="comment.php" method="post" class="comment">
                    <input type="hidden" name="img" value="<?php echo $photo ?>">
                    <input type="hidden" name="usr" value="<?php echo $_SESSION["username"] ?>">
                    <input type="hidden" name="usrp" value="<?php echo $user ?>">
                    <input type="text" minlength="1" maxlength="150" name="comment">
                    <input type="submit" value="comment" name="cmtbutton">
                </form>
                <?php 
                }
                ?>
        </div>
            <hr>
         <?php
            }
        //pagination
        $sql="SELECT COUNT(*) FROM users_imgs";
        $re = $db->prepare($sql);
        $re->execute();
        $rows = $re->fetchColumn();
        $pages = ceil($rows / $row);
        for ($i=1; $i <= $pages; $i++) { 
            echo '<a href="gallery.php?page=' . $i . '">' . $i . '</a>    ';
        }
        ?>
    </div>
    </main>
</body>
<div class="footer">
        <p>Camagru 2019 © adouz</p>
</div>
</html>