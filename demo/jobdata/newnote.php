<?php //jobdata/newnote.php; collects data to put in new note?>
<?php
session_start();
$userid = $_SESSION['id'];

$link = mysqli_connect('localhost', 'redacted', 'redacted', 'catchit') or die('Could not connect: ' . mysql_error()); #username for mysql database, password for stated username (database named catchit)
mysqli_select_db($link, 'catchit') or die('Could not select database')

?>
<html>
        <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>New Note</title>
            <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://bootswatch.com/3/flatly/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</head>

<?php
if ($_POST['jobid']) {
	$jobid = $_POST['jobid'];
	$job = "job not found";
	$company = "company not found";

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
                <h2 class="text-center">New Note</h2>

		<p>This note is associated with <?php echo $job?> at <?php echo $company?>.</p>

                <form method="post" action="noteentryaction.php">
                        <div class="form-group">
                        	<label for="inputNote">Note Text:</label>
                        	<textarea type="text" rows="6" class="form-control" id="inputNote" name="notetext"></textarea>
                        </div>
			<input type="hidden" name="date" value="<?php echo date('Y-m-d'); ?>">
			<input type="hidden" name="jobid" value="<?php echo $jobid?>">
			<input type="hidden" name="noteid" value="0">
			<input type="hidden" name="action" value="insert">
                        <button type="submit" class="btn btn-primary">Submit</button>
			<button type="button" class="btn" onclick="window.location.replace('../dashboard.php')">Cancel</button>
                </form>

                </div>
                </div>
        </body>
<?php } ?>
</html>
