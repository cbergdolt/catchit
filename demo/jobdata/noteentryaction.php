<?php //noteentryaction.php; form action file for newnote.php, enters note data into catchit.Note ?>
<?php 
session_start(); //resume existing session

$link = mysqli_connect('localhost', 'redacted', 'redacted', 'catchit') or die('Could not connect: ' . mysql_error()); #username for mysql database, password for stated username (database named catchit)
mysqli_select_db($link, 'catchit') or die('Could not select database');

//get data from note form
$notetext = $_POST['notetext'];
$date = $_POST['date'];
$jobid = $_POST['jobid'];
$action = $_POST['action'];
$noteid = $_POST['noteid'];

$userid = $_SESSION['id'];

//create and execute insert into Note
if($action == "insert"){
   $stmt = mysqli_prepare($link, "INSERT INTO Note (JobID, UserID, Content, Date) VALUES (?, ?, ?, ?)");
   mysqli_stmt_bind_param($stmt, "iiss", $jobid, $userid, $notetext, $date);
}
else if($action == "edit"){
   $stmt = mysqli_prepare($link, "UPDATE Note SET Content=?, Date=? WHERE JobID=? and UserID=? and NoteID=?");
   mysqli_stmt_bind_param($stmt, "ssiii", $notetext, $date, $jobid, $userid, $noteid);
}
mysqli_stmt_execute($stmt);

if (mysqli_affected_rows($link) == 1) { //exactly one row in Note was affected by the insert
	header('Location: ../dashboard.php');
} else {
	echo('Failed insert into Note:');
	echo mysqli_error($link);
}

?>
