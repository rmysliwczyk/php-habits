<?php
session_start();
include('db.php');
$matches = [];
preg_match("/\/(\d*)[\/]*/", $_SERVER['PATH_INFO'], $matches);
$habit_id = $matches[1];
$habit = get_habit($habit_id);

if (isset($_REQUEST['date'])) {
	$start_date = new DateTimeImmutable($_REQUEST['date']);
} else {
	$start_date = new DateTimeImmutable();
}
$start_date = new DateTimeImmutable($start_date->format("Y-m"));
$end_date = $start_date->add(new DateInterval('P1M'));
$days = $start_date->diff($end_date)->days;
$period = new DatePeriod($start_date, new DateInterval('P1D'), $end_date);

$habit_entries = get_habit_entries($habit_id);

$date_to_entry = [];
foreach ($habit_entries as $entry) {
	$date_to_entry[$entry->date] = $entry;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['date'])){

		$habit_entry = get_habit_entry($date_to_entry[$_POST['date']]->id);
		if ($habit_entry === null) {
			add_habit_entry($_POST['date'], $habit_id);
		} else {
			delete_habit_entry($habit_entry->id);
		}

		$start_date_string = $start_date->format("Y-m-d");
		header("Location: /habit.php/$habit_id?date=$start_date_string");
	} elseif (isset($_POST['delete'])) {
		delete_habit($habit_id);
		header("Location: /habits.php");
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
<?php if(isset($_SESSION['user_id'])): ?>
	<div class='habit-meta'>
		<h3>Habit: <?=$habit->description?></h3>
		<form method="GET">
			<div>
			<select id="month-select" name="month">
				<?php foreach((new DatePeriod(new DateTime("1970-01"), new DateInterval('P1M'), new DateTime("1971-01"))) as $month): ?>
					<option <?php echo $month->format("m") == $start_date->format("m") ? "selected" : "" ?>><?php echo $month->format("m") ?></option>
				<?php endforeach ?>
			</select>
			<select id="year-select" name="year">
				<?php foreach((new DatePeriod(new DateTime("2025-01"), new DateInterval('P1Y'), new DateTime("2027-01"))) as $year): ?>
					<option <?php echo $year->format("Y") == $start_date->format("Y") ? "selected" : "" ?>><?php echo $year->format("Y") ?></option>
				<?php endforeach ?>
			</select>
			</div>
		</form>
		<form method="POST">
			<input type="hidden" name="delete" value=<?=$habit->id?> />
			<button type="submit">Delete habit</button>
		</form>
	</div>
	<section id="habit-squares">
	<?php foreach($period as $date): ?>
	<form method="POST" action=<?php echo "/habit.php/$habit_id" ?> >
	<input type="hidden" name="date" value=<?=$date->format("Y-m-d")?> />
	<button type="submit" class="habit-square" <?php echo $date_to_entry[$date->format("Y-m-d")] ? "data-completed" : "" ?> ><?=$date->format("m-d")?></button>
	</form>
	<?php endforeach ?>
	</ul>
	</section>
<?php endif ?>
<script>
let monthSelect = document.querySelector("#month-select");
let yearSelect = document.querySelector("#year-select");
document.addEventListener("change", function(event) {
	window.location.assign(`?date=${yearSelect.value}-${monthSelect.value}`);
})
</script>
</body>
</html>
