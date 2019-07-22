<?php
    session_start();
    function getName($n) { 
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
        $randomString = ''; 
    
        for ($i = 0; $i < $n; $i++) { 
            $index = rand(0, strlen($characters) - 1); 
            $randomString .= $characters[$index]; 
        }
        return $randomString; 
    }
    require 'config/database.php';
    $user = $_SESSION['username'];
    if (isset($_POST['img'])){
        // convert base64url
        $img = $_POST['img'];
        // if file is empty
        if ($img == "data:,"){
            exit();
        }
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $img = base64_decode($img);
        $dir = 'userspics/'.$user.'/';
        if (!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
        $filename = $dir.getName(10).time().".png";
            file_put_contents($filename, $img);
        if (isset($_POST['img2'])){
            $path = $_POST['img2'];
            //echo $path;
            list($w, $h) = getimagesize($path);
            $img1 = imagecreatefrompng($filename);
            $img2 = imagecreatefrompng($path);
            imagecopy($img1, $img2, 0, 0, 0, 0, $w, $w);
            // image dir
            // save image
            imagepng($img1, $filename);
            // record on db
            $sql = "INSERT INTO users_imgs (username, photo) VALUES (?, ?)";
            $qr = $db->prepare($sql);
            $qr->execute([ $user, $filename ]);
            exit("$filename");
        }else{
            $img1 = imagecreatefrompng('userspics/tmp.png');
            // save image
            imagepng($img1, $filename);
            // record on db
            $sql = "INSERT INTO users_imgs (username, photo) VALUES (?, ?)";
            $qr = $db->prepare($sql);
            $qr->execute([ $user, $filename ]);
            exit("$filename");
        }
    }
?>