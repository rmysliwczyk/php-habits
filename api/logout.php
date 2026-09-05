<?php
if (!isset($_SESSION)) {
	session_start();
}
foreach ($_SESSION as $key => $value) {
	unset($_SESSION[$key]);
}
header('Location: /');
?>
