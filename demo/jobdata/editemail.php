<?php //jobdata/editemail.php; collects data to modify existing email tuple
session_start();
$userid = $_SESSION['id'];

$link = mysqli_connect('localhost', 'redacted', 'redacted', 'catchit') or die('Could not connect: ' . mysql_error()); #username for mysql database, password for stated username (database named catchit)
mysqli_select_db($link, 'catchit') or die('Could not select database')

?>
<html>
        <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>New Email</title>
            <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://bootswatch.com/3/flatly/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</head>

<?php
if ($_POST['jobid']) {
	$jobid = $_POST['jobid'];
	$emailid = $_POST['emailid'];
	$action = $_POST['action'];
	$job = "job not found";
        $company = "company not found";

   if ($action == 'delete') {
        $delstmt = mysqli_prepare($link, "DELETE from Email where EmailID = ?");
        mysqli_stmt_bind_param($delstmt, 'i', $emailid);
        mysqli_stmt_execute($delstmt);
        header('Location: ../dashboard.php');

   }

   else if ($action == 'edit') {


	$emailstmt = mysqli_prepare($link, "SELECT Recipient, Message, Date from Email where EmailID = ?");
	mysqli_stmt_bind_param($emailstmt, 'i', $emailid);
	mysqli_stmt_execute($emailstmt);
	if ($emailresult = mysqli_stmt_get_result($emailstmt)) {
		//will use values from last row if contains multiple results (which it shoudln't)
		while($row = mysqli_fetch_assoc($emailresult)) {
			$message = $row['Message'];
			$recipient = $row['Recipient'];
			$date = $row['Date'];
		}
		mysqli_free_result($emailresult);
	}

        $jobstmt = mysqli_prepare($link, "SELECT JobTitle, Company FROM JobPosting WHERE UserID = ? and JobID = ?");
        mysqli_stmt_bind_param($jobstmt, 'ii', $userid, $jobid);
        mysqli_stmt_execute($jobstmt);
        if ($result = mysqli_stmt_get_result($jobstmt)) {
                //there should only be one tuple in the result, but in the case that there are multiple,
                // job and company will be the values of the last tuple in the result
                while($row = mysqli_fetch_assoc($result)) {
                        $job = $row['JobTitle'];
                        $company = $row['Company'];
                }
                mysqli_free_result($result);
        }

?>

        <body>
                <div style="padding:70px 0">
                <div class="container col-lg-6 col-lg-offset-3">
                <h2 class="text-center">New Email Record</h2>

		<p>This email is associated with <?php echo $job?> at <?php echo $company?>.</p>

                <form method="post" action="emailentryaction.php">
			<div class="form-group">
				<label for="inputRecipient">Email Recipient (or Source):</label>
				<input type="text" class="form-control" id="inputRecipient" name="recipient" value="<?php echo $recipient?>">
			</div>
                        <div class="form-group">
                        	<label for="inputMessage">Message Text:</label>
                        	<textarea type="text" rows="6" class="form-control" id="inputMessage" name="message"><?php echo $message ?></textarea>
                        </div>
			<div class="form-group">
				<label for"inputDate">Email Date:</label>
				<input type="date" class="form-control" id = "inputDate" name="date" value="<?php echo $date ?>">
			</div>
			<input type="hidden" name="jobid" value="<?=$jobid?>">
			<input type="hidden" name="emailid" value="<?=$emailid?>">
			<input type="hidden" name="action" value="edit">
                        <button type="submit" class="btn btn-primary">Submit</button>
			<button type="button" class="btn" onclick="window.location.replace('../dashboard.php')">Cancel</button>
                </form>

                </div>
                </div>
        </body>
<?php } }?>
</html>
