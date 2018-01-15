<?php
//jobentryaction.php
session_start(); //resume existing session

$link = mysqli_connect('localhost', 'redacted', 'redacted', 'catchit') or die('Could not connect: ' . mysql_error()); #username for mysql database, password for stated username (database named catchit)
echo 'Successful connection';
echo '<br>';
mysqli_select_db($link, 'catchit') or die('Could not select database');

//get data from form
//$id = $_SESSION['id'];
$company = $_POST["company"];
$location = $_POST["location"];
$title = $_POST["jobtitle"];
$deadline = $_POST["deadline"];
$source = $_POST["source"];
$details = $_POST["details"];
$url = $_POST["url"];

$user = $_SESSION['id'];
$stage = 1;

//create and execute the insert
$stmt = mysqli_prepare($link, "INSERT INTO JobPosting (JobTitle, Location, Company, Deadline, URL, Source, Details, UserID, Stage) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);");
mysqli_stmt_bind_param($stmt, "sssssssii", $title, $location, $company, $deadline, $url, $source, $details, $user, $stage);
mysqli_stmt_execute($stmt);
if(mysqli_affected_rows($link) == 1) {
  echo('Successful GeneralJobPosting insert.');
} else {
  echo 'Failed GeneralJobPosting insert.';
 echo mysqli_error($link);

}

//check affect rosws to see if login was successful
if(mysqli_affected_rows($link) == 1) {
  echo('Successful UserJobOfInterest insert.');
  header('Location: dashboard.php');
} else {
  echo "Failed UserJobOfInterest insert.";
	echo '<br>', "userid = ", $user;
	echo '<br>', "jobid = ", $job;
	echo '<br>';
	
  echo mysqli_error($link);
}

mysqli_close($link);
?>

