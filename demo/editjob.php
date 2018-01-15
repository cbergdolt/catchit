<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit Job Posting</title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://bootswatch.com/3/flatly/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  </head>

<?php
$link = mysqli_connect('localhost', 'redacted', 'redacted', 'catchit') or die('Could not connect: ' . mysql_error()); #username for mysql database, password for stated username (database named catchit)
mysqli_select_db($link, 'catchit') or die('Could not select database');

$editstmt = mysqli_prepare($link, "SELECT Company, Location, JobTitle, Deadline, Stage, Details, URL FROM JobPosting WHERE UserID = ? and JobID = ?");
$stagestmt = mysqli_prepare($link, "SELECT StageID, Description from Stage");
$deletestmt = mysqli_prepare($link, "DELETE FROM JobPosting WHERE UserID = ? and JobID = ?");


$deleteEmailstmt = mysqli_prepare($link, "DELETE FROM Email WHERE JobID = ?");
$deleteInterviewstmt = mysqli_prepare($link, "DELETE FROM Interview WHERE JobID = ?");
$deleteNetworkingEventstmt = mysqli_prepare($link, "DELETE FROM NetworkingEvent WHERE JobID = ?");
$deleteNotestmt = mysqli_prepare($link, "DELETE FROM Note WHERE JobID = ?");

//$deleteEmailstmt = mysqli_prepare($link, "DELETE FROM Email WHERE JobID = ?; DELETE FROM Interview WHERE JobID = ?; DELETE FROM NetworkingEvent WHERE JobID = ?; DELETE FROM Note WHERE JobID = ?");


if ($_POST['action'] && $_POST['userid'] && $_POST['jobid']) {
	$userid = $_POST['userid'];
	$jobid = $_POST['jobid'];
	if ($_POST['action'] == 'Delete') {
		mysqli_stmt_bind_param($deletestmt, "ii", $userid, $jobid);
		mysqli_stmt_execute($deletestmt);
                //check affected rows to see if login was successful
                if(mysqli_affected_rows($link) == 1) {
                        echo('Successful UserJobOfInterest deletion.');
                      header('Location: dashboard.php');
                } else {
                        echo "Failed UserJobOfInterest deletion.";
                        echo mysqli_error($link);
                }
                
                mysqli_stmt_bind_param($deleteEmailstmt, "i", $jobid);
                mysqli_stmt_execute($deleteEmailstmt);

                mysqli_stmt_bind_param($deleteInterviewstmt, "i", $jobid);
                mysqli_stmt_execute($deleteInterviewstmt);

                mysqli_stmt_bind_param($deleteNetworkingEventstmt, "i", $jobid);
                mysqli_stmt_execute($deleteNetworkingEventstmt);

                mysqli_stmt_bind_param($deleteNotestmt, "i", $jobid);
                mysqli_stmt_execute($deleteNotestmt);



	}
	else if ($_POST['action'] == 'Edit') {?>
		<?php
		mysqli_stmt_bind_param($editstmt, "ii", $userid, $jobid);
		mysqli_stmt_execute($editstmt);
		//get current job posting details
		if ($result = mysqli_stmt_get_result($editstmt)) {
			//given the nature of JobPosting and the query, there should only be one result, 
			//but just in case, there's this while loop.
			//the final values of each attribute will be the values from the last row of the query result
			while ($row = mysqli_fetch_assoc($result)) {
				$title = $row["JobTitle"];
				$company = $row["Company"];
				$location = $row["Location"];
				$deadline = $row["Deadline"];
				$details = $row["Details"];
				$stage = $row["Stage"];
				$url = $row["URL"];
			}
			mysqli_free_result($result);
		}

		//get stage text Descriptions
		mysqli_stmt_execute($stagestmt);
		if ($result = mysqli_stmt_get_result($stagestmt)) {
			$all_stages = array();
			while ($row = mysqli_fetch_assoc($result)) {
				$all_stages[$row["StageID"]] = $row["Description"];
			}
			mysqli_free_result($result);
		}
		

		// create a pre-filled form for editing job posting information ?>
	        <body>
        	        <div style="padding:70px 0">
                	<div class="container col-lg-6 col-lg-offset-3">
	                <h2 class="text-center">Update Job Details</h2>

			<form method="post" action="dashboard.php">
			<label for="noteBtnGrp">Add a new note or correspondence to this job:</label>
			<input type="hidden" name="jobid" value="<?php echo $jobid?>"/>
			<div class="btn-group-sm" id="noteBtnGrp">
				<button type="submit" class="btn btn-default" formaction="jobdata/newnote.php">Note</button>
				<button type="submit" class="btn btn-default" formaction="jobdata/newemail.php">Email</button>
				<button type="submit" class="btn btn-default" formaction="jobdata/newinterview.php">Interview</button>
				<button type="submit" class="btn btn-default" formaction="jobdata/newnetworking.php">Networking Event</button>
			</div>
			</form>

	                <form method="post" action="jobupdateaction.php">
	                        <div class="form-group">
	                        <label for="inputCompany">Company:</label>
	                        <input type="text" class="form-control" id="inputCompany" name="company" value="<?php echo $company?>">
	                        </div>
	                        <div class="form-group">
	                                <label for="inputLocation">Location:</label>
	                                <input type="text" class="form-control" id="inputLocation" name="location" value="<?php echo $location?>">
	                        </div>
	                        <div class="form-group">
	                                <label for="inputTitle">Job Title:</label>
	                                <input type="text" class="form-control" id="inputTitle" name="jobtitle" value="<?php echo $title?>">
	                        </div>
	                        <div class="form-group">
	                                <label for="inputDeadline">Application Deadline:</label>
	                                <input type="date" class="form-control" id="inputDeadline" name="deadline" value="<?php echo $deadline?>">
	                        </div>
				<div class="form-group">
					<label for="inputURL">URL:</label>
					<input type="text" class="form-control" id="inputURL" name="url" value="<?php echo $url?>">
				</div>
	                        <div class="form-group">
	                                <label for="inputOther">Other Details:</label>
	                                <textarea type="text" class="form-control" rows="4" id="inputOther" name="details"><?php echo $details?></textarea>
	                        </div>
	                        <div class="form-group">
	                                <label for="inputStage">Stage:</label>
	                                <select type="text" class="form-control" id="inputStage" name="stage">
						<?php foreach($all_stages as $stageid => $stagetext) {
							if ($stageid == $stage) { ?> <option selected="selected" value="<?php echo $stageid?>"> <?php echo $stagetext?> </option> 
							<?php } else { ?> <option value="<?php echo $stageid?>"> <?php echo $stagetext?> </option>
						<?php } } ?>
					</select>
	                        </div>
                          	<input type="hidden" name="jobid" value="<?php echo $jobid?>"/>
	                        <button type="submit" class="btn btn-primary">Submit</button>
				<button type="button" class="btn" onclick="window.location.replace('dashboard.php')">Cancel</button>
	                </form>
	                </div>
	                </div>
	        </body>
</html>
		<?php
	}
}

mysqli_close($link);
?>

