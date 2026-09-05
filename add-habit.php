<?php
session_start();
include('db.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$description = $_POST['description'];
	add_habit($description, $_SESSION['user_id']);
	header('Location: /habits.php');
}
?>
<!DOCTYPE html>

<html>
<head>
<?php include('head.php') ?>
</head>
<body>
<?php include('navbar.php') ?>
<?php if(isset($_SESSION['user_id'])): ?>
	<h2>Logged in as <?=$_SESSION['username']?></h2>
	<form method="POST">
		<label for="description">Habit description:</label>
		<input type="text" id="description" name="description"/>
		<button type="submit">Add habit</button>
	</form>
<?php endif ?>
</body>
</html>
