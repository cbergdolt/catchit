<?php
$link = mysqli_connect('localhost', 'redacted', 'redacted', 'catchit') or die('Could not connect: ' . mysql_error()); #username for mysql database, password for stated username (database named catchit)
echo 'Successful connection';
echo '<br>';
mysqli_select_db($link, 'catchit') or die('Could not select database');

//get data from post and hash password
$name = $_POST["name"];
$email = $_POST["email"];
$psw_hash = password_hash($_POST["psw"], PASSWORD_DEFAULT);

//create and execute the insert
$stmt = mysqli_prepare($link, "INSERT INTO User (Email, Name, Password) VALUES (?, ?, ?);");
mysqli_stmt_bind_param($stmt, "sss", $email, $name, $psw_hash);
mysqli_stmt_execute($stmt);

$id = mysqli_insert_id($link);

//check affect rosws to see if login was successful
if(mysqli_affected_rows($link) == 1) {
  echo('Successful insert.');
  session_start();
  $_SESSION['email'] = $email;
  $_SESSION['id'] = $id;
  header('Location: dashboard.php');
} else {
  echo "<script type='text/javascript'>alert('User already exists');</script>";
    echo "<script>
             window.history.go(-1);
     </script>";
}
				 
mysqli_close($link);
?>
