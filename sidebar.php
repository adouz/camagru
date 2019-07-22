<?php
// The creation of the final image (so among others the superposing of the two images)must be done on the server side, in PHP.
// The user should be able to delete his edited images, but only his, not other users’creations.
    require 'config/database.php';

    session_start();
    $user = $_SESSION['username'];
    if (isset($_POST['delete'])){
        $img = $_POST['img'];
        $sql = "DELETE FROM users_imgs WHERE username = ? AND photo = ?";
        $w = $db->prepare($sql);
        $exec = $w->execute([ $user, $img ]);
        if ($exec){
            unlink($img);
        }
    }
?>
<div class="imgs" id="imgs">
<script src="./src/js/home.js"></script>
<?php
         // check if there is any user images in db and show them!
        $sql = "SELECT photo FROM users_imgs WHERE username = ? ORDER BY id DESC";
        $qr = $db->prepare($sql);
        $qr->execute([ $_SESSION['username'] ]);
        while ($a = $qr->fetch()) {
            ?>
            <div>
                <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
                    <img src="<?php echo $a["photo"]; ?>">
                    <input type="hidden" name="img" value="<?php echo $a["photo"]; ?>">
                    <input type="submit" name="delete" value="X">
                </form>
            </div>
            <?php
        }
        ?>
</div>
