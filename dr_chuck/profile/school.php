<?php
require_once 'pdo.php';
session_start();

if(!isset($_SESSION['user_id']) ) {
    die('ACCESS DENIED');
} else if (!isset($_GET['term'])) {
    die('Missing required parameter');
}

?>
