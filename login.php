<?php

include 'config/db.php';
session_start();

$name = $_GET['name'] ?? null;

$_SESSION['name'] = $name;

$query = $db->prepare('SELECT * from review WHERE name = :name');
    $query->execute([
      ':name' => $name,
    ]);
    $user = $query->fetch();

if ($name === $user ) {
    header('Location : login.php');  
}

if (!$_SESSION['username']) {
  header('Location: avis.php');
}



?>
