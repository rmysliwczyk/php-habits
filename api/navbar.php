<?php
if (!isset($_SESSION)) {
	session_start();
}
?>
<header>
	<?php if(isset($_SESSION['user_id'])): ?>
	<a href="/habits.php">Habits</a>
	<a href="/logout.php">Logout</a>
	<?php else: ?>
	<a href="/login.php">Login</a>
	<a href="/register.php">Register</a>
	<?php endif ?>
</header>
