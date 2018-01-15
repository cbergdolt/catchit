<html>
  <head>
    <title>Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://bootswatch.com/3/flatly/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="sortFunctions.js"></script>
    <script src="https://raw.githubusercontent.com/mistic100/Bootstrap-Confirmation/master/bootstrap-confirmation.min.js"></script>
    <script>
      window.foo = function(e) {
      e.stopPropagation();
      }
    </script>
  </head>
  <div class = "container">
  <body>
</nav>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">CatchIt</a>
    </div>
    <ul class="nav navbar-nav">
      <li class="active"><a href="#">Home</a></li>
      <li><a href="features.html">Features</a></li>
      <li><a href="jobentry.html">New Entry</a></li>
      <li><a href="logout.php">Log Out</a></li>
    </ul>
  </div>
</nav>   
   <?php
       session_start();
       $email = $_SESSION['email'];
       $id = $_SESSION['id'];
       if (!isset($email)) {
         header("Location: index.html");
         exit;
       }

       $link = mysqli_connect('localhost', 'redacted', 'redacted', 'catchit') or die('Could not connect: ' . mysql_error()); #username for mysql database, password for stated username (database named catchit)
       mysqli_select_db($link, 'catchit') or die('Could not select database');
       $stmt = mysqli_prepare($link, "SELECT Name FROM User WHERE Email = ?");
       mysqli_stmt_bind_param($stmt, "s", $email);
       mysqli_stmt_execute($stmt);
       mysqli_stmt_bind_result($stmt, $username);
       if(mysqli_stmt_fetch($stmt)){
         echo "<h1>Welcome, {$username}!</h1>";
       }
       mysqli_stmt_close($stmt);
    ?>
    <h3>Job Dashboard</h3>
 
    <table id="mainTable" class="table">
      <thead>
	<tr style="border-bottom: 0.5px solid #e2e2e2">
	  <td>Company</td>
	  <td>Job Title</td>
	  <td>Location</td>
	  <td>Deadline</td>
	  <td>Stage</td>
	  <td></td>
	  <td></td>
	</tr>	
      </thead>
      <tbody>
	<?php
	   $result = mysqli_prepare($link, "select g.JobID, g.Company, g.JobTitle, g.Location, g.Deadline, g.Details, s.Description
					    from JobPosting g, Stage s
					    where 1=1
					    and g.Stage=s.StageID
					    and g.UserID=?");
	   mysqli_stmt_bind_param($result, "s", $id);
	   mysqli_stmt_execute($result);
	   mysqli_stmt_bind_result($result, $jobid, $company, $jobtitle, $location, $deadline, $details, $stage);
	   $count = 0;
	   $arr = array();
	   while(mysqli_stmt_fetch($result)){
	     $count = $count + 1;
	     $arr[$count] = array($company, $jobtitle, $location, $deadline, $details, $stage, $id, $jobid);
	   }
	   mysqli_stmt_close($result);
	   for($i = 1; $i < $count + 1; $i++){
	   ?>
	   <tr data-toggle="collapse" data-target="#<?php echo $i?>" class="accordion-toggle" style="cursor: pointer; border-bottom: 0.5px solid #e2e2e2">
	     <td><?php echo $arr[$i][0]?></td>
	     <td><?php echo $arr[$i][1]?></td>
	     <td><?php echo $arr[$i][2]?></td>
	     <td><?php if ($arr[$i][3] == '0000-00-00') echo 'Not Specified'; else echo $arr[$i][3];?></td>
	     <td><?php echo $arr[$i][5]?></td>
	     
	     <td>
	       <form action="editjob.php" method="post">
		 <input type="submit" name="action" value="Edit" onclick="foo(event);"/>
                 <!-- <input type="submit" name="action" value="Delete" onclick="foo(event);"/> -->
                 <script>function ConfirmDelete() {return confirm("Are you sure you want to delete this job entry?");}</script>
                 <input type="submit" name="action" value="Delete" onclick="return ConfirmDelete();"/>
                 <input type="hidden" name="userid" value="<?php echo $arr[$i][6]?>"/>
		 <input type="hidden" name="jobid" value="<?php echo $arr[$i][7]?>"/>
	       </form>
	  </td>
	</tr>
	<tr>
	  <td colspan="7" style="padding: 0">
	    <div id="<?php echo $i?>" class="accordion-body collapse"> 
	      <div class="well" style="padding-top: 5px;">
		<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#<?php echo $i?>modal" style="margin-top: 5px;">View Correspondences</button>
		<form method="post" action="dashboard.php">
                <label for="noteBtnGrp">Add a new note or correspondence to this job:</label>
                <input type="hidden" name="jobid" value="<?php echo $arr[$i][7]?>"/>
               	<div class="btn-group-sm" id="noteBtnGrp">
                	<button type="submit" class="btn btn-default" formaction="jobdata/newnote.php">Note</button>
                       	<button type="submit" class="btn btn-default" formaction="jobdata/newemail.php">Email</button>
                       	<button type="submit" class="btn btn-default" formaction="jobdata/newinterview.php">Interview</button>
                       	<button type="submit" class="btn btn-default" formaction="jobdata/newnetworking.php">Networking Event</button>
                </div>
                </form>
		<p style="margin-top: 10px;"><b>Job Details:</b> <?php echo $arr[$i][4]?></p>
		<?php
		   $qry = mysqli_prepare($link, "select n.NoteID, n.Content, n.Date from Note n where n.JobID = ? and n.UserID = ?");
		   mysqli_stmt_bind_param($qry, "ss", $arr[$i][7], $arr[$i][6]);
		   mysqli_stmt_execute($qry);
		   mysqli_stmt_bind_result($qry, $nid, $ncontent, $ndate);
		   $ct = 0;
		   $notearr = array();
		   while(mysqli_stmt_fetch($qry)){
		     $notearr[$ct] = array($nid, $ncontent, $ndate);
		     $ct = $ct + 1;
		   }
		   mysqli_stmt_close($qry);
		?>
		<div class="modal fade" id="<?php echo $i?>modal" tabindex="-1" role="dialog" aria-labelledby="<?php echo $i?>modallabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Correspondences</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			</button>
		      </div>
		      <div class="modal-body">
		<?php
		   for($j = 0; $j < $ct; $j++){
		     if($j == 0){
		?>
		<p><b>Notes:</b></p>
		<?php } ?>
		<p>Last Edited: <?php echo $notearr[$j][2]?></p><p><?php echo $notearr[$j][1]?></p>
		<form action="jobdata/editnote.php" method="post">
		  <input type="submit" name="action" value="edit" onclick="foo(event);"/>
                  <script>function ConfirmDeleteNote() {return confirm("Are you sure you want to delete this note?");}</script>
                  <input type="submit" name="action" value="delete" onclick="return ConfirmDeleteNote();"/>
		  <input type="hidden" name="noteid" value="<?php echo $notearr[$j][0]?>"/>
		  <input type="hidden" name="userid" value="<?php echo $arr[$i][6]?>"/>
		  <input type="hidden" name="jobid" value="<?php echo $arr[$i][7]?>"/>
		</form>
		<?php }
		  $qry2 = mysqli_prepare($link, "select i.InterviewID, i.Date, i.Location, i.Notes from Interview i where i.JobID = ? and i.UserID = ?");
		  mysqli_stmt_bind_param($qry2, "ss", $arr[$i][7], $arr[$i][6]);
		  mysqli_stmt_execute($qry2);
		  mysqli_stmt_bind_result($qry2, $iid, $idate, $ilocation, $inotes);
		  $ct = 0;
		  $interviewarray = array();
		  while(mysqli_stmt_fetch($qry2)){
		      $interviewarray[$ct] = array($iid, $idate, $ilocation, $inotes);
		      $ct = $ct + 1;
		  }
		  mysqli_stmt_close($qry2);
		  for($j = 0; $j < $ct; $j++){
		      if($j == 0){
		?>
	        <hr><p><b>Interviews:</b></p> 
		<?php } ?>
                <p>Date: <?php echo $interviewarray[$j][1]?></p>
                <p>Location: <?php echo $interviewarray[$j][2]?></p>
                <p>Notes: <?php echo $interviewarray[$j][3]?></p>
		<form action="jobdata/editinterview.php" method="post">
                  <input type="submit" name="action" value="edit" onclick="foo(event);"/>
		  <script>function ConfirmDeleteInterview() {return confirm("Are you sure you want to delete this interview?");}</script>
                  <input type="submit" name="action" value="delete" onclick="return ConfirmDeleteInterview();"/>
                  <input type="hidden" name="iid" value="<?php echo $interviewarray[$j][0]?>"/>
		  <input type="hidden" name="userid" value="<?php echo $arr[$i][6]?>"/>
                  <input type="hidden" name="jobid" value="<?php echo $arr[$i][7]?>"/>
                </form>
		<?php
		   }
		   $qry3 = mysqli_prepare($link, "select e.EmailID, e.Recipient, e.Message, e.Date from Email e where e.JobID = ? and e.UserID = ?");
		   mysqli_stmt_bind_param($qry3, "ss", $arr[$i][7], $arr[$i][6]);
		   mysqli_stmt_execute($qry3);
		   mysqli_stmt_bind_result($qry3, $eid, $erecip, $emsg, $edate);
		   $ct = 0;
		   $emailarray = array();
		   while(mysqli_stmt_fetch($qry3)){
		     $emailarray[$ct] = array($eid,$erecip, $emsg, $edate);
		     $ct = $ct + 1;
		   }
		   mysqli_stmt_close($qry3);
		   for($j = 0; $j < $ct; $j++){
		     if($j == 0){
		   ?>
		   <hr><p><b>Emails:</b></p>
		   <?php } ?>
		   <p>Date: <?php echo $emailarray[$j][3]?></p>
		   <p>Recipient: <?php echo $emailarray[$j][1]?></p>
		   <p>Message: <?php echo $emailarray[$j][2]?></p>
		   <form action="jobdata/editemail.php" method="post">
                     <input type="submit" name="action" value="edit" onclick="foo(event);"/>
                     <script>function ConfirmDeleteEmail() {return confirm("Are you sure you want to delete this email?");}</script>
                     <input type="submit" name="action" value="delete" onclick="return ConfirmDeleteEmail();"/>
                     <input type="hidden" name="emailid" value="<?php echo $emailarray[$j][0]?>"/>
		     <input type="hidden" name="userid" value="<?php echo $arr[$i][6]?>"/>
                     <input type="hidden" name="jobid" value="<?php echo $arr[$i][7]?>"/>
                   </form>

		   <?php }
		     $qry4 = mysqli_prepare($link, "select n.EventID, n.EventName, n.ContactName, n.Details, n.Date from NetworkingEvent n where n.JobID = ? and n.UserID = ?");
		     mysqli_stmt_bind_param($qry4, "ss", $arr[$i][7], $arr[$i][6]);
		     mysqli_stmt_execute($qry4);
		     mysqli_stmt_bind_result($qry4, $nid, $nname, $ncontact, $ndetails, $ndate);
		     $ct = 0;
		     $networkarray = array();
		     while(mysqli_stmt_fetch($qry4)){
			 $networkarray[$ct] = array($nid, $nname, $ncontact, $ndetails, $ndate);
			 $ct = $ct + 1;
		     }
		     mysqli_stmt_close($qry4);
		     for($j = 0; $j < $ct; $j++){
			if($j == 0){
		   ?>
		   <hr><p><b>Events:</b></p>
		   <?php } ?>
		   <p>Event: <?php echo $networkarray[$j][1]?></p>
		   <p>Date: <?php echo $networkarray[$j][4]?></p>
		   <p>Contact: <?php echo $networkarray[$j][2]?></p>
		   <p>Details: <?php echo $networkarray[$j][3]?></p>
		   <form action="jobdata/editnetwork.php" method="post">
                     <input type="submit" name="action" value="edit" onclick="foo(event);"/>
                     <script>function ConfirmDeleteNetwork() {return confirm("Are you sure you want to delete this networking event?");}</script>
                     <input type="submit" name="action" value="delete" onclick="return ConfirmDeleteNetwork();"/>
                     <input type="hidden" name="eventid" value="<?php echo $networkarray[$j][0]?>"/>
		     <input type="hidden" name="userid" value="<?php echo $arr[$i][6]?>"/>
                     <input type="hidden" name="jobid" value="<?php echo $arr[$i][7]?>"/>
                   </form>

		   <?php } ?>
		   </div>
		   <div class="modal-footer">
		     <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		   </div>
		    </div>
		  </div>
		  </div>
	      </div>
	    </div>
	  </td>
	</tr>
	<?php } ?>
      </tbody>
    </table>
</script>
  </body>
  </div>
</html>
