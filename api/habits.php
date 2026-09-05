<?php
if (!isset($_SESSION)) {
	session_start();
}
include('db.php');
$habits = get_habits($_SESSION['user_id']);
?>
<!DOCTYPE html>

<html>
<head>
<?php include('head.php') ?>
</head>
<body>
<?php include('navbar.php') ?>
<?php if(isset($_SESSION['user_id'])): ?>
	<a href="/add-habit.php">Add habit</a>
	<ul>
	<?php foreach($habits as $habit): ?>
		<li><a href=<?="/habit.php/$habit->id"?>><?=$habit->description?></a></li>
	<?php endforeach ?>
	</ul>
<?php endif ?>
</body>
</html>
