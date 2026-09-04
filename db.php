<?php
// Code for interaction with the database.

// For local testing:
//$dbh = new PDO("mysql:host=localhost;dbname=habits", "root", "db");

$dbh = new PDO(getenv("DB_URL"), getenv("DB_USER"), getenv("DB_PASSWORD"));
$stmt = $dbh->prepare("CREATE TABLE IF NOT EXISTS users (
	id INT AUTO_INCREMENT PRIMARY KEY,
	username TEXT,
	password_hash TEXT
)");
$stmt->execute();

$stmt = $dbh->prepare("CREATE TABLE IF NOT EXISTS habits (
	id INT AUTO_INCREMENT PRIMARY KEY,
	description TEXT,
	user_id INT,
	FOREIGN KEY (user_id) REFERENCES users (id)
)");
$stmt->execute();

$stmt = $dbh->prepare("CREATE TABLE IF NOT EXISTS habit_entries (
	id INT AUTO_INCREMENT PRIMARY KEY,
	date DATE,
	habit_id INT,
	FOREIGN KEY (habit_id) REFERENCES habits (id) ON DELETE CASCADE
)");
$stmt->execute();

// Models
class User {
	public function __construct($id, $username) {
		$this->id = $id;
		$this->username = $username;
	}
}

class Habit {
	public function __construct($id, $description) {
		$this->id = $id;
		$this->description = $description;
	}
}

class HabitEntry {
	public function __construct($id, $habit_id, $date) {
		$this->id = $id;
		$this->habit_id = $habit_id;
		$this->date = $date;
	}
}

// Users
function add_user($username, $password) {
	global $dbh;
	
	$stmt = $dbh->prepare("SELECT * FROM users WHERE username = :username");
	$stmt->bindParam(':username', $username);
	$stmt->execute();
	$stmt->fetchAll();
	$count = $stmt->rowCount();

	if ($count > 0) {
		throw new Exception("User already exists");
	}

	$password_hash = password_hash($password, PASSWORD_DEFAULT);
	$stmt = $dbh->prepare("INSERT INTO users (username, password_hash) VALUES(
		:username,
		:password_hash
	)");
	$stmt->bindParam(':username', $username);
	$stmt->bindParam(':password_hash', $password_hash);
	$stmt->execute();
	return get_user($username);
}

function get_users() {
	global $dbh;
	$stmt = $dbh->prepare("SELECT * FROM users");
	$stmt->execute();
	$res = $stmt->fetchAll();
	return $res;
}

function get_user($username) {
	global $dbh;
	$stmt = $dbh->prepare("SELECT * FROM users WHERE username = :username");
	$stmt->bindParam(':username', $username);
	$stmt->execute();
	$res = $stmt->fetch();
	$user = new User($res['id'], $res['username']);
	return $user;
}

function login($username, $password) {
	global $dbh;
	$stmt = $dbh->prepare("SELECT * FROM users WHERE username = :username");
	$stmt->bindParam(':username', $username);
	$stmt->execute();
	$res = $stmt->fetch();
	
	if (!$res) {
		throw new Exception('Incorrect login');
	}
	if (password_verify($password, $res['password_hash'])) {
		$user = new User($res['id'], $res['username']);
		return $user;
	} else {
		throw new Exception('Incorrect password');
	}
}

// Habits
function add_habit($description, $user_id) {
	global $dbh;
	
	$stmt = $dbh->prepare("SELECT * FROM habits WHERE description = :description AND user_id = :user_id ");
	$stmt->bindParam(':description', $description);
	$stmt->bindParam(':user_id', $user_id);
	$stmt->execute();
	$stmt->fetchAll();
	$count = $stmt->rowCount();

	if ($count > 0) {
		throw new Exception("Habit already exists");
	}

	$stmt = $dbh->prepare("INSERT INTO habits (description, user_id) VALUES (:description, :user_id)");
	$stmt->bindParam(':description', $description);
	$stmt->bindParam(':user_id', $user_id);
	$stmt->execute();

	return get_habit($description);
}

function get_habits($user_id) {
	global $dbh;
	$stmt = $dbh->prepare("SELECT * FROM habits WHERE user_id = :user_id");
	$stmt->bindParam(':user_id', $user_id);
	$stmt->execute();
	$res = $stmt->fetchAll();

	$habits = array();

	foreach ($res as $habit) {
		$habits[] = new Habit($habit['id'], $habit['description']);
	}

	return $habits;
}

function get_habit($id) {
	global $dbh;
	$stmt = $dbh->prepare("SELECT * FROM habits WHERE id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$res = $stmt->fetch();
	$habit = new Habit($res['id'], $res['description']);
	return $habit;
}

function delete_habit($id) {
	global $dbh;
	$stmt = $dbh->prepare("DELETE FROM habits WHERE id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
}

// Habit entries
function add_habit_entry($date, $habit_id) {
	global $dbh;
	
	$stmt = $dbh->prepare("SELECT * FROM habit_entries WHERE date = :date AND habit_id = :habit_id");
	$stmt->bindParam(':date', $date);
	$stmt->bindParam(':habit_id', $habit_id);
	$stmt->execute();
	$stmt->fetchAll();
	$count = $stmt->rowCount();

	if ($count > 0) {
		throw new Exception("Habit entry already exists");
	}

	$stmt = $dbh->prepare("INSERT INTO habit_entries (date, habit_id) VALUES (:date, :habit_id)");
	$stmt->bindParam(':date', $date);
	$stmt->bindParam(':habit_id', $habit_id);
	$stmt->execute();

	return get_habit_entry($habit_entry_id);
}

function get_habit_entries($habit_id) {
	global $dbh;
	$stmt = $dbh->prepare("SELECT * FROM habit_entries WHERE habit_id = :habit_id");
	$stmt->bindParam(':habit_id', $habit_id);
	$stmt->execute();
	$res = $stmt->fetchAll();

	$habit_entries = array();

	foreach ($res as $habit_entry) {
		$habit_entries[] = new HabitEntry($habit_entry['id'], $habit_entry['habit_id'], $habit_entry['date']);
	}

	return $habit_entries;
}

function get_habit_entry($id) {
	global $dbh;
	$stmt = $dbh->prepare("SELECT * FROM habit_entries WHERE id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$res = $stmt->fetch();

	if (!$res) {
		return null;
	}

	$habit_entry = new HabitEntry($res['id'], $res['habit_id'], $res['date']);
	return $habit_entry;
}

function delete_habit_entry($id) {
	global $dbh;
	$stmt = $dbh->prepare("DELETE FROM habit_entries WHERE id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
}
?>
