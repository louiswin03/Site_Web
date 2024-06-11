<?php
session_start();


$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'fr';


session_unset();
session_destroy();


$_SESSION['lang'] = $lang;

header("Location: accueil.php");
exit();
?>