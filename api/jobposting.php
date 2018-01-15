<?php

$link = mysqli_connect('localhost', 'redacted', 'redacted', 'catchit') or die('Could not connect: ' . mysql_error()); #username for mysql database, password for stated username (database named catchit)

mysqli_select_db($link, 'catchit') or die('Could not select database');

//get data from form
$user = $_POST['id'];
$company = $_POST["company"];
$location = $_POST["location"];
$title = $_POST["jobtitle"];
$deadline = $_POST["deadline"];
$source = $_POST["source"];
$details = $_POST["details"];
$url = $_POST["url"];

$stage = 1;

//create and execute the insert
$stmt = mysqli_prepare($link, "INSERT INTO JobPosting (JobTitle, Location, Company, Deadline, URL, Source, Details, UserID, Stage) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);");
mysqli_stmt_bind_param($stmt, "sssssssii", $title, $location, $company, $deadline, $url, $source, $details, $user, $stage);
mysqli_stmt_execute($stmt);
if(mysqli_affected_rows($link) == 1) {
  echo "success";
} else {
  echo 'Failed JobPosting insert.';
  echo mysqli_error($link);
}

mysqli_close($link);
?>

