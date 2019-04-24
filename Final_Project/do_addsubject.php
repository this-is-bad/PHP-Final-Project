<?php
// Start the session
session_start();
?>
<?php
include 'connect.php';
doDB();

//check for required fields from the form
if ((!$_POST['subject_owner']) || (!$_POST['subject_title']) || (!$_POST['reply_text'])) {
	header("Location: lightcycleaddsubject.html");
	exit;
}

//create safe values for input into the database
$clean_subject_owner = mysqli_real_escape_string($mysqli, $_POST['subject_owner']);
$clean_subject_title = mysqli_real_escape_string($mysqli, $_POST['subject_title']);
$clean_reply_text = mysqli_real_escape_string($mysqli, $_POST['reply_text']);

//create and issue the first query
$add_subject_sql = "INSERT INTO subjects (subject_title, subject_create_time, subject_owner) SELECT subject_title, subject_create_time, subject_owner FROM (SELECT '".$clean_subject_title ."' AS subject_title, now() AS subject_create_time, '".$clean_subject_owner."' AS subject_owner) s WHERE NOT EXISTS (SELECT 1 FROM subjects WHERE subject_title = '".$clean_subject_title."' AND subject_owner = '".$clean_subject_owner."')";

$add_subject_res = mysqli_query($mysqli, $add_subject_sql) or die(mysqli_error($mysqli));

//get the id of the last query
$subject_id = mysqli_insert_id($mysqli);
$_SESSION["subject_id"]=$subject_id;
$_SESSION['subject_title']=$clean_subject_title;
$_SESSION['reply_text']=$clean_reply_text;

//create and issue the second query
if (isset($_SESSION['reply_id']))
{
	$add_reply_sql = "INSERT INTO replies (subject_id, reply_text, reply_create_time, reply_owner) SELECT subject_id, reply_text, reply_create_time, reply_owner FROM (SELECT '".$subject_id."' AS subject_id, '".$clean_reply_text."' AS reply_text,  now() AS reply_create_time, '".$clean_subject_owner."' AS reply_owner) r WHERE NOT EXISTS (SELECT 1 FROM replies WHERE subject_id = '".$subject_id."' AND reply_text = '".$clean_reply_text."' AND reply_owner = '".$clean_subject_owner."' AND reply_id = '".$_SESSION["reply_id"]."') ";
} else {
	$add_reply_sql = "INSERT INTO replies (subject_id, reply_text, reply_create_time, reply_owner) VALUES ('".$subject_id."', '".$clean_reply_text."',  now(), '".$clean_subject_owner."')";	
}


$add_reply_res = mysqli_query($mysqli, $add_reply_sql) or die(mysqli_error($mysqli));

//get the id of the last query
$reply_id = mysqli_insert_id($mysqli);
$_SESSION["reply_id"]=$reply_id;

//close connection to MySQL
mysqli_close($mysqli);

//create nice message for user
$display_block = "<p>The <strong>".$_POST["subject_title"]."</strong> subject has been created.</p>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8>
<title>New Subject Added</title>
<link href="css/lightcycle_discussion.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<h1>New Subject Added</h1>
	<?php echo $display_block; ?>
	<form class="col-8 col-m-12">
		<input type="button" name="menu" id="menu" value="Return to Menu" onclick="location.href='lightcyclemenu.html'">
		<input type="button" name="edit" id="edit" value="Edit Reply" onclick="location.href='lightcycleeditreply.php'">
		<input type="button" name="delete" id="delete" value="Delete Reply" onclick="location.href='lightcycledeletereply.php'">
	</form>
</body>
</html>
