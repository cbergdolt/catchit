<?php
$link = mysqli_connect('localhost', 'redacted', 'redacted', 'catchit') or die('Could not connect: ' . mysql_error()); #username for mysql database, password for stated username (database named catchit)
//echo 'Successful connection';
//echo '<br>';
mysqli_select_db($link, 'catchit') or die('Could not select database');

$email = $_POST["email"];
$password = $_POST["psw"];

//prepare and execute query
$stmt = mysqli_prepare($link, "SELECT Email, Password, UserID FROM User WHERE Email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

//get result, check if any user is found, then verify the password
mysqli_stmt_bind_result($stmt, $Email, $psw_hash, $id);
if (mysqli_stmt_fetch($stmt)) {
  echo "$psw_hash";
  if (password_verify($password, $psw_hash)) {   
    session_start();
    $_SESSION['email'] = $email;
    $_SESSION['id'] = $id;
    header('Location: dashboard.php');
  } else {
    echo "<script type='text/javascript'>alert('Incorrect username or password');</script>";
    echo "<script>window.history.go(-1);</script>;";
  }   
} else {
  echo "<script type='text/javascript'>alert('Incorrect username or password');</script>";
    echo "<script>
             window.history.go(-1);
     </script>";
}

mysqli_close($link);
?>
