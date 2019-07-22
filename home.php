<?php
// The creation of the final image (so among others the superposing of the two images)must be done on the server side, in PHP.
// The user should be able to delete his edited images, but only his, not other users’creations.
    require 'config/database.php';

    session_start();
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: index.php");
        exit();
    }
    $user = $_SESSION['username'];
    // create dir for new users
    $dir = 'userspics/'.$user.'/';
    if (!is_dir($dir)){
        mkdir($dir, 0777, true);
    }
    if (isset($_POST['delete'])){
        $img = $_POST['img'];
        $imgid = $_POST['imgid'];
        // delete photo
        $sql = "DELETE FROM users_imgs WHERE username = ? AND photo = ?";
        $w = $db->prepare($sql);
        $exec = $w->execute([ $user, $img ]);
        // delete all comment on photo
        $sql = "DELETE FROM imgs_cmt WHERE photo = ?";
        $w = $db->prepare($sql);
        $exec = $w->execute([ $img ]);
        // delete all likes on photo
        $sql = "DELETE FROM likes WHERE photoid = ?";
        $w = $db->prepare($sql);
        $exec = $w->execute([ $imgid ]);
        // delete photo file if exists
        if (file_exists($img)){
            if ($exec){
                unlink($img);
            }
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home | Camagru</title>
    <link rel="icon" href="./src/pics/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./src/css/style.css">
    <link rel="stylesheet" href="./src/css/home.css">
</head>
<body>
    <header>
        <a href="index.php" class="logo"><img src="src/pics/logo.png"></a>
        <div class="header-right">
            <a href="home.php" class="active">Home</a>
            <a href="gallery.php?page=1">Gallery</a>
            <a href="account.php">Account</a>    
            <a href="logout.php">Logout</a>
        </div>
    </header>
    <!-- select image or upload -->
    <main>
    <div class="camera">
        <div class="edits">
            <h2>Welcome <?php echo  $_SESSION["username"]?></h2>
            <h3>chose a sticker to take a photo.</h3>
            <form>
                <input type="radio" name="photo" id="thug"><img src="src/pics/thug.png">
                <input type="radio" name="photo" id="ahshit"><img src="src/pics/ahshit.png">
                <input type="radio" name="photo" id="morocco"><img src="src/pics/morocco.png">
                <input type="radio" name="photo" id="wanted"><img src="src/pics/wanted.png">
                <p class="upload">upload: <input type="file" accept="image/*" id="dropimg"></p>
            </form>
            <input type="checkbox" name="mirror" id="mirror">Mirror Effect
        </div>
        <!-- webcam stream -->
        <div class="stream" id="stream">
            <p id="video"><video class="live" autoplay poster='/src/pics/test_pattern.svg' ></video></p>
            <p><img class="preimg" id="preimg" src=""></p>
            <p><input type="hidden" name="user" value="<?php echo $_SESSION["username"]?>"></p>
            <p id="takepic"><button class="unclick" id="screenbutton">Take Picture</button></p>
            <!-- show the edited image -->
        </div>
        <div>
            <img src="" id="showimg">
            <canvas style="display:none;"></canvas>
        </div>
    </div>
    <!-- sidebar images -->    
    <div class="imgs" id="imgs">
        <?php
         // check if there is any user images in db and show them!
        $sql = "SELECT id, photo  FROM users_imgs WHERE username = ? ORDER BY id DESC";
        $qr = $db->prepare($sql);
        $qr->execute([ $_SESSION['username'] ]);
        while ($a = $qr->fetch()) {
            ?>
            <!-- every image form -->
            <div class="oldimg">
                <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
                    <img src="<?php echo $a["photo"]; ?>">
                    <input type="hidden" name="img" value="<?php echo $a["photo"]; ?>">
                    <input type="hidden" name="imgid" value="<?php echo $a["id"]; ?>">
                    <input type="submit" name="delete" value="DELETE">
                    <a class="down" href="<?php echo $a["photo"]; ?>" download>DOWNLOAD</a> 
                </form>
            </div>
            <?php
        }
        ?>
    </div>
    </main>
    <!-- javascript -->
    <script src="./src/js/home.js"></script>
    <div class="footer">
        <p>Camagru 2019 © adouz</p>
    </div>
</body>
</html>



 
