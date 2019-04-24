<?php
include 'connect.php';
doDB();

//check for required info from the query string
if (!isset($_GET['subject_id'])) {
	header("Location: lightcyclesubjectlist.php");
	exit;
}

//create safe values for use
$safe_subject_id = mysqli_real_escape_string($mysqli, $_GET['subject_id']);

//verify the subject exists
$verify_subject_sql = "SELECT subject_title FROM subjects WHERE subject_id = '".$safe_subject_id."'";
$verify_subject_res =  mysqli_query($mysqli, $verify_subject_sql) or die(mysqli_error($mysqli));

if (mysqli_num_rows($verify_subject_res) < 1) {
	//this subject does not exist
	$display_block = "<p><em>You have selected an invalid topic.<br/>
	Please <a href=\"lightcyclesubjectlist.php\">try again</a>.</em></p>";
} else {
	//get the subject title
	while ($subject_info = mysqli_fetch_array($verify_subject_res)) {
		$subject_title = stripslashes($subject_info['subject_title']);
	}

	//gather the replies
	$get_replies_sql = "SELECT reply_id, reply_text, DATE_FORMAT(reply_create_time, '%b %e %Y<br/>%r') AS fmt_reply_create_time, reply_owner, reply_create_time FROM replies WHERE subject_id = '".$safe_subject_id."' ORDER BY reply_create_time ASC";
	$get_replies_res = mysqli_query($mysqli, $get_replies_sql) or die(mysqli_error($mysqli));

	//create the display string
	$display_block = <<<END_OF_TEXT
	<p>
		Showing replies to
		<br />
		<span class="yellowy"><strong>$subject_title</strong></span>
	</p>
	<table>
		<tr>
			<th>AUTHOR</th>
			<th>POST</th>
		</tr>
END_OF_TEXT;

	while ($replies_info = mysqli_fetch_array($get_replies_res)) {
		$reply_id = $replies_info['reply_id'];
		$reply_text = nl2br(stripslashes($replies_info['reply_text']));
		$reply_create_time = $replies_info['fmt_reply_create_time'];
		$reply_owner = stripslashes($replies_info['reply_owner']);

		//add to display
	 	$display_block .= <<<END_OF_TEXT
		<tr>
			<td>$reply_owner<br/><br/>created on:<br/>$reply_create_time</td>
			<td>$reply_text<br/><br/>
			<a href="lightcyclereplytoreply.php?reply_id=$reply_id"><strong>REPLY TO POST</strong></a></td>
		</tr>
END_OF_TEXT;
	}

	//free results
	mysqli_free_result($get_replies_res);
	mysqli_free_result($verify_subject_res);

	//close connection to MySQL
	mysqli_close($mysqli);

	//close up the table
	$display_block .= "</table>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8>
<title>Posts in Topic</title>
<link href="css/lightcycle_discussion.css" rel="stylesheet" type="text/css" />

</head>
<body>
	<h1 class="moreResponsiveH">Posts in Topic</h1>
	<?php echo $display_block; ?>
	<p>Would you like to <a href="lightcyclemenu.html">return to main menu</a>?</p>
</body>
</html>
