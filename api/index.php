<?php
if (!isset($_SESSION)) {
	session_start();
}
include('db.php');
?>
<!DOCTYPE html>

<html>
<head>
<?php include('head.php') ?>
</head>
<body>
<?php include('navbar.php') ?>
</body>
</html>
