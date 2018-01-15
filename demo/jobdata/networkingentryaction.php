<?php //networkingentryaction.php; form action file for newnetworking.php, enters networking event data into catchit.NetworkingEvent ?>
<?php 
session_start(); //resume existing session

$link = mysqli_connect('localhost', 'redacted', 'redacted', 'catchit') or die('Could not connect: ' . mysql_error()); #username for mysql database, password for stated username (database named catchit)
mysqli_select_db($link, 'catchit') or die('Could not select database');

//get data from networking event form
$details = $_POST['details'];
$contact = $_POST['contact'];
$event = $_POST['event'];
$date = $_POST['date'];
$jobid = $_POST['jobid'];
$action = $_POST['action'];
$eventid = $_POST['eventid'];

$userid = $_SESSION['id'];

//create and execute insert into NetworkingEvent
if($action == "insert"){
	   $stmt = mysqli_prepare($link, "INSERT INTO NetworkingEvent (JobID, UserID, EventName, ContactName, Details, Date) VALUES (?, ?, ?, ?, ?, ?)");
	   mysqli_stmt_bind_param($stmt, "iissss", $jobid, $userid, $event, $contact, $details, $date);
}
else if($action == "edit"){
     $stmt = mysqli_prepare($link, "UPDATE NetworkingEvent SET EventName=?, ContactName=?, Details=?, Date=? WHERE JobID=? and UserID=? and EventID=?");
     mysqli_stmt_bind_param($stmt, "ssssiii", $event, $contact, $details, $date, $jobid, $userid, $eventid);
}
mysqli_stmt_execute($stmt);

if (mysqli_affected_rows($link) == 1) { //exactly one row in NetworkingEvent was affected by the insert
	header('Location: ../dashboard.php');
} else {
	echo('Failed insert into NetworkingEvent:');
	echo mysqli_error($link);
}

?>
