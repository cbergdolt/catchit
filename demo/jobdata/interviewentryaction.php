<?php //interviewentryaction.php; form action file for newinterview.php, enters interview data into catchit.Interview ?>
<?php 
session_start(); //resume existing session

$link = mysqli_connect('localhost', 'redacted', 'redacted', 'catchit') or die('Could not connect: ' . mysql_error()); #username for mysql database, password for stated username (database named catchit)
mysqli_select_db($link, 'catchit') or die('Could not select database');

//get data from interview form
$notes = $_POST['notes'];
$location = $_POST['location'];
$date = $_POST['date'];
$jobid = $_POST['jobid'];
$action = $_POST['action'];
$intid = $_POST['intid'];

$userid = $_SESSION['id'];

//create and execute insert into Interview
if($action == "insert"){
	   $stmt = mysqli_prepare($link, "INSERT INTO Interview (JobID, UserID, Date, Location, Notes) VALUES (?, ?, ?, ?, ?)");
	   mysqli_stmt_bind_param($stmt, "iisss", $jobid, $userid, $date, $location, $notes);
}
else if($action == "edit"){
     $stmt = mysqli_prepare($link, "UPDATE Interview SET Date=?, Location=?, Notes=? WHERE JobID=? and UserID = ? and InterviewID = ?");
     mysqli_stmt_bind_param($stmt, "sssiii", $date, $location, $notes, $jobid, $userid, $intid);
}
mysqli_stmt_execute($stmt);

if (mysqli_affected_rows($link) == 1) { //exactly one row in Interview was affected by the insert
	header('Location: ../dashboard.php');
} else {
	echo('Failed insert into Interview:');
	echo mysqli_error($link);
}

?>
