<?php
session_start();


function display_page_content() {
    if (!isset($_SESSION['email'])) {
        echo('<a href="login.php">Please Log In</a>');
    } else {
        echo('<a href="add.php">Add New Entry</a><br>');
        echo('<a href="logout.php">Logout</a>');
    }
}
?>

<!----------------------------------------------------------------------------->

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Drestayumna Nurmareko</title>
</head>
<body>
    <h1>Welcome to Autos Database</h1>
    <?php display_page_content() ?>
</body>
</html>
