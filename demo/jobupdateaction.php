<?php	//jobupdateaction.php, used to edit existing job posting

session_start(); //resume existing session

$link = mysqli_connect('localhost', 'redacted', 'redacted', 'catchit') or die('Could not connect: ' . mysql_error()); #username for mysql database, password for stated username (database named catchit)
echo 'Successful connection';
echo '<br>';
mysqli_select_db($link, 'catchit') or die('Could not select database');

//get data from form
$company = $_POST["company"];
$location = $_POST["location"];
$title = $_POST["jobtitle"];
$deadline = $_POST["deadline"];
$url = $_POST["url"];
$details = $_POST["details"];
$stage = $_POST["stage"];
$jobid = $_POST["jobid"];

$user = $_SESSION['id'];

//create and execute the update
$stmt = mysqli_prepare($link, "UPDATE JobPosting SET JobTitle=?, Location=?, Company=?, Deadline=?, URL=?, Details=?, Stage=? WHERE JobID=? and UserID=?;");
mysqli_stmt_bind_param($stmt, "ssssssiii", $title, $location, $company, $deadline, $url, $details, $stage, $jobid, $user);
mysqli_stmt_execute($stmt);
if(mysqli_affected_rows($link) == 1) {
	echo('Successful JobPosting insert.');
	header('Location: dashboard.php');
} else {
	echo 'Note- nothing changed.';
	header('Location: dashboard.php');

}

mysqli_close($link);
?>

