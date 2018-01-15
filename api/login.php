<?php

header('Access-Control-Allow-Origin: *');

$link = mysqli_connect('localhost', 'redacted', 'redacted', 'catchit') or die('Could not connect: ' . mysql_error());	#username for mysql database, password for stated username (database named catchit)
mysqli_select_db($link, 'catchit') or die('Could not select database');

$email = $_POST["email"];
$password = $_POST["psw"];

//prepare and execute query
$stmt = mysqli_prepare($link, "SELECT Email, Password, UserID FROM User WHERE Email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

//get result, check if any user is found, then verify the password
mysqli_stmt_bind_result($stmt, $email, $psw_hash, $id);
if (mysqli_stmt_fetch($stmt)) {
  if (password_verify($password, $psw_hash)) {
    echo $id;
  } else {
    echo "invalid_pass";
  }
} else {
  echo "invalid_email";
}

mysqli_close($link);

?>
