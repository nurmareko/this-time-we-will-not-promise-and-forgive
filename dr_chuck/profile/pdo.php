<?php
    $dsn = 'mysql:host=localhost;port=3306;dbname=misc';
    $username = 'dev';
    $password = 'dev';

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        die('sorry we cant connect to our database at the moment');
    }


?>
