<?php 

define('DB_HOST', 'localhost');
define('DB_NAME', 'avis');
define('DB_USER', 'root');
define('DB_PASSWORD', '');


    $db = new PDO('mysql:host='.DB_HOST.';port=3307;dbname='.DB_NAME, DB_USER, DB_PASSWORD, [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]); 

?>