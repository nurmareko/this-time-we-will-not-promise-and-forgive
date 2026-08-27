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
?>
