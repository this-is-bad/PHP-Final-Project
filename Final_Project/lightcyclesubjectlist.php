<?php
// Start the session
session_start();
?>
<?php
include 'connect.php';
doDB();

//gather the subjects
$get_subjects_sql = "SELECT subject_id, subject_title, DATE_FORMAT(subject_create_time,  '%b %e %Y at %r') as fmt_subject_create_time, subject_owner FROM subjects ORDER BY subject_create_time DESC";
$get_subjects_res = mysqli_query($mysqli, $get_subjects_sql) or die(mysqli_error($mysqli));

if (mysqli_num_rows($get_subjects_res) < 1) {
	//inform the user when no subjects exist
	$display_block = "<p><em>No topics exist.</em></p>";
} else {
	//create the display string
    $display_block = <<<END_OF_TEXT
    <table>
    <tr>
    <th>TOPIC TITLE</th>
    <th># of POSTS</th>
    </tr>
END_OF_TEXT;

	while ($subject_info = mysqli_fetch_array($get_subjects_res)) {
		$subject_id = $subject_info['subject_id'];
		$subject_title = stripslashes($subject_info['subject_title']);
		$subject_create_time = $subject_info['fmt_subject_create_time'];
		$subject_owner = stripslashes($subject_info['subject_owner']);

		//get number of replies
		$get_num_replies_sql = "SELECT COUNT(subject_id) AS reply_count FROM replies WHERE subject_id = '".$subject_id."'";
		$get_num_replies_res = mysqli_query($mysqli, $get_num_replies_sql) or die(mysqli_error($mysqli));

		while ($replies_info = mysqli_fetch_array($get_num_replies_res)) {
			$num_replies = $replies_info['reply_count'];
		}

		//add to display
		$display_block .= <<<END_OF_TEXT
		<tr>
		<td><a href="lightcycleshowsubject.php?subject_id=$subject_id"><strong>$subject_title</strong></a><br/>
		Created on $subject_create_time by $subject_owner</td>
		<td class="num_replies_col">$num_replies</td>
		</tr>
END_OF_TEXT;
	}
	//free results
	mysqli_free_result($get_subjects_res);
	mysqli_free_result($get_num_replies_res);

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
<title>Topics in the light cycle forum</title>
<link href="css/lightcycle_discussion.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<section class="col-8 col-m-12">
		<h1>Topics in light cycle</h1>
		<?php echo $display_block; ?>
		<p>Would you like to <a href="lightcycleaddsubject.html">add a topic</a>?</p>
		<p>Would you like to <a href="lightcyclemenu.html">return to main</a>?</p>
	</section>
</body>
</html>
