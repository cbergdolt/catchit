<?php //emailentryaction.php; form action file for newemail.php, enters note data into catchit.Email ?>
<?php 
session_start(); //resume existing session

$link = mysqli_connect('localhost', 'redacted', 'redacted', 'catchit') or die('Could not connect: ' . mysql_error()); #username for mysql database, password for stated username (database named catchit)
mysqli_select_db($link, 'catchit') or die('Could not select database');

//get data from email form
$message = $_POST['message'];
$recipient = $_POST['recipient'];
$date = $_POST['date'];
$jobid = $_POST['jobid'];
$action = $_POST['action'];
$emailid = $_POST['emailid'];

$userid = $_SESSION['id'];

//create and execute insert into Email
if ($action == "insert") {
	$stmt = mysqli_prepare($link, "INSERT INTO Email (JobID, UserID, Recipient, Message, Date) VALUES (?, ?, ?, ?, ?)");
	mysqli_stmt_bind_param($stmt, "iisss", $jobid, $userid, $recipient, $message, $date);
}
else if ($action == "edit") {
	$stmt = mysqli_prepare($link, "UPDATE Email SET Recipient=?, Message=?, Date=? WHERE JobID=? and UserID = ? and EmailID = ?");
	mysqli_stmt_bind_param($stmt, "sssiii", $recipient, $message, $date, $jobid, $userid, $emailid);
}
mysqli_stmt_execute($stmt);

if (mysqli_affected_rows($link) == 1) { //exactly one row in Email was affected by the insert
	header('Location: ../dashboard.php');
} else {
	echo('Failed insert into Email:');
	echo mysqli_error($link);
}

?>
