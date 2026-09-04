<?php
	session_start();
include('db.php');
$error_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = $_POST['username'];
	$password = $_POST['password'];
	try {
		$user = login($username, $password);
		$_SESSION['user_id'] = $user->id;
		$_SESSION['username'] = $user->username;
		header("Location: /habits.php");
	} catch (Exception $e) {
		$error_message = $e->getMessage();
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
	<?php include('head.php') ?>
	</head>
	<body>
		<?php include('navbar.php') ?>
		<form method="POST" autocomplete="off">
			<label for="username">Username</label>
			<input type="text" id="username" name="username"/>
			<label for="password">Password</label>
			<input type="password" id="password" name="password" default=""/>
			<button type="submit">Login</button>
		</form>
		<div id="error-message"><?=$error_message?></div>
	</body>
</html>
