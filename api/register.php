<?php
	if (!isset($_SESSION)) {
		session_start();
	}
	include('db.php');
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$username = $_POST['username'];
		$password = $_POST['password'];
		if($username && $password) {
			try {
				$user = add_user($username, $password);
				$_SESSION['user_id'] = $user->id;
				$_SESSION['username'] = $user->username;
				header("Location: /");
			} catch (Exception $e) {
				$error_message = $e->getMessage();
			}
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
		<form method="POST">
			<label for="username">Username</label>
			<input type="text" id="username" name="username"/>
			<label for="password">Password</label>
			<input type="password" id="password" name="password"/>
			<button type="submit">Register</button>
		</form>
		<div id="error-message"><?=$error_message?></div>
	</body>
</html>
