<?php
function error_message() {
    if (isset($_SESSION['error_message'])) {
        $error_message = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
        echo "<p style=\"color: red;\">$error_message</p>";
    }
}

function success_message() {
    if (isset($_SESSION['success_message'])) {
        $success_message = $_SESSION['success_message'];
        echo("<p style=\"color: green;\">$success_message</p>");
        unset($_SESSION['success_message']);
    }
}

function check_password($password) {
    $salt = 'XyZzy12*_';
    $stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';
    return hash('md5', $salt.$password) === $stored_hash;
}

?>
