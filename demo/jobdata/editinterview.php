<?php //jobdata/editinterview.php; collects data to modify existing interview tuple
session_start();
$userid = $_SESSION['id'];

$link = mysqli_connect('localhost', 'redacted', 'redacted', 'catchit') or die('Could not connect: ' . mysql_error()); #username for mysql database, password for stated username (database named catchit)
mysqli_select_db($link, 'catchit') or die('Could not select database')
?>

<html>
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>New Interview</title>
	<link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://bootswatch.com/3/flatly/bootstrap.css">
 	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</head>

<?php
if ($_POST['jobid']) {
   $jobid = $_POST['jobid'];
   $intid = $_POST['iid'];
   $action = $_POST['action'];
   $job = "job not found";
   $company = "company not found";

   if ($action == 'delete') {
        $delstmt = mysqli_prepare($link, "DELETE from Interview where InterviewID = ?");
        mysqli_stmt_bind_param($delstmt, 'i', $intid);
        mysqli_stmt_execute($delstmt);
        header('Location: ../dashboard.php');

   }

   else if ($action == 'edit') {

   $intstmt = mysqli_prepare($link, "SELECT Date, Location, Notes from Interview where InterviewID = ?");
   mysqli_stmt_bind_param($intstmt, 'i', $intid);
   mysqli_stmt_execute($intstmt);
   if ($intresult = mysqli_stmt_get_result($intstmt)){
      while($row = mysqli_fetch_assoc($intresult)){
      	$date = $row['Date'];
	$location = $row['Location'];
	$notes = $row['Notes'];
      }
      mysqli_free_result($intresult);
   }
   $jobstmt = mysqli_prepare($link, "SELECT JobTitle, Company FROM JobPosting WHERE UserID = ? and JobID = ?");
   mysqli_stmt_bind_param($jobstmt, 'ii', $userid, $jobid);
   mysqli_stmt_execute($jobstmt);
   if ($result = mysqli_stmt_get_result($jobstmt)) {
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
	<h2 class="text-center">New Interview</h2>
	<p>This interview is for <?php echo $job?> with <?php echo $company?>.</p>
	<form method="post" action="interviewentryaction.php">
	      <div class="form-group">
	      	   <label for="inputDate">Interview Date:</label>
		    <input type="date" class="form-control" id = "inputDate" name="date" value="<?php echo $date ?>">
	      </div>
	      <div class="form-group">
	      	   <label for="inputLocation">Interview Location:</label>
              	   <input type="text" class="form-control" id="inputLocation" name="location" value="<?php echo $location?>">
              </div>
              <div class="form-group">
	      <label for="inputNotes">Notes about the Interview:</label>
	      <textarea type="text" rows="6" class="form-control" id="inputNotes" name="notes"><?php echo $notes?></textarea>
	      </div> 
	      <input type="hidden" name="jobid" value="<?=$jobid?>">
	      <input type="hidden" name="intid" value="<?=$intid?>">
	      <input type="hidden" name="action" value="edit">
              <button type="submit" class="btn btn-primary">Submit</button>
	      <button type="button" class="btn" onclick="window.location.replace('../dashboard.php')">Cancel</button>
	      </form>
	      </div>
	      </div>
	</body>
	<?php } }?>
</html>

