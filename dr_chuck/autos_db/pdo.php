<?php

$dsn = 'mysql:host=localhost;port=3306;dbname=misc';
$username = 'dev';
$password = 'dev';

$pdo = new PDO($dsn, $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>
