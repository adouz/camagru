<?php
    //A config/setup.php file, capable of creating or re-creating the database schema,
    //by using the info cintained in the file config/database.php.
    require 'database.php';
    $users = "CREATE TABLE IF NOT EXISTS `users` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `username` varchar(32) NOT NULL,
        `password` varchar(64) NOT NULL,
        `email` text NOT NULL,
        `hash` varchar(32) NOT NULL,
        `active` int(1) NOT NULL DEFAULT '0',
        `notification` int(1) DEFAULT '1',
        PRIMARY KEY (`id`)
    )
    ";
    $q = $db->prepare($users);
    $q->execute();

    $imgs_cmt = "CREATE TABLE IF NOT EXISTS `imgs_cmt` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `username` varchar(32) NOT NULL,
        `comment` varchar(150) NOT NULL,
        `photo` varchar(50) NOT NULL,
        PRIMARY KEY (`id`)
    )
    ";
    $q = $db->prepare($imgs_cmt);
    $q->execute();

    $likes = "CREATE TABLE IF NOT EXISTS `likes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `userid` int(11) NOT NULL,
        `photoid` int(11) NOT NULL,
        PRIMARY KEY (`id`)
    )     
    ";
    $q = $db->prepare($likes);
    $q->execute();
    $users_imgs = "CREATE TABLE IF NOT EXISTS `users_imgs` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `username` varchar(32) NOT NULL,
        `photo` varchar(50) NOT NULL,
        `likes` int(11) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`)
    )
    ";
    $q = $db->prepare($users_imgs);
    $q->execute();

    $password_reset = "CREATE TABLE IF NOT EXISTS `password_reset` (
        `email` varchar(250) NOT NULL,
        `key` varchar(250) NOT NULL,
        `expDate` datetime NOT NULL
      );";
    $q = $db->prepare($password_reset);
    $q->execute();

?>