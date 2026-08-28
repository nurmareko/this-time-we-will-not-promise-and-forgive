<?php
require_once 'pdo.php';
session_start();

if(!isset($_SESSION['user_id']) ) {
    die('ACCESS DENIED');
} else if (!isset($_GET['term'])) {
    die('Missing required parameter');
}

$stmt = $pdo->prepare('SELECT * FROM Institution WHERE name LIKE :term');
$stmt->execute(['term' => $_GET['term'] . '%']);
$institutions = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('content-type:application/json');
echo(json_encode($institutions));

?>
