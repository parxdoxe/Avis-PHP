<?php

include 'config/db.php';
session_start();

if (isset($_GET['logout'])) {
    unset($_SESSION['name']);
}

if (!$_SESSION['username']) {
    header('Location: avis.php');
}

?>