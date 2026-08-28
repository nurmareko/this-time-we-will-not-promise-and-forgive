<?php
require_once 'pdo.php';
session_start();

if(!isset($_SESSION['user_id']) ) {
    die('ACCESS DENIED');
} else if (!isset($_GET['term'])) {
    die('Missing required parameter');
}

$stmt = $pdo->prepare('SELECT name FROM Institution WHERE name LIKE :term');
$stmt->execute(['term' => $_GET['term'] . '%']);

$institutions = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $institutions[] = $row['name'];
}

header('Content-Type: application/json');
echo json_encode($institutions, JSON_PRETTY_PRINT);
