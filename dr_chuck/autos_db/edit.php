<?php
session_start();

if (!isset($_SESSION['email'])) {
    die('Not logged in');
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
    <p>edit page</p>
</body>
</html>
