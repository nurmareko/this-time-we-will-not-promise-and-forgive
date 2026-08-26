<?php
function error_message() {
    if (isset($_SESSION['error_message'])) {
        $error_message = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
        echo "<p style=\"color: red;\">$error_message</p>";
    }
}

?>
